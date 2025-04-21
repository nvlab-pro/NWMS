<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OfferResource;
use App\Models\rwOffer;
use App\Models\rwShop;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="NWMS API",
 *     description="This API uses Bearer Token authentication.
 *     To authorize, use the 'Authorize' button above and enter a token in the format:
 *     Bearer {your_token}"
 * )
 *
 * @OA\ExternalDocumentation(
 *     description="The authorization principle is described in detail here",
 *     url="https://nwms.cloud/docs/api"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\RequestBody(
 *     request="Product",
 *     required=true,
 *     @OA\JsonContent(
 *         required={"of_name", "of_status", "of_shop_id"},
 *         @OA\Property(property="of_ext_id", type="string", maxLength=25),
 *         @OA\Property(property="of_status", type="integer"),
 *         @OA\Property(property="of_shop_id", type="integer"),
 *         @OA\Property(property="of_name", type="string", maxLength=150),
 *         @OA\Property(property="of_article", type="string", maxLength=25),
 *         @OA\Property(property="of_sku", type="string", maxLength=25),
 *         @OA\Property(property="of_price", type="number", format="float"),
 *         @OA\Property(property="of_estimated_price", type="number", format="float"),
 *         @OA\Property(property="of_img", type="string", maxLength=255),
 *         @OA\Property(property="of_dimension_x", type="number", format="float"),
 *         @OA\Property(property="of_dimension_y", type="number", format="float"),
 *         @OA\Property(property="of_dimension_z", type="number", format="float"),
 *         @OA\Property(property="of_weight", type="integer"),
 *         @OA\Property(property="of_datamatrix", type="integer"),
 *         @OA\Property(property="of_comment", type="string", maxLength=255)
 *     )
 * )
 * @OA\Schema(
 * schema="Product",
 * type="object",
 * required={"of_name", "of_status", "of_shop_id"},
 * @OA\Property(property="of_ext_id", type="string", maxLength=25, description="External product ID from an external system"),
 * @OA\Property(property="of_status", type="integer", description="Product status (e.g., 1 - active, 0 - disabled)"),
 * @OA\Property(property="of_shop_id", type="integer", description="ID of the shop this product belongs to"),
 * @OA\Property(property="of_name", type="string", maxLength=150, description="Product name"),
 * @OA\Property(property="of_article", type="string", maxLength=25, description="Product article number"),
 * @OA\Property(property="of_sku", type="string", maxLength=25, description="Stock Keeping Unit (SKU)"),
 * @OA\Property(property="of_price", type="number", format="float", description="Product price"),
 * @OA\Property(property="of_estimated_price", type="number", format="float", description="Estimated price (for reference)"),
 * @OA\Property(property="of_img", type="string", maxLength=255, description="Product image URL"),
 * @OA\Property(property="of_dimension_x", type="number", format="float", description="Product length in cm"),
 * @OA\Property(property="of_dimension_y", type="number", format="float", description="Product width in cm"),
 * @OA\Property(property="of_dimension_z", type="number", format="float", description="Product height in cm"),
 * @OA\Property(property="of_weight", type="integer", description="Product weight in grams"),
 * @OA\Property(property="of_datamatrix", type="integer", description="Whether the product requires a DataMatrix code (0 or 1)"),
 * @OA\Property(property="of_comment", type="string", maxLength=255, description="Additional comment or note about the product")
 * )
 * /
 */
class OfferController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get list of products",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="sku", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="sort_by", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort_dir", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of products")
     * )
     */
    public function index(Request $request)
    {
        $query = rwOffer::query();

        if ($request->has('sku')) {
            $query->where('of_sku', $request->sku);
        }
        if ($request->has('id')) {
            $query->where('of_id', $request->id);
        }
        if ($request->has('name')) {
            $query->where('of_name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('status')) {
            $query->where('of_status', $request->status);
        }

        $currentUser = $request->user();

        if (!$currentUser->hasRole('admin')) {
            if ($currentUser->hasRole('warehouse_manager')) {
                $query->where('of_domain_id', $currentUser->domain_id);
            } else {
                $query->where('of_domain_id', $currentUser->domain_id)
                    ->whereHas('getShop', function ($query) use ($currentUser) {
                        $query->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id]);
                    });
            }
        }

        if ($request->has('sort_by')) {
            $direction = $request->get('sort_dir', 'asc');
            $query->orderBy($request->sort_by, $direction);
        }

        $perPage = (int)$request->get('per_page', 20);
        $offers = $query->paginate($perPage);

        return OfferResource::collection($offers);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"of_name", "of_status", "of_shop_id"},
     *             @OA\Property(property="of_ext_id", type="string", maxLength=25),
     *             @OA\Property(property="of_status", type="integer"),
     *             @OA\Property(property="of_shop_id", type="integer"),
     *             @OA\Property(property="of_name", type="string", maxLength=150),
     *             @OA\Property(property="of_article", type="string", maxLength=25),
     *             @OA\Property(property="of_sku", type="string", maxLength=25),
     *             @OA\Property(property="of_price", type="number", format="float"),
     *             @OA\Property(property="of_estimated_price", type="number", format="float"),
     *             @OA\Property(property="of_img", type="string", maxLength=255),
     *             @OA\Property(property="of_dimension_x", type="number", format="float"),
     *             @OA\Property(property="of_dimension_y", type="number", format="float"),
     *             @OA\Property(property="of_dimension_z", type="number", format="float"),
     *             @OA\Property(property="of_weight", type="integer"),
     *             @OA\Property(property="of_datamatrix", type="integer"),
     *             @OA\Property(property="of_comment", type="string", maxLength=255)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created successfully")
     * )
     */
    public function insert(Request $request)
    {
        $data = $request->validate([
            'of_ext_id' => 'nullable|string|max:25',
            'of_shop_id' => 'required|integer',
            'of_name' => 'required|string|max:150',
            'of_article' => 'nullable|string|max:25',
            'of_sku' => 'nullable|string|max:25',
            'of_price' => 'nullable|numeric',
            'of_estimated_price' => 'nullable|numeric',
            'of_img' => 'nullable|string|max:255',
            'of_dimension_x' => 'nullable|numeric',
            'of_dimension_y' => 'nullable|numeric',
            'of_dimension_z' => 'nullable|numeric',
            'of_weight' => 'nullable|integer',
            'of_datamatrix' => 'nullable|integer',
            'of_comment' => 'nullable|string|max:255',
        ]);

        $access = true;

        $currentUser = $request->user();
        $data['of_domain_id'] = $currentUser->domain_id;
        $data['of_status'] = 1;

        if ($currentUser->hasRole('warehouse_manager')) {
            $access = rwShop::where('sh_domain_id', $currentUser->domain_id)
                ->where('sh_id', $data['of_shop_id'])
                ->exists();
        }

        if (!$currentUser->hasRole('admin') && !$currentUser->hasRole('warehouse_manager')) {
            $access = rwShop::where('sh_domain_id', $currentUser->domain_id)
                ->whereIn('sh_user_id', [$currentUser->id, $currentUser->parent_id])
                ->where('sh_id', $data['of_shop_id'])
                ->exists();
        }

        if ($access) {
            $offer = rwOffer::create($data);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "You do not have sufficient rights to create a product"
            ], 401);
        }

        return new OfferResource($offer);
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
     *     summary="Update an existing product",
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
     *     @OA\Response(response=200, description="Product deleted")
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