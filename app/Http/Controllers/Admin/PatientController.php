<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $patients = Patient::withCount('addresses')->latest()->get();
            return response()->json(['data' => $patients]);
        }
        return view('admin.patients.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik'        => 'required|digits:16|unique:patients,nik',
            'rm'         => 'required|string|max:20|unique:patients,rm',
            'name'       => 'required|string|max:255',
            'birth_date' => 'required|date',
            'phone'      => 'required|string|max:20',
        ], [
            'nik.required'        => 'NIK wajib diisi.',
            'nik.digits'          => 'NIK harus 16 digit.',
            'nik.unique'          => 'NIK sudah terdaftar.',
            'rm.required'         => 'Nomor RM wajib diisi.',
            'rm.unique'           => 'Nomor RM sudah terdaftar.',
            'name.required'       => 'Nama wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'phone.required'      => 'Nomor HP wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient = Patient::create($request->only('nik', 'rm', 'name', 'birth_date', 'phone') + ['is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Pasien berhasil ditambahkan.', 'patient' => $patient]);
    }

    public function show(Patient $patient)
    {
        return response()->json($patient);
    }

    public function update(Request $request, Patient $patient)
    {
        $validator = Validator::make($request->all(), [
            'nik'        => 'required|digits:16|unique:patients,nik,' . $patient->id,
            'rm'         => 'required|string|max:20|unique:patients,rm,' . $patient->id,
            'name'       => 'required|string|max:255',
            'birth_date' => 'required|date',
            'phone'      => 'required|string|max:20',
        ], [
            'nik.required'        => 'NIK wajib diisi.',
            'nik.digits'          => 'NIK harus 16 digit.',
            'nik.unique'          => 'NIK sudah terdaftar.',
            'rm.required'         => 'Nomor RM wajib diisi.',
            'rm.unique'           => 'Nomor RM sudah terdaftar.',
            'name.required'       => 'Nama wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'phone.required'      => 'Nomor HP wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient->update([
            ...$request->only('nik', 'rm', 'name', 'birth_date', 'phone'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => true, 'message' => 'Data pasien berhasil diupdate.', 'patient' => $patient->fresh()]);
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return response()->json(['success' => true, 'message' => 'Pasien berhasil dihapus.']);
    }
}
