<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourierApiController extends Controller
{
    /**
     * Daftar resep untuk kurir:
     * - Semua siap_kirim yang belum diambil kurir manapun (courier_id NULL)
     * - Resep milik kurir ini (dibawa / dalam_pengiriman)
     */
    public function myOrders(Request $request)
    {
        $courier = $request->user()->courier;

        if (! $courier) {
            return response()->json(['message' => 'Data kurir tidak ditemukan.'], 404);
        }

        $orders = Prescription::with(['patient', 'address'])
            ->where('is_active', true)
            ->where(function ($q) use ($courier) {
                // Resep tersedia (belum diambil siapapun)
                $q->where(fn($q2) => $q2->where('status', 'siap_kirim')->whereNull('courier_id'))
                  // Resep milik kurir ini yang sedang diproses
                  ->orWhere(fn($q2) => $q2->where('courier_id', $courier->id)
                                          ->whereIn('status', ['dibawa', 'dalam_pengiriman']));
            })
            ->orderByRaw("FIELD(status, 'dalam_pengiriman', 'dibawa', 'siap_kirim')")
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
     * Statistik pengiriman selesai kurir (hari ini & bulan ini).
     */
    public function stats(Request $request)
    {
        $courier = $request->user()->courier;

        if (! $courier) {
            return response()->json(['message' => 'Data kurir tidak ditemukan.'], 404);
        }

        $today = Prescription::where('courier_id', $courier->id)
            ->where('status', 'terkirim')
            ->whereDate('updated_at', today())
            ->count();

        $thisMonth = Prescription::where('courier_id', $courier->id)
            ->where('status', 'terkirim')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $total = Prescription::where('courier_id', $courier->id)
            ->where('status', 'terkirim')
            ->count();

        return response()->json([
            'today'      => $today,
            'this_month' => $thisMonth,
            'total'      => $total,
        ]);
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
     * Kurir ambil resep dari RS (batch pickup).
     * - Set courier_id ke kurir yang login
     * - Ubah status siap_kirim → dibawa
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
            ->where('status', 'siap_kirim')
            ->whereNull('courier_id')   // hanya yang belum diambil
            ->get();

        if ($prescriptions->isEmpty()) {
            return response()->json(['message' => 'Tidak ada resep yang valid untuk diambil.'], 422);
        }

        $prescriptions->each->update([
            'courier_id' => $courier->id,
            'status'     => 'dibawa',
        ]);

        return response()->json([
            'message' => "{$prescriptions->count()} resep berhasil diambil.",
            'count'   => $prescriptions->count(),
        ]);
    }

    /**
     * Update status resep oleh kurir.
     */
    public function updateStatus(Request $request, Prescription $prescription)
    {
        $courier = $request->user()->courier;

        // Untuk siap_kirim (belum ada courier): set courier_id dulu
        if ($prescription->status === 'siap_kirim' && is_null($prescription->courier_id)) {
            $prescription->update(['courier_id' => $courier->id]);
        }

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

    /**
     * Upload foto bukti pengiriman saat tiba di lokasi pasien.
     */
    public function uploadPhoto(Request $request, Prescription $prescription)
    {
        $courier = $request->user()->courier;

        if (! $courier || $prescription->courier_id !== $courier->id) {
            return response()->json(['message' => 'Tidak memiliki akses ke resep ini.'], 403);
        }

        if ($prescription->status !== 'dalam_pengiriman') {
            return response()->json(['message' => 'Foto hanya bisa diupload saat status dalam pengiriman.'], 422);
        }

        $request->validate(['photo' => 'required|image|max:5120']); // max 5MB

        // Hapus foto lama jika ada
        if ($prescription->delivery_photo) {
            Storage::disk('public')->delete($prescription->delivery_photo);
        }

        $path = $request->file('photo')->store('delivery-photos', 'public');
        $prescription->update(['delivery_photo' => $path]);

        return response()->json([
            'message'   => 'Foto berhasil diupload.',
            'photo_url' => asset('storage/' . $path),
        ]);
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private function formatOrder(Prescription $p): array
    {
        return [
            'id'             => $p->id,
            'nomor_resep'    => $p->nomor_resep,
            'tanggal'        => $p->tanggal->format('Y-m-d'),
            'status'         => $p->status,
            'status_label'   => $p->status_label,
            'keterangan'     => $p->keterangan,
            'delivery_photo' => $p->delivery_photo
                                    ? asset('storage/' . $p->delivery_photo)
                                    : null,
            'patient' => [
                'name'  => $p->patient->name  ?? '',
                'phone' => $p->patient->phone ?? '',
            ],
            'address' => $p->address ? [
                'label'     => $p->address->label,
                'address'   => $p->address->address,
                'latitude'  => $p->address->latitude,
                'longitude' => $p->address->longitude,
            ] : null,
        ];
    }
}
