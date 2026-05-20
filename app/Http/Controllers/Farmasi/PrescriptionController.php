<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Admin\PrescriptionController as AdminPrescriptionController;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends AdminPrescriptionController
{
    /**
     * Farmasi hanya boleh update resep yang masih berstatus penyiapan.
     */
    public function update(Request $request, Prescription $prescription)
    {
        if ($prescription->status !== 'penyiapan') {
            return response()->json([
                'message' => 'Resep ini tidak dapat diubah karena statusnya sudah "' . $prescription->status_label . '".',
            ], 403);
        }

        return parent::update($request, $prescription);
    }

    /**
     * Farmasi hanya boleh hapus resep yang masih berstatus penyiapan.
     */
    public function destroy(Prescription $prescription)
    {
        if ($prescription->status !== 'penyiapan') {
            return response()->json([
                'message' => 'Resep ini tidak dapat dihapus karena statusnya sudah "' . $prescription->status_label . '".',
            ], 403);
        }

        return parent::destroy($prescription);
    }

    /**
     * Perubahan status cepat dari dashboard Farmasi.
     * Transisi yang diizinkan:
     *   penyiapan  → siap_kirim
     *   siap_kirim → penyiapan  (batalkan penyerahan)
     */
    public function quickStatus(Request $request, Prescription $prescription)
    {
        $transitions = [
            'penyiapan'  => 'siap_kirim',
            'siap_kirim' => 'penyiapan',
        ];

        $current = $prescription->status;

        if (! isset($transitions[$current])) {
            return response()->json([
                'message' => 'Status tidak dapat diubah dari sini.',
            ], 422);
        }

        $newStatus = $transitions[$current];
        $prescription->update(['status' => $newStatus]);

        $fresh = $prescription->fresh();

        return response()->json([
            'success'      => true,
            'new_status'   => $newStatus,
            'status_label' => $fresh->status_label,
            'status_color' => $fresh->status_color,
            'message'      => $newStatus === 'siap_kirim'
                ? "Resep {$prescription->nomor_resep} diserahkan ke kurir."
                : "Resep {$prescription->nomor_resep} dikembalikan ke antrian penyiapan.",
        ]);
    }
}
