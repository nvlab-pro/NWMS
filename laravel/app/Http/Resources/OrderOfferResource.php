<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderOfferResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'oo_id' => $this->oo_id,
            'oo_order_id' => $this->oo_order_id,
            'oo_offer_id' => $this->oo_offer_id,
            'oo_qty' => $this->oo_qty,
            'oo_oc_price' => $this->oo_oc_price,
            'oo_price' => $this->oo_price,
            'oo_expiration_date' => $this->oo_expiration_date,
            'oo_batch' => $this->oo_batch,
            'oo_operation_user_id' => $this->oo_operation_user_id,
            'offer' => $this->whenLoaded('offer', function () {
                return [
                    'of_id' => $this->offer->of_id,
                    'of_name' => $this->offer->of_name,
                    'of_article' => $this->offer->of_article,
                    'of_sku' => $this->offer->of_sku,
                    'of_price' => $this->offer->of_price,
                    'of_img' => $this->offer->of_img,
                ];
            }),
        ];
    }
}
