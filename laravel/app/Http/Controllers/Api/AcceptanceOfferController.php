<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\{AcceptanceOfferResource, AcceptanceResource};
use App\Models\{rwAcceptance, rwAcceptanceOffer};
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AcceptanceOfferController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/acceptances/{id}/offers",
     *     summary="Add offers to an acceptance",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/AcceptanceOfferInput"))
     *     ),
     *     @OA\Response(response=201, description="Offers added successfully")
     * )
     */
    public function add(Request $request, $id)
    {
        $acceptance = rwAcceptance::where('acc_id', $id)
            ->where('acc_domain_id', $request->user()->domain_id)
            ->firstOrFail();

        $data = $request->validate([
            '*.ao_offer_id' => 'required|integer|exists:rw_offers,of_id',
            '*.ao_expected' => 'required|numeric|min:0.01',
            '*.ao_price' => 'nullable|numeric',
            '*.ao_batch' => 'nullable|string|max:50',
            '*.ao_expiration_date' => 'nullable|date',
            '*.ao_barcode' => 'nullable|string|max:100'
        ]);

        $results = [];
        foreach ($data as $item) {
            $item['ao_acceptance_id'] = $acceptance->acc_id;
            $results[] = new AcceptanceOfferResource(rwAcceptanceOffer::create($item));
        }

        return response()->json($results, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/acceptances/{acc_id}/offers/{ao_id}",
     *     summary="Update an offer in an acceptance",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="acc_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="ao_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AcceptanceOfferInput")),
     *     @OA\Response(response=200, description="Offer updated successfully")
     * )
     */
    public function update(Request $request, $acc_id, $ao_id)
    {
        $acceptance = rwAcceptance::where('acc_id', $acc_id)
            ->where('acc_domain_id', $request->user()->domain_id)
            ->firstOrFail();

        $offer = rwAcceptanceOffer::where('ao_acceptance_id', $acc_id)
            ->where('ao_id', $ao_id)
            ->firstOrFail();

        $data = $request->validate([
            'ao_expected' => 'sometimes|numeric|min:0.01',
            'ao_price' => 'nullable|numeric',
            'ao_batch' => 'nullable|string|max:50',
            'ao_expiration_date' => 'nullable|date',
            'ao_barcode' => 'nullable|string|max:100'
        ]);

        $offer->update($data);
        return new AcceptanceOfferResource($offer);
    }

    /**
     * @OA\Delete(
     *     path="/api/acceptances/{acc_id}/offers/{ao_id}",
     *     summary="Delete an offer from an acceptance",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="acc_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="ao_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Offer deleted successfully")
     * )
     */
    public function delete(Request $request, $acc_id, $ao_id)
    {
        $acceptance = rwAcceptance::where('acc_id', $acc_id)
            ->where('acc_domain_id', $request->user()->domain_id)
            ->firstOrFail();

        $offer = rwAcceptanceOffer::where('ao_acceptance_id', $acc_id)
            ->where('ao_id', $ao_id)
            ->firstOrFail();

        $offer->delete();
        return response()->json(['message' => 'Deleted']);
    }
}