<?php

use App\Http\Controllers\Api\AcceptanceController;
use App\Http\Controllers\Api\AcceptanceOfferController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Api\OfferController;
use App\Http\Middleware\ForceJsonResponse;

Route::middleware([ForceJsonResponse::class])->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'    => 'error',
                'message' => 'Incorrect login or password'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    });

    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'    => 'success',
            'message' => 'The exit is complete'
        ]);
    });

    // Работа с товарами
    Route::middleware('auth:sanctum')->prefix('products')->group(function () {
        Route::get('/', [OfferController::class, 'index']);
        Route::post('/', [OfferController::class, 'insert']);
        Route::get('/{id}', [OfferController::class, 'show']);
        Route::put('/{id}', [OfferController::class, 'update']);
        Route::delete('/{id}', [OfferController::class, 'destroy']);
    });

    // Работа с заказами
    Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'insert']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}', [OrderController::class, 'update']);
        Route::delete('/{id}', [OrderController::class, 'destroy']);

        Route::post('/{id}/offers', [OrderController::class, 'addOffer']);
        Route::put('/{order_id}/offers/{offer_id}', [OrderController::class, 'updateOffer']);
        Route::delete('/{order_id}/offers/{offer_id}', [OrderController::class, 'deleteOffer']);

    });

    Route::middleware('auth:sanctum')->prefix('acceptances')->group(function () {

        // Приемки
        Route::get('/', [AcceptanceController::class, 'index']);
        Route::post('/', [AcceptanceController::class, 'store']);
        Route::get('/{id}', [AcceptanceController::class, 'show']);
        Route::put('/{id}', [AcceptanceController::class, 'update']);
        Route::delete('/{id}', [AcceptanceController::class, 'destroy']);

        // Товары в приемке
        Route::post('/{id}/offers', [AcceptanceOfferController::class, 'add']);
        Route::put('/{acceptance_id}/offers/{offer_id}', [AcceptanceOfferController::class, 'update']);
        Route::delete('/{acceptance_id}/offers/{offer_id}', [AcceptanceOfferController::class, 'delete']);
    });

});