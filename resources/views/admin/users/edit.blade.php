@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<div class="max-w-lg bg-white rounded-xl shadow p-6">
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-4">
        @csrf @method('PATCH')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select name="role"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="farmasi" {{ old('role',$user->role)=='farmasi'?'selected':'' }}>Farmasi</option>
                <option value="superadmin" {{ old('role',$user->role)=='superadmin'?'selected':'' }}>Super Admin</option>
            </select>
            @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="is_active"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
            <input type="password" name="password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Minimal 8 karakter">
            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Ulangi password baru">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-5 py-2 rounded-lg">
                Update
            </button>
            <a href="{{ route('admin.users.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm px-5 py-2 rounded-lg">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
