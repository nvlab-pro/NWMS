<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'of_id' => $this->of_id,
            'of_ext_id' => $this->of_ext_id,
            'of_name' => $this->of_name,
            'of_sku' => $this->of_sku,
            'of_article' => $this->of_article,
            'of_price' => $this->of_price,
            'of_status' => $this->of_status,
            'of_shop_id' => $this->of_shop_id,
            'of_domain_id' => $this->of_domain_id,
            'of_dimensions' => [
                'x' => $this->of_dimension_x,
                'y' => $this->of_dimension_y,
                'z' => $this->of_dimension_z,
            ],
            'of_weight' => $this->of_weight,
            'of_image' => $this->of_img,
            'of_datamatrix' => $this->of_datamatrix,
            'of_comment' => $this->of_comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
