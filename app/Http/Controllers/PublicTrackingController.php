<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PublicTrackingController extends Controller
{
    /**
     * Halaman utama: form input kode resep.
     */
    public function index(Request $request)
    {
        return view('track.index');
    }

    /**
     * Halaman tracking berdasarkan kode resep.
     * Bisa diakses publik tanpa login.
     */
    public function show(string $kode)
    {
        $prescription = Prescription::with(['patient', 'address', 'courier'])
            ->where('nomor_resep', $kode)
            ->firstOrFail();

        $mapsKey = config('services.google_maps.key');

        return view('track.show', compact('prescription', 'mapsKey'));
    }

    /**
     * JSON endpoint untuk polling real-time (dipanggil dari JS).
     */
    public function poll(string $kode)
    {
        $prescription = Prescription::with(['patient', 'address', 'courier'])
            ->where('nomor_resep', $kode)
            ->first();

        if (! $prescription) {
            return response()->json(['error' => 'Resep tidak ditemukan.'], 404);
        }

        $courier = $prescription->courier;

        return response()->json([
            'status'       => $prescription->status,
            'status_label' => $prescription->status_label,
            'status_color' => $prescription->status_color,
            'courier'      => $courier ? [
                'name'         => $courier->name,
                'plate_number' => $courier->plate_number,
                'phone'        => $courier->phone,
                'lat'          => $courier->last_latitude,
                'lng'          => $courier->last_longitude,
                'last_seen'    => $courier->last_seen_at?->diffForHumans() ?? '-',
            ] : null,
            'destination'  => $prescription->address ? [
                'lat'     => (float) $prescription->address->latitude,
                'lng'     => (float) $prescription->address->longitude,
                'label'   => $prescription->address->label,
                'address' => $prescription->address->address,
            ] : null,
        ]);
    }
}
