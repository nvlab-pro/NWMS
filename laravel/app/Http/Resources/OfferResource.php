<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->of_id,
            'name' => $this->of_name,
            'sku' => $this->of_sku,
            'article' => $this->of_article,
            'price' => $this->of_price,
            'status' => $this->of_status,
            'shop_id' => $this->of_shop_id,
            'domain_id' => $this->of_domain_id,
            'dimensions' => [
                'x' => $this->of_dimension_x,
                'y' => $this->of_dimension_y,
                'z' => $this->of_dimension_z,
            ],
            'weight' => $this->of_weight,
            'image' => $this->of_img,
            'datamatrix' => $this->of_datamatrix,
            'comment' => $this->of_comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
