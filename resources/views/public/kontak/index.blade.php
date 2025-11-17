@extends('public.layouts.app')

@section('title', 'Kontak Kami - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Kontak Kami</h1>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto">Hubungi kami untuk informasi lebih lanjut tentang SMKN 4 KOTA BOGOR</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Contact Information -->
        <div class="space-y-8">
            <!-- School Info -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Berita Sekolah</h2>
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-school text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">SMKN 4 KOTA BOGOR</h3>
                            <p class="text-gray-600">Sekolah Menengah Kejuruan Negeri 4 Kota Bogor</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Alamat</h3>
                            <p class="text-gray-600">Jl. Raya Tajur No. 123<br>Kecamatan Bogor Selatan<br>Kota Bogor, Jawa Barat 16134</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Telepon</h3>
                            <p class="text-gray-600">(0251) 1234567</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Email</h3>
                            <p class="text-gray-600">info@smkn4bogor.sch.id</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-yellow-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-globe text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Website</h3>
                            <p class="text-gray-600">www.smkn4bogor.sch.id</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Office Hours -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Jam Operasional</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">Senin - Jumat</span>
                        <span class="text-blue-600 font-semibold">07:00 - 16:00</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">Sabtu</span>
                        <span class="text-green-600 font-semibold">07:00 - 12:00</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">Minggu</span>
                        <span class="text-red-600 font-semibold">Tutup</span>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Media Sosial</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fab fa-facebook text-blue-600 text-xl"></i>
                        <span class="text-gray-700">Facebook</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 p-4 bg-pink-50 rounded-lg hover:bg-pink-100 transition-colors">
                        <i class="fab fa-instagram text-pink-600 text-xl"></i>
                        <span class="text-gray-700">Instagram</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fab fa-twitter text-blue-400 text-xl"></i>
                        <span class="text-gray-700">Twitter</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fab fa-youtube text-red-600 text-xl"></i>
                        <span class="text-gray-700">YouTube</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Kirim Pesan</h2>
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Terjadi kesalahan. Silakan periksa kembali data yang Anda masukkan.
                </div>
            @endif

            <form action="{{ route('message.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama lengkap Anda"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="contoh@email.com"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
                    <select id="subject" 
                            name="subject" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subject') border-red-500 @enderror"
                            required>
                        <option value="">Pilih subjek pesan</option>
                        <option value="Informasi Pendaftaran" {{ old('subject') == 'Informasi Pendaftaran' ? 'selected' : '' }}>Informasi Pendaftaran</option>
                        <option value="Informasi Akademik" {{ old('subject') == 'Informasi Akademik' ? 'selected' : '' }}>Informasi Akademik</option>
                        <option value="Informasi Ekstrakurikuler" {{ old('subject') == 'Informasi Ekstrakurikuler' ? 'selected' : '' }}>Informasi Ekstrakurikuler</option>
                        <option value="Kerjasama" {{ old('subject') == 'Kerjasama' ? 'selected' : '' }}>Kerjasama</option>
                        <option value="Keluhan" {{ old('subject') == 'Keluhan' ? 'selected' : '' }}>Keluhan</option>
                        <option value="Lainnya" {{ old('subject') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                    <textarea id="message" 
                              name="message" 
                              rows="6"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('message') border-red-500 @enderror"
                              placeholder="Tuliskan pesan Anda di sini..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>

    <!-- Map Section -->
    <div class="mt-16">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Lokasi Sekolah</h2>
            <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden">
                <!-- Placeholder for map - you can integrate Google Maps here -->
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-blue-200">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt text-blue-600 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Peta Lokasi</h3>
                        <p class="text-gray-600">Jl. Raya Tajur No. 123, Bogor Selatan</p>
                        <a href="https://maps.google.com" target="_blank" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            Buka di Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection