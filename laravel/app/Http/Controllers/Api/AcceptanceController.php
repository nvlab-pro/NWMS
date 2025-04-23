<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\{AcceptanceResource, AcceptanceOfferResource};
use App\Models\{rwAcceptance, rwAcceptanceOffer};
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AcceptanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/acceptances",
     *     summary="Get list of acceptances",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="List of acceptances")
     * )
     */
    public function index(Request $request)
    {
        $query = rwAcceptance::with([
            'getAccStatus',
            'getAccType',
            'getWarehouse',
            'getUser',
            'offers' // 👈 вот это добавляем
        ]);

        $query->where('acc_domain_id', $request->user()->domain_id);

        return AcceptanceResource::collection($query->paginate(20));
    }

    /**
     * @OA\Post(
     *     path="/api/acceptances",
     *     summary="Create a new acceptance",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Acceptance")
     *     ),
     *     @OA\Response(response=201, description="Acceptance created")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'acc_wh_id' => 'required|integer',
            'acc_shop_id' => 'required|integer',
            'acc_type' => 'required|integer',
            'acc_comment' => 'nullable|string|max:255'
        ]);

        $data['acc_user_id'] = $request->user()->id;
        $data['acc_domain_id'] = $request->user()->domain_id;
        $data['acc_status'] = 1;
        $data['acc_date'] = now();

        $acceptance = rwAcceptance::create($data);

        return new AcceptanceResource($acceptance);
    }

    /**
     * @OA\Get(
     *     path="/api/acceptances/{id}",
     *     summary="Get acceptance by ID",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Acceptance found")
     * )
     */
    public function show($id, Request $request)
    {
        $acceptance = rwAcceptance::where('acc_id', $id)
            ->where('acc_domain_id', $request->user()->domain_id)
            ->with('getAccStatus', 'getAccType', 'getWarehouse', 'getUser', 'offers') // 👈
            ->firstOrFail();

        return new AcceptanceResource($acceptance);
    }

    /**
     * @OA\Put(
     *     path="/api/acceptances/{id}",
     *     summary="Update acceptance",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(ref="#/components/schemas/Acceptance"),
     *     @OA\Response(response=200, description="Updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $acceptance = rwAcceptance::where('acc_id', $id)
            ->where('acc_domain_id', $request->user()->domain_id)
            ->firstOrFail();

        $acceptance->update($request->only([
            'acc_wh_id', 'acc_shop_id', 'acc_type', 'acc_comment'
        ]));

        return new AcceptanceResource($acceptance);
    }

    /**
     * @OA\Delete(
     *     path="/api/acceptances/{id}",
     *     summary="Delete acceptance",
     *     tags={"Acceptances"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted")
     * )
     */
    public function destroy($id, Request $request)
    {
        $acceptance = rwAcceptance::where('acc_id', $id)
            ->where('acc_domain_id', $request->user()->domain_id)
            ->firstOrFail();

        $acceptance->delete();

        return response()->json(['message' => 'Deleted']);
    }
}