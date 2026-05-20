<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientAddressController extends Controller
{
    public function index(Patient $patient)
    {
        $addresses = $patient->addresses()->latest()->get();
        return response()->json(['data' => $addresses, 'patient' => $patient->only('id', 'name', 'nik')]);
    }

    public function store(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'label'     => 'required|string|max:50',
            'address'   => 'required|string',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'label.required'   => 'Label wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'longitude.numeric'=> 'Longitude harus berupa angka.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Jika is_primary, reset yang lain
        if ($request->boolean('is_primary')) {
            $patient->addresses()->update(['is_primary' => false]);
        }

        // Jika ini alamat pertama, otomatis jadi primary
        $isPrimary = $request->boolean('is_primary') || $patient->addresses()->count() === 0;

        $address = $patient->addresses()->create([
            'label'      => $request->label,
            'address'    => $request->address,
            'latitude'   => $request->latitude ?: null,
            'longitude'  => $request->longitude ?: null,
            'is_primary' => $isPrimary,
        ]);

        return response()->json(['success' => true, 'message' => 'Alamat berhasil ditambahkan.', 'address' => $address]);
    }

    public function show(Patient $patient, PatientAddress $address)
    {
        return response()->json($address);
    }

    public function update(Request $request, Patient $patient, PatientAddress $address)
    {
        $validator = Validator::make($request->all(), [
            'label'     => 'required|string|max:50',
            'address'   => 'required|string',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->boolean('is_primary')) {
            $patient->addresses()->update(['is_primary' => false]);
        }

        $address->update([
            'label'      => $request->label,
            'address'    => $request->address,
            'latitude'   => $request->latitude ?: null,
            'longitude'  => $request->longitude ?: null,
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return response()->json(['success' => true, 'message' => 'Alamat berhasil diupdate.', 'address' => $address->fresh()]);
    }

    public function destroy(Patient $patient, PatientAddress $address)
    {
        $address->delete();

        // Jika yang dihapus primary, set yang pertama jadi primary
        if ($address->is_primary) {
            $patient->addresses()->oldest()->first()?->update(['is_primary' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Alamat berhasil dihapus.']);
    }
}
