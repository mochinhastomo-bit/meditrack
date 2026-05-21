<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Prescription;
use Illuminate\Http\Request;

class CourierApiController extends Controller
{
    /**
     * Daftar resep yang ditugaskan ke kurir yang login.
     * Hanya yang aktif dan statusnya bukan terkirim/dibatalkan.
     */
    public function myOrders(Request $request)
    {
        $courier = $request->user()->courier;

        if (! $courier) {
            return response()->json(['message' => 'Data kurir tidak ditemukan.'], 404);
        }

        $orders = Prescription::with(['patient', 'address'])
            ->where('courier_id', $courier->id)
            ->where('is_active', true)
            ->whereNotIn('status', ['terkirim', 'dibatalkan'])
            ->orderBy('tanggal')
            ->get()
            ->map(fn($p) => $this->formatOrder($p));

        return response()->json(['data' => $orders]);
    }

    /**
     * Semua riwayat resep kurir (termasuk yang sudah selesai).
     */
    public function orderHistory(Request $request)
    {
        $courier = $request->user()->courier;

        if (! $courier) {
            return response()->json(['message' => 'Data kurir tidak ditemukan.'], 404);
        }

        $orders = Prescription::with(['patient', 'address'])
            ->where('courier_id', $courier->id)
            ->latest('tanggal')
            ->take(50)
            ->get()
            ->map(fn($p) => $this->formatOrder($p));

        return response()->json(['data' => $orders]);
    }

    /**
     * Update koordinat GPS kurir.
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $courier = $request->user()->courier;

        if (! $courier) {
            return response()->json(['message' => 'Data kurir tidak ditemukan.'], 404);
        }

        $courier->update([
            'last_latitude'  => $request->latitude,
            'last_longitude' => $request->longitude,
            'last_seen_at'   => now(),
        ]);

        return response()->json([
            'message'   => 'Lokasi diperbarui.',
            'latitude'  => $courier->last_latitude,
            'longitude' => $courier->last_longitude,
            'timestamp' => $courier->last_seen_at->toIso8601String(),
        ]);
    }

    /**
     * Ambil beberapa resep sekaligus dari RS (batch pickup).
     * Mengubah status siap_kirim → dibawa untuk resep yang dipilih.
     */
    public function pickupOrders(Request $request)
    {
        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:prescriptions,id',
        ]);

        $courier = $request->user()->courier;

        if (! $courier) {
            return response()->json(['message' => 'Data kurir tidak ditemukan.'], 404);
        }

        $prescriptions = Prescription::whereIn('id', $request->order_ids)
            ->where('courier_id', $courier->id)
            ->where('status', 'siap_kirim')
            ->get();

        if ($prescriptions->isEmpty()) {
            return response()->json(['message' => 'Tidak ada resep yang valid untuk diambil.'], 422);
        }

        $prescriptions->each->update(['status' => 'dibawa']);

        return response()->json([
            'message' => "{$prescriptions->count()} resep berhasil diambil.",
            'count'   => $prescriptions->count(),
        ]);
    }

    /**
     * Update status resep oleh kurir.
     * Transisi yang diizinkan:
     *   siap_kirim       → dibawa (via pickupOrders)
     *   dibawa           → dalam_pengiriman (hanya jika tidak ada yang aktif)
     *   dalam_pengiriman → terkirim
     */
    public function updateStatus(Request $request, Prescription $prescription)
    {
        $courier = $request->user()->courier;

        if (! $courier || $prescription->courier_id !== $courier->id) {
            return response()->json(['message' => 'Tidak memiliki akses ke resep ini.'], 403);
        }

        $allowed = [
            'siap_kirim'       => 'dalam_pengiriman',
            'dibawa'           => 'dalam_pengiriman',
            'dalam_pengiriman' => 'terkirim',
        ];

        $current = $prescription->status;

        if (! isset($allowed[$current])) {
            return response()->json([
                'message' => 'Status saat ini tidak dapat diubah oleh kurir.',
                'status'  => $current,
            ], 422);
        }

        $newStatus = $allowed[$current];

        // Pastikan tidak ada pengiriman aktif lain sebelum mulai antar
        if ($newStatus === 'dalam_pengiriman') {
            $activeExists = Prescription::where('courier_id', $courier->id)
                ->where('status', 'dalam_pengiriman')
                ->where('id', '!=', $prescription->id)
                ->exists();

            if ($activeExists) {
                return response()->json([
                    'message' => 'Selesaikan pengiriman aktif terlebih dahulu sebelum memulai yang baru.',
                ], 422);
            }
        }

        $prescription->update(['status' => $newStatus]);

        return response()->json([
            'message'      => 'Status resep berhasil diperbarui.',
            'nomor_resep'  => $prescription->nomor_resep,
            'status'       => $newStatus,
            'status_label' => $prescription->fresh()->status_label,
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private function formatOrder(Prescription $p): array
    {
        return [
            'id'           => $p->id,
            'nomor_resep'  => $p->nomor_resep,
            'tanggal'      => $p->tanggal->format('Y-m-d'),
            'status'       => $p->status,
            'status_label' => $p->status_label,
            'keterangan'   => $p->keterangan,
            'patient'      => [
                'name'  => $p->patient->name ?? '',
                'phone' => $p->patient->phone ?? '',
            ],
            'address'      => $p->address ? [
                'label'     => $p->address->label,
                'address'   => $p->address->address,
                'latitude'  => $p->address->latitude,
                'longitude' => $p->address->longitude,
            ] : null,
        ];
    }
}
