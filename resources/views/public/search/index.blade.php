@extends('public.layouts.app')

@section('title', 'Hasil Pencarian - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Hasil Pencarian</h1>
            @if($query)
            <p class="text-xl text-gray-600">Hasil pencarian untuk: <span class="font-semibold text-blue-600">"{{ $query }}"</span></p>
            @else
            <p class="text-xl text-gray-600">Masukkan kata kunci untuk mencari</p>
        @endif
    </div>

        <!-- Search Form -->
    <div class="max-w-2xl mx-auto mb-12">
        <form action="{{ route('search.index') }}" method="GET" class="relative">
            <div class="relative">
                <input type="text" 
                       name="q" 
                       value="{{ $query }}"
                       placeholder="Cari berita, ekstrakurikuler, profil, atau galeri..."
                       class="w-full px-6 py-4 pr-12 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg">
                <button type="submit" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            </form>
        </div>

        <!-- Search Results -->
    @if($query)
        @if($results && $results->count() > 0)
            <div class="mb-8">
                <p class="text-gray-600">
                    Ditemukan <span class="font-semibold text-blue-600">{{ $results->count() }}</span> hasil untuk pencarian 
                    <span class="font-semibold">"{{ $query }}"</span>
                </p>
                @if(config('app.debug'))
                <p class="text-xs text-gray-400 mt-2">
                    Debug: {{ $posts->count() }} berita, {{ $ekstrakurikuler->count() }} ekstrakurikuler, {{ $galleries->count() }} galeri
                </p>
                @endif
                </div>

            <div class="space-y-8">
                @foreach($results as $index => $result)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-8">
                        <div class="flex items-start space-x-6">
                            <!-- Image -->
                            <div class="flex-shrink-0">
                                <div class="w-32 h-32 bg-gray-200 rounded-lg overflow-hidden">
                                    @if(isset($result['image']) && $result['image'])
                                        <img src="{{ $result['image'] }}" 
                                             alt="{{ $result['title'] }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <!-- Type Badge -->
                                <div class="mb-3">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full
                                        @if($result['type'] == 'berita') bg-blue-100 text-blue-800
                                        @elseif($result['type'] == 'ekstrakurikuler') bg-green-100 text-green-800
                                        @elseif($result['type'] == 'profil') bg-purple-100 text-purple-800
                                        @elseif($result['type'] == 'galeri') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($result['type']) }}
                                    </span>
                                    @if(config('app.debug'))
                                    <span class="ml-2 text-xs text-gray-400">ID: {{ $result['data']->id ?? 'N/A' }}</span>
                                    @endif
                    </div>

                                <!-- Title -->
                                <h2 class="text-2xl font-bold text-gray-800 mb-3">
                                    <a href="{{ $result['url'] }}" class="hover:text-blue-600 transition-colors">
                                        {{ $result['title'] }}
                                    </a>
                                </h2>

                                <!-- Description -->
                                <p class="text-gray-600 mb-4 leading-relaxed">
                                    {{ $result['description'] }}
                                </p>

                                <!-- Meta Information -->
                                <div class="flex items-center space-x-6 text-sm text-gray-500">
                                    @if(isset($result['date']))
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $result['date'] }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['category_name']))
                                        <span>
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ $result['category_name'] }}
                                        </span>
                                    @endif
                                    
                                    @if(isset($result['members']))
                                        <span>
                                            <i class="fas fa-users mr-1"></i>
                                            {{ $result['members'] }} Siswa
                                        </span>
            @endif

                                    @if(isset($result['status']))
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                            {{ ucfirst($result['status']) }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Read More Link -->
                                <div class="mt-4">
                                    <a href="{{ $result['url'] }}" 
                                       class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                                        Baca Selengkapnya
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
        @else
            <!-- No Results -->
            <div class="text-center py-16">
                <div class="max-w-md mx-auto">
                    <i class="fas fa-search text-gray-400 text-6xl mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Tidak ada hasil ditemukan</h3>
                    <p class="text-gray-600 mb-8">
                        Maaf, tidak ada hasil yang ditemukan untuk pencarian 
                        <span class="font-semibold">"{{ $query }}"</span>. 
                        Coba gunakan kata kunci yang berbeda atau lebih spesifik.
                    </p>
                    
                    <!-- Search Suggestions -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-800 mb-4">Saran pencarian:</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('search.index', ['q' => 'ekstrakurikuler']) }}" 
                               class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm hover:bg-blue-200 transition-colors">
                                Ekstrakurikuler
                            </a>
                            <a href="{{ route('search.index', ['q' => 'berita']) }}" 
                               class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm hover:bg-green-200 transition-colors">
                                Berita
                            </a>
                            <a href="{{ route('search.index', ['q' => 'galeri']) }}" 
                               class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm hover:bg-purple-200 transition-colors">
                                Galeri
                            </a>
                            <a href="{{ route('search.index', ['q' => 'profil']) }}" 
                               class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm hover:bg-yellow-200 transition-colors">
                                Profil
                            </a>
                                    </div>
                                </div>
                    </div>
                </div>
            @endif
    @else
        <!-- No Search Query -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <i class="fas fa-search text-gray-400 text-6xl mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Mulai Pencarian</h3>
                <p class="text-gray-600 mb-8">
                    Gunakan form pencarian di atas untuk mencari berita, ekstrakurikuler, profil, atau galeri.
                </p>
                
                <!-- Popular Searches -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Pencarian Populer:</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('search.index', ['q' => 'paskibra']) }}" 
                           class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm hover:bg-blue-200 transition-colors">
                            Paskibra
                        </a>
                        <a href="{{ route('search.index', ['q' => 'pramuka']) }}" 
                           class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm hover:bg-green-200 transition-colors">
                            Pramuka
                        </a>
                        <a href="{{ route('search.index', ['q' => 'silat']) }}" 
                           class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm hover:bg-purple-200 transition-colors">
                            Silat
                        </a>
                        <a href="{{ route('search.index', ['q' => 'futsal']) }}" 
                           class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm hover:bg-yellow-200 transition-colors">
                            Futsal
                        </a>
                        <a href="{{ route('search.index', ['q' => 'basket']) }}" 
                           class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm hover:bg-red-200 transition-colors">
                            Basket
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
