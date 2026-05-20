<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $couriers = Courier::with('user')->latest()->get();
            return response()->json(['data' => $couriers]);
        }
        return view('admin.couriers.index');
    }

    public function store(Request $request)
    {
        $rules = [
            'nik'          => 'required|digits:16|unique:couriers,nik',
            'name'         => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'phone'        => 'required|string|max:20',
            'account_mode' => 'required|in:existing,new,none',
        ];

        // Validasi tambahan sesuai mode akun
        if ($request->account_mode === 'existing') {
            $rules['user_id'] = 'required|exists:users,id';
        } elseif ($request->account_mode === 'new') {
            $rules['login_email']    = 'required|email|unique:users,email';
            $rules['login_password'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'nik.required'          => 'NIK wajib diisi.',
            'nik.digits'            => 'NIK harus 16 digit.',
            'nik.unique'            => 'NIK sudah terdaftar.',
            'name.required'         => 'Nama wajib diisi.',
            'plate_number.required' => 'Plat nomor wajib diisi.',
            'phone.required'        => 'Nomor HP wajib diisi.',
            'user_id.required'      => 'Pilih akun login yang akan dihubungkan.',
            'login_email.required'  => 'Email login wajib diisi.',
            'login_email.unique'    => 'Email sudah digunakan.',
            'login_password.required' => 'Password wajib diisi.',
            'login_password.min'      => 'Password minimal 8 karakter.',
            'login_password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $userId = null;

            if ($request->account_mode === 'new') {
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->login_email,
                    'password'  => Hash::make($request->login_password),
                    'role'      => 'kurir',
                    'is_active' => true,
                    'phone'     => $request->phone,
                ]);
                $userId = $user->id;
            } elseif ($request->account_mode === 'existing') {
                $userId = $request->user_id;
            }

            $courier = Courier::create([
                'nik'          => $request->nik,
                'name'         => $request->name,
                'plate_number' => strtoupper($request->plate_number),
                'phone'        => $request->phone,
                'user_id'      => $userId,
                'is_active'    => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kurir berhasil ditambahkan' . ($userId ? ' beserta akun login.' : '.'),
                'courier' => $courier->load('user'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show(Courier $courier)
    {
        return response()->json($courier->load('user'));
    }

    public function update(Request $request, Courier $courier)
    {
        $rules = [
            'nik'          => 'required|digits:16|unique:couriers,nik,' . $courier->id,
            'name'         => 'required|string|max:255',
            'plate_number' => 'required|string|max:20',
            'phone'        => 'required|string|max:20',
            'account_mode' => 'required|in:existing,new,none',
        ];

        if ($request->account_mode === 'existing') {
            $rules['user_id'] = 'required|exists:users,id';
        } elseif ($request->account_mode === 'new') {
            $rules['login_email']    = 'required|email|unique:users,email';
            $rules['login_password'] = 'required|string|min:8|confirmed';
        }

        $validator = Validator::make($request->all(), $rules, [
            'nik.digits'             => 'NIK harus 16 digit.',
            'nik.unique'             => 'NIK sudah terdaftar.',
            'name.required'          => 'Nama wajib diisi.',
            'plate_number.required'  => 'Plat nomor wajib diisi.',
            'phone.required'         => 'Nomor HP wajib diisi.',
            'user_id.required'       => 'Pilih akun login yang akan dihubungkan.',
            'login_email.required'   => 'Email login wajib diisi.',
            'login_email.unique'     => 'Email sudah digunakan.',
            'login_password.min'     => 'Password minimal 8 karakter.',
            'login_password.confirmed'=> 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $userId = $courier->user_id;

            if ($request->account_mode === 'none') {
                $userId = null;
            } elseif ($request->account_mode === 'new') {
                $user = User::create([
                    'name'      => $request->name,
                    'email'     => $request->login_email,
                    'password'  => Hash::make($request->login_password),
                    'role'      => 'kurir',
                    'is_active' => true,
                    'phone'     => $request->phone,
                ]);
                $userId = $user->id;
            } elseif ($request->account_mode === 'existing') {
                $userId = $request->user_id;
            }

            $courier->update([
                'nik'          => $request->nik,
                'name'         => $request->name,
                'plate_number' => strtoupper($request->plate_number),
                'phone'        => $request->phone,
                'user_id'      => $userId,
                'is_active'    => $request->boolean('is_active'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data kurir berhasil diupdate.',
                'courier' => $courier->fresh()->load('user'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Courier $courier)
    {
        $courier->delete();
        return response()->json(['success' => true, 'message' => 'Kurir berhasil dihapus.']);
    }

    public function availableUsers(Request $request)
    {
        $excludeId  = $request->query('exclude');
        $usedIds    = Courier::whereNotNull('user_id')
                        ->when($excludeId, fn($q) => $q->where('user_id', '!=', $excludeId))
                        ->pluck('user_id');

        $users = User::where('role', 'kurir')
            ->whereNotIn('id', $usedIds)
            ->get(['id', 'name', 'email', 'is_active']);

        return response()->json($users);
    }
}
