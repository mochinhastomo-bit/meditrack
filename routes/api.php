<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourierApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MediTrack API — untuk Aplikasi Android Kurir
|--------------------------------------------------------------------------
|
| Base URL  : /api
| Auth      : Laravel Sanctum (Bearer Token)
| Format    : JSON
|
*/

// ── Public ──────────────────────────────────────────────────────────────
Route::post('/kurir/login', [AuthController::class, 'login']);

// ── Authenticated (Sanctum) ─────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/kurir/logout',   [AuthController::class, 'logout']);
    Route::get('/kurir/profile',   [AuthController::class, 'profile']);

    // GPS
    Route::post('/kurir/location', [CourierApiController::class, 'updateLocation']);

    // Orders
    Route::get('/kurir/orders',              [CourierApiController::class, 'myOrders']);
    Route::get('/kurir/orders/history',      [CourierApiController::class, 'orderHistory']);
    Route::patch('/kurir/orders/{prescription}/status', [CourierApiController::class, 'updateStatus']);
});

// ── Tracking publik (untuk web, no auth) ────────────────────────────────
Route::get('/track/{prescription}', function (\App\Models\Prescription $prescription) {
    $courier = $prescription->courier;

    if (! $courier || $prescription->status !== 'dalam_pengiriman') {
        return response()->json([
            'tracking' => false,
            'message'  => 'Kurir sedang tidak dalam pengiriman aktif.',
        ]);
    }

    return response()->json([
        'tracking'     => true,
        'nomor_resep'  => $prescription->nomor_resep,
        'status'       => $prescription->status,
        'status_label' => $prescription->status_label,
        'courier'      => [
            'name'         => $courier->name,
            'plate_number' => $courier->plate_number,
            'phone'        => $courier->phone,
        ],
        'location'     => [
            'latitude'   => $courier->last_latitude,
            'longitude'  => $courier->last_longitude,
            'last_seen'  => $courier->last_seen_at?->diffForHumans(),
        ],
        'destination'  => $prescription->address ? [
            'label'     => $prescription->address->label,
            'address'   => $prescription->address->address,
            'latitude'  => $prescription->address->latitude,
            'longitude' => $prescription->address->longitude,
        ] : null,
    ]);
});
