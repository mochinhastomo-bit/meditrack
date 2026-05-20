<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Patient;
use App\Models\PatientAddress;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $prescriptions = Prescription::with(['patient', 'address', 'courier'])
                ->latest('tanggal')
                ->latest('id')
                ->get()
                ->map(fn($p) => [
                    'id'           => $p->id,
                    'nomor_resep'  => $p->nomor_resep,
                    'tanggal'      => $p->tanggal->format('d/m/Y'),
                    'patient_name' => $p->patient->name ?? '-',
                    'address'      => $p->address
                        ? "[{$p->address->label}] {$p->address->address}"
                        : '<span class="text-gray-400 text-xs">Belum dipilih</span>',
                    'keterangan'   => $p->keterangan ?? '-',
                    'courier_name' => $p->courier->name ?? '<span class="text-gray-400 text-xs">Belum assign</span>',
                    'status'       => $p->status,
                    'status_label' => $p->status_label,
                    'status_color' => $p->status_color,
                    'is_active'    => $p->is_active,
                ]);

            return response()->json(['data' => $prescriptions]);
        }

        return view('admin.prescriptions.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal'            => 'required|date',
            'patient_id'         => 'required|exists:patients,id',
            'patient_address_id' => 'nullable|exists:patient_addresses,id',
            'courier_id'         => 'nullable|exists:couriers,id',
            'keterangan'         => 'nullable|string|max:500',
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'patient_id.required' => 'Pasien wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prescription = Prescription::create([
            'nomor_resep'        => Prescription::generateNomor(),
            'tanggal'            => $request->tanggal,
            'patient_id'         => $request->patient_id,
            'patient_address_id' => $request->patient_address_id ?: null,
            'courier_id'         => $request->courier_id ?: null,
            'keterangan'         => $request->keterangan,
            'status'             => 'penyiapan',
            'is_active'          => true,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Catatan resep berhasil dibuat.',
            'prescription' => $prescription,
        ]);
    }

    public function show(Prescription $prescription)
    {
        return response()->json($prescription->load(['patient', 'address', 'courier']));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $validator = Validator::make($request->all(), [
            'tanggal'            => 'required|date',
            'patient_id'         => 'required|exists:patients,id',
            'patient_address_id' => 'nullable|exists:patient_addresses,id',
            'courier_id'         => 'nullable|exists:couriers,id',
            'keterangan'         => 'nullable|string|max:500',
            'status'             => 'required|in:penyiapan,siap_kirim,dalam_pengiriman,terkirim,dibatalkan',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $prescription->update([
            'tanggal'            => $request->tanggal,
            'patient_id'         => $request->patient_id,
            'patient_address_id' => $request->patient_address_id ?: null,
            'courier_id'         => $request->courier_id ?: null,
            'keterangan'         => $request->keterangan,
            'status'             => $request->status,
            'is_active'          => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil diupdate.',
        ]);
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return response()->json(['success' => true, 'message' => 'Resep berhasil dihapus.']);
    }

    // AJAX: ambil alamat berdasarkan pasien
    public function addressesByPatient(Patient $patient)
    {
        return response()->json($patient->addresses()->get(['id', 'label', 'address', 'is_primary']));
    }

    // Halaman tracking kurir real-time
    public function track(Prescription $prescription)
    {
        $prescription->load(['patient', 'address', 'courier']);
        $mapsKey = config('services.google_maps.key');
        return view('admin.prescriptions.track', compact('prescription', 'mapsKey'));
    }
}
