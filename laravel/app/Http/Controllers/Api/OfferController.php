<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\rwOffer;
use App\Models\rwShop;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Product management endpoints"
 * )
 *
 * @OA\RequestBody(
 *     request="Product",
 *     required=true,
 *     @OA\JsonContent(
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Product")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"of_name", "of_shop_id"},
 *     @OA\Property(property="of_ext_id", type="string", maxLength=25, description="External product ID from external system"),
 *     @OA\Property(property="of_shop_id", type="integer", description="Shop ID"),
 *     @OA\Property(property="of_name", type="string", maxLength=150, description="Product name"),
 *     @OA\Property(property="of_article", type="string", maxLength=25, description="Product article number"),
 *     @OA\Property(property="of_sku", type="string", maxLength=25, description="SKU code"),
 *     @OA\Property(property="of_price", type="number", format="float", description="Product base price"),
 *     @OA\Property(property="of_estimated_price", type="number", format="float", description="Estimated product price"),
 *     @OA\Property(property="of_img", type="string", maxLength=255, description="Image URL"),
 *     @OA\Property(property="of_dimension_x", type="number", format="float", description="Length (cm)"),
 *     @OA\Property(property="of_dimension_y", type="number", format="float", description="Width (cm)"),
 *     @OA\Property(property="of_dimension_z", type="number", format="float", description="Height (cm)"),
 *     @OA\Property(property="of_weight", type="integer", description="Weight (grams)"),
 *     @OA\Property(property="of_datamatrix", type="integer", description="Requires DataMatrix (0 or 1)"),
 *     @OA\Property(property="of_comment", type="string", maxLength=255, description="Comment or note")
 * )
 */
class OfferController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get list of products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="of_sku", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="of_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="of_name", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="of_status", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort_by", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_dir", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index(Request $request)
    {
        $query = rwOffer::query();

        $query->when($request->filled('of_sku'), fn($q) => $q->where('of_sku', $request->of_sku))
            ->when($request->filled('of_id'), fn($q) => $q->where('of_id', $request->of_id))
            ->when($request->filled('of_name'), fn($q) => $q->where('of_name', 'like', "%{$request->of_name}%"))
            ->when($request->filled('of_status'), fn($q) => $q->where('of_status', $request->of_status));

        $currentUser = $request->user();

        if (!$currentUser->hasRole('admin')) {
            $query->where('of_domain_id', $currentUser->domain_id);

            if (!$currentUser->hasRole('warehouse_manager')) {
                $query->whereHas('getShop', fn($q) =>
                $q->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id])
                );
            }
        }

        if ($request->has('sort_by')) {
            $query->orderBy($request->get('sort_by'), $request->get('sort_dir', 'asc'));
        }

        return OfferResource::collection($query->paginate((int)$request->get('per_page', 20)));
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create new products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(ref="#/components/requestBodies/Product"),
     *     @OA\Response(response=201, description="Products created successfully")
     * )
     */
    public function insert(Request $request)
    {
        $offersData = $request->validate([
            '*.of_ext_id' => 'nullable|string|max:25',
            '*.of_shop_id' => 'required|integer',
            '*.of_name' => 'required|string|max:150',
            '*.of_article' => 'nullable|string|max:25',
            '*.of_sku' => 'nullable|string|max:25',
            '*.of_price' => 'nullable|numeric',
            '*.of_estimated_price' => 'nullable|numeric',
            '*.of_img' => 'nullable|string|max:255',
            '*.of_dimension_x' => 'nullable|numeric',
            '*.of_dimension_y' => 'nullable|numeric',
            '*.of_dimension_z' => 'nullable|numeric',
            '*.of_weight' => 'nullable|integer',
            '*.of_datamatrix' => 'nullable|integer',
            '*.of_comment' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $createdOffers = [];

        foreach ($offersData as $data) {
            $data['of_domain_id'] = $user->domain_id;
            $data['of_status'] = 1;

            $hasAccess = match (true) {
                $user->hasRole('admin') => true,
                $user->hasRole('warehouse_manager') => rwShop::where('sh_domain_id', $user->domain_id)
                    ->where('sh_id', $data['of_shop_id'])->exists(),
                default => rwShop::where('sh_domain_id', $user->domain_id)
                    ->whereIn('sh_user_id', [$user->id, $user->parent_id])
                    ->where('sh_id', $data['of_shop_id'])->exists(),
            };

            if (!$hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => "You do not have sufficient rights to create a product in shop ID {$data['of_shop_id']}"
                ], 401);
            }

            $createdOffers[] = new OfferResource(rwOffer::create($data));
        }

        return response()->json($createdOffers, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get product by ID",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product found"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show($id, Request $request)
    {
        $offer = rwOffer::where('of_id', $id)
            ->where('of_domain_id', $request->user()->domain_id)
            ->where('of_shop_id', $request->user()->shop_id)
            ->firstOrFail();

        return new OfferResource($offer);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(ref="#/components/requestBodies/Product"),
     *     @OA\Response(response=200, description="Product updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $offer = rwOffer::where('of_id', $id)
            ->where('of_domain_id', $request->user()->domain_id)
            ->where('of_shop_id', $request->user()->shop_id)
            ->firstOrFail();

        $offer->update($request->all());

        return new OfferResource($offer);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted successfully"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy($id, Request $request)
    {
        $offer = rwOffer::where('of_id', $id)
            ->where('of_domain_id', $request->user()->domain_id)
            ->where('of_shop_id', $request->user()->shop_id)
            ->firstOrFail();

        $offer->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

