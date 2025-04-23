<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Acceptance",
 *     type="object",
 *     required={"acc_wh_id", "acc_shop_id", "acc_user_id", "acc_type", "acc_status"},
 *     @OA\Property(property="acc_id", type="integer", description="Primary key"),
 *     @OA\Property(property="acc_domain_id", type="integer", description="Domain ID"),
 *     @OA\Property(property="acc_status", type="integer", description="Status ID"),
 *     @OA\Property(property="acc_ext_id", type="integer", description="External ID"),
 *     @OA\Property(property="acc_user_id", type="integer", description="User ID"),
 *     @OA\Property(property="acc_wh_id", type="integer", description="Warehouse ID"),
 *     @OA\Property(property="acc_shop_id", type="integer", description="Shop ID"),
 *     @OA\Property(property="acc_type", type="integer", description="Acceptance type"),
 *     @OA\Property(property="acc_comment", type="string", description="Comment"),
 *     @OA\Property(property="acc_count_expected", type="number", format="float", description="Total expected quantity"),
 *     @OA\Property(property="acc_count_accepted", type="number", format="float", description="Total accepted quantity"),
 *     @OA\Property(property="acc_count_placed", type="number", format="float", description="Total placed quantity"),
 *     @OA\Property(property="acc_date", type="string", format="date-time", description="Acceptance creation date"),
 *     @OA\Property(
 *         property="offers",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AcceptanceOffer")
 *     )
 * )
 */
class AcceptanceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'acc_id' => $this->acc_id,
            'acc_domain_id' => $this->acc_domain_id,
            'acc_status' => $this->acc_status,
            'acc_ext_id' => $this->acc_ext_id,
            'acc_user_id' => $this->acc_user_id,
            'acc_wh_id' => $this->acc_wh_id,
            'acc_shop_id' => $this->acc_shop_id,
            'acc_type' => $this->acc_type,
            'acc_comment' => $this->acc_comment,
            'acc_count_expected' => $this->acc_count_expected,
            'acc_count_accepted' => $this->acc_count_accepted,
            'acc_count_placed' => $this->acc_count_placed,
            'acc_date' => $this->acc_date,
            'offers' => AcceptanceOfferResource::collection($this->whenLoaded('offers')),
        ];
    }
}