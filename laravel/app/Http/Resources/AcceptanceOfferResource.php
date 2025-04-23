<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AcceptanceOffer",
 *     type="object",
 *     required={"ao_acceptance_id", "ao_offer_id", "ao_expected"},
 *     @OA\Property(property="ao_id", type="integer", description="Primary key"),
 *     @OA\Property(property="ao_acceptance_id", type="integer", description="Acceptance ID"),
 *     @OA\Property(property="ao_offer_id", type="integer", description="Offer ID"),
 *     @OA\Property(property="ao_expected", type="number", format="float", description="Expected quantity"),
 *     @OA\Property(property="ao_accepted", type="number", format="float", description="Accepted quantity"),
 *     @OA\Property(property="ao_placed", type="number", format="float", description="Placed quantity"),
 *     @OA\Property(property="ao_price", type="number", format="float", description="Purchase price"),
 *     @OA\Property(property="ao_batch", type="string", description="Batch number"),
 *     @OA\Property(property="ao_expiration_date", type="string", format="date", description="Expiration date"),
 *     @OA\Property(property="ao_barcode", type="string", description="Barcode"),
 *     @OA\Property(
 *         property="offer",
 *         type="object",
 *         @OA\Property(property="of_id", type="integer", description="Product ID"),
 *         @OA\Property(property="of_name", type="string", description="Product name"),
 *         @OA\Property(property="of_article", type="string", description="Product article number"),
 *         @OA\Property(property="of_img", type="string", description="Image URL")
 *     )
 * )
 *
 * @OA\Schema(
 *      schema="AcceptanceOfferInput",
 *      type="object",
 *      required={"ao_offer_id", "ao_expected"},
 *      @OA\Property(property="ao_offer_id", type="integer", description="ID of the offer"),
 *      @OA\Property(property="ao_expected", type="number", format="float", description="Expected quantity"),
 *      @OA\Property(property="ao_price", type="number", format="float", description="Purchase price"),
 *      @OA\Property(property="ao_batch", type="string", maxLength=30, description="Batch number"),
 *      @OA\Property(property="ao_expiration_date", type="string", format="date", description="Expiration date"),
 *      @OA\Property(property="ao_barcode", type="string", maxLength=64, description="Barcode")
 * )
 */
class AcceptanceOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'ao_id' => $this->ao_id,
            'ao_acceptance_id' => $this->ao_acceptance_id,
            'ao_offer_id' => $this->ao_offer_id,
            'ao_expected' => $this->ao_expected,
            'ao_accepted' => $this->ao_accepted,
            'ao_placed' => $this->ao_placed,
            'ao_price' => $this->ao_price,
            'ao_batch' => $this->ao_batch,
            'ao_expiration_date' => $this->ao_expiration_date,
            'ao_barcode' => $this->ao_barcode,
            'offer' => $this->whenLoaded('getOffers', function () {
                return [
                    'of_id' => $this->getOffers->of_id ?? null,
                    'of_name' => $this->getOffers->of_name ?? null,
                    'of_article' => $this->getOffers->of_article ?? null,
                    'of_img' => $this->getOffers->of_img ?? null,
                ];
            })
        ];
    }
}