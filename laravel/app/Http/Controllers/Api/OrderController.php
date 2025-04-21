<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\rwOrder;
use App\Models\rwOrderOffer;
use App\Orchid\Services\OrderService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Orders",
 *     description="Order management endpoints"
 * )
 *
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     required={"o_status_id", "o_type_id", "o_user_id", "o_shop_id", "o_wh_id", "o_date", "o_count", "o_sum"},
 *     @OA\Property(property="o_status_id", type="integer"),
 *     @OA\Property(property="o_type_id", type="integer"),
 *     @OA\Property(property="o_user_id", type="integer"),
 *     @OA\Property(property="o_ext_id", type="string", maxLength=30),
 *     @OA\Property(property="o_shop_id", type="integer"),
 *     @OA\Property(property="o_wh_id", type="integer"),
 *     @OA\Property(property="o_date", type="string", format="date-time"),
 *     @OA\Property(property="o_date_send", type="string", format="date"),
 *     @OA\Property(property="o_source_id", type="integer"),
 *     @OA\Property(property="o_count", type="number"),
 *     @OA\Property(property="o_sum", type="number"),
 *     @OA\Property(property="o_operation_user_id", type="integer"),
 *     @OA\Property(property="o_order_place", type="integer"),
 *     @OA\Property(property="o_current_pallet", type="integer"),
 *     @OA\Property(property="o_current_box", type="integer")
 * )
 */
class OrderController extends Controller
{
    private function applyAccessFilters($query, $user)
    {
        if (!$user->hasRole('admin')) {
            if ($user->hasRole('warehouse_manager')) {
                $query->where('o_domain_id', $user->domain_id);
            } else {
                $query->where('o_domain_id', $user->domain_id)
                    ->whereHas('getShop', function ($q) use ($user) {
                        $q->whereIn('sh_user_id', [$user->id, $user->parent_id]);
                    });
            }
        }

        return $query;
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get list of orders",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of orders")
     * )
     */
    public function index(Request $request)
    {
        $query = rwOrder::with('offers', 'offers.offer');

        $this->applyAccessFilters($query, $request->user());

        $orders = $query->paginate((int)$request->get('per_page', 20));

        return OrderResource::collection($orders);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get order by ID",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order found"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
    public function show($id, Request $request)
    {
        $order = rwOrder::where('o_id', $id);
        $this->applyAccessFilters($order, $request->user());
        return new OrderResource($order->firstOrFail());
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=201, description="Order created successfully")
     * )
     */
    public function insert(Request $request)
    {
        $data = $request->validate([
            'o_type_id' => 'required|integer',
            'o_ext_id' => 'nullable|string|max:30',
            'o_shop_id' => 'required|integer',
            'o_wh_id' => 'required|integer',
            'o_date_send' => 'nullable|date',
            'o_source_id' => 'nullable|integer',
        ]);

        $data += [
            'o_domain_id' => $request->user()->domain_id,
            'o_user_id' => $request->user()->id,
            'o_operation_user_id' => $request->user()->id,
            'o_status_id' => 10,
            'o_date' => now(),
            'o_count' => 0,
            'o_sum' => 0,
            'o_order_place' => null,
            'o_current_pallet' => 1,
            'o_current_box' => 1,
        ];

        return new OrderResource(rwOrder::create($data));
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update an order",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $order = rwOrder::where('o_id', $id);
        $this->applyAccessFilters($order, $request->user());
        $order = $order->firstOrFail();
        $order->update($request->all());
        return new OrderResource($order);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete an order",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Order deleted")
     * )
     */
    public function destroy($id, Request $request)
    {
        $order = rwOrder::where('o_id', $id);
        $this->applyAccessFilters($order, $request->user());
        $order = $order->firstOrFail();
        $order->delete();
        return response()->json(['message' => 'Deleted']);
    }

    /**
     * @OA\Post(
     *     path="/api/orders/{id}/offers",
     *     summary="Add offers to an order",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/OrderOffer")
     *         )
     *     ),
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=201, description="Offers added to order")
     * )
     */
    public function addOffer(Request $request, $id)
    {
        $order = rwOrder::where('o_id', $id);
        $this->applyAccessFilters($order, $request->user());
        $order = $order->firstOrFail();

        $validatedOffers = $request->validate([
            '*.oo_offer_id' => 'required|integer|exists:rw_offers,of_id',
            '*.oo_qty' => 'required|numeric|min:0.01',
            '*.oo_oc_price' => 'nullable|numeric',
            '*.oo_price' => 'required|numeric|min:0',
            '*.oo_expiration_date' => 'nullable|date',
            '*.oo_batch' => 'nullable|string|max:15',
            '*.oo_operation_user_id' => 'nullable|integer',
        ]);

        $results = [];

        foreach ($validatedOffers as $offerData) {
            $offerData['oo_order_id'] = $order->o_id;
            $results[] = rwOrderOffer::create($offerData);
        }

        (new OrderService($order->o_id))->recalcOrderRest();

        return response()->json($results, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{order_id}/offers/{offer_id}",
     *     summary="Update an offer in order",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/OrderOffer")),
     *     @OA\Parameter(name="order_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="offer_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Offer updated")
     * )
     */
    public function updateOffer(Request $request, $order_id, $offer_id)
    {
        $order = rwOrder::where('o_id', $order_id);
        $this->applyAccessFilters($order, $request->user());
        $order = $order->firstOrFail();

        $offer = rwOrderOffer::where('oo_order_id', $order->o_id)
            ->where('oo_id', $offer_id)
            ->first();

        if (!$offer) {
            return response()->json(['error' => 'Offer not found in this order'], 404);
        }

        $data = $request->validate([
            'oo_qty' => 'sometimes|numeric|min:0.01',
            'oo_oc_price' => 'sometimes|numeric',
            'oo_price' => 'sometimes|numeric|min:0',
            'oo_expiration_date' => 'nullable|date',
            'oo_batch' => 'nullable|string|max:15',
            'oo_operation_user_id' => 'nullable|integer',
        ]);

        $offer->update($data);
        (new OrderService($order->o_id))->recalcOrderRest();

        return response()->json($offer);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{order_id}/offers/{offer_id}",
     *     summary="Delete an offer from an order",
     *     tags={"Orders"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="order_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="offer_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Offer removed from order")
     * )
     */
    public function deleteOffer(Request $request, $order_id, $offer_id)
    {
        $order = rwOrder::where('o_id', $order_id);
        $this->applyAccessFilters($order, $request->user());
        $order = $order->firstOrFail();

        $offer = rwOrderOffer::where('oo_order_id', $order->o_id)
            ->where('oo_id', $offer_id)
            ->first();

        if (!$offer) {
            return response()->json(['error' => 'Offer not found in this order'], 404);
        }

        $offer->delete();
        (new OrderService($order->o_id))->recalcOrderRest();

        return response()->json(['message' => 'Offer removed from order']);
    }
}
