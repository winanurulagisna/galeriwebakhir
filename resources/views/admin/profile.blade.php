@extends('admin.layouts.app')

@section('title', 'Pengaturan Akun - Admin SMKN 4 KOTA BOGOR')
@section('page-title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="space-y-6">
        <!-- Profile Information -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-[#023859]">Informasi Profil</h2>
                <p class="text-gray-600">Kelola informasi akun administrator Anda</p>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#023859] focus:border-[#023859] @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#023859] focus:border-[#023859] @error('email') border-red-500 @enderror"
                           placeholder="Masukkan alamat email"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Saat Ini
                    </label>
                    <input type="password" 
                           name="current_password" 
                           id="current_password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#023859] focus:border-[#023859]"
                           placeholder="Masukkan password saat ini (opsional)">
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password Baru
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#023859] focus:border-[#023859] @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password baru (opsional)">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#023859] focus:border-[#023859]"
                           placeholder="Konfirmasi password baru">
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button type="submit" 
                            class="px-6 py-2 bg-[#023859] hover:bg-[#01304b] text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Profil
                    </button>
                </div>
            </form>
        </div>

        <!-- Account Security -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-[#023859]">Keamanan Akun</h2>
                <p class="text-gray-600">Pengaturan keamanan untuk melindungi akun Anda</p>
            </div>

            <div class="space-y-4">
                <!-- Last Login -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Login Terakhir</h3>
                        <p class="text-sm text-gray-500">{{ $user->updated_at ? $user->updated_at->format('d M Y H:i') : 'Tidak tersedia' }}</p>
                    </div>
                    <div class="text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Status Akun</h3>
                        <p class="text-sm text-gray-500">Aktif dan terverifikasi</p>
                    </div>
                    <div class="text-green-600">
                        <i class="fas fa-shield-alt text-xl"></i>
                    </div>
                </div>

                <!-- Two Factor Authentication -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Autentikasi 2 Faktor</h3>
                        <p class="text-sm text-gray-500">Belum diaktifkan</p>
                    </div>
                    <button class="px-3 py-1 bg-[#023859] hover:bg-[#01304b] text-white text-sm font-medium rounded-lg transition-colors">
                        Aktifkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-red-200">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-red-900">Zona Berbahaya</h2>
                <p class="text-red-600">Tindakan ini tidak dapat dibatalkan</p>
            </div>

            <div class="space-y-4">
                <!-- Delete Account -->
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-red-900">Hapus Akun</h3>
                        <p class="text-sm text-red-600">Hapus akun administrator secara permanen</p>
                    </div>
                    <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        Hapus Akun
                    </button>
                </div>

                <!-- Logout All Devices -->
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                    <div>
                        <h3 class="text-sm font-medium text-red-900">Logout Semua Perangkat</h3>
                        <p class="text-sm text-red-600">Keluar dari semua perangkat yang sedang login</p>
                    </div>
                    <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        Logout Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
