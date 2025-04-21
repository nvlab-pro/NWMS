<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderOffer",
 *     type="object",
 *     required={"oo_order_id", "oo_offer_id", "oo_qty", "oo_price"},
 *     @OA\Property(property="oo_id", type="integer", description="Primary key"),
 *     @OA\Property(property="oo_order_id", type="integer", description="Order ID"),
 *     @OA\Property(property="oo_offer_id", type="integer", description="Offer ID"),
 *     @OA\Property(property="oo_qty", type="number", format="float", description="Quantity of the offer in the order"),
 *     @OA\Property(property="oo_oc_price", type="number", format="float", description="Original cost price of the offer"),
 *     @OA\Property(property="oo_price", type="number", format="float", description="Price at which the offer is sold"),
 *     @OA\Property(property="oo_expiration_date", type="string", format="date", description="Expiration date of the product, if applicable"),
 *     @OA\Property(property="oo_batch", type="string", maxLength=15, description="Batch number of the product"),
 *     @OA\Property(property="oo_operation_user_id", type="integer", description="User ID of the operator who handled this offer"),
 *     @OA\Property(
 *         property="offer",
 *         type="object",
 *         @OA\Property(property="of_id", type="integer", description="Product ID"),
 *         @OA\Property(property="of_name", type="string", description="Product name"),
 *         @OA\Property(property="of_article", type="string", description="Product article number"),
 *         @OA\Property(property="of_sku", type="string", description="SKU code"),
 *         @OA\Property(property="of_price", type="number", format="float", description="Base price of the product"),
 *         @OA\Property(property="of_img", type="string", description="URL of product image")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="OrderWithOffers",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Order"),
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="offers",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/OrderOffer")
 *             )
 *         )
 *     }
 * )
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'o_id' => $this->o_id,
            'o_domain_id' => $this->o_domain_id,
            'o_status_id' => $this->o_status_id,
            'o_parcel_id' => $this->o_parcel_id,
            'o_type_id' => $this->o_type_id,
            'o_user_id' => $this->o_user_id,
            'o_ext_id' => $this->o_ext_id,
            'o_shop_id' => $this->o_shop_id,
            'o_wh_id' => $this->o_wh_id,
            'o_date' => $this->o_date,
            'o_date_send' => $this->o_date_send,
            'o_source_id' => $this->o_source_id,
            'o_count' => $this->o_count,
            'o_sum' => $this->o_sum,
            'o_operation_user_id' => $this->o_operation_user_id,
            'o_order_place' => $this->o_order_place,
            'o_current_pallet' => $this->o_current_pallet,
            'o_current_box' => $this->o_current_box,
            'offers' => OrderOfferResource::collection($this->whenLoaded('offers')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
