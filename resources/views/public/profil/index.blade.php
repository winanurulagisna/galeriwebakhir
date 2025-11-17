@extends('public.layouts.app')

@section('title', 'Profil Sekolah - SMKN 4 KOTA BOGOR')

@section('content')
<div class="container mx-auto px-4 py-8">
    

    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-2xl md:text-3xl font-bold text-[#023859] mb-2">Profil Sekolah</h1>
        
    </div>

    <!-- Profile Content -->
    @if(isset($profiles) && $profiles->count() > 0)
        <div class="space-y-12">
            @foreach($profiles as $profile)
            <section>
                <h2 class="text-2xl font-bold text-[#023859] mb-4">{{ $profile->judul }}</h2>
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! nl2br(e($profile->isi)) !!}
                </div>
            </section>
            @endforeach
        </div>
    @else
        <!-- Modern Single-Column Profile Content -->
        <div class="max-w-3xl mx-auto space-y-12">
            <!-- Video + Introduction (Side-by-Side) -->
            <section>
                <div class="">
                    <div class="flex flex-col md:flex-row items-start gap-6 md:gap-8">
                        <!-- Video -->
                        <div class="relative w-full md:w-1/2 rounded-xl overflow-hidden">
                            <div class="relative w-full" style="padding-top: 56.25%;">
                                <iframe
                                    class="absolute inset-0 w-full h-full"
                                    src="https://www.youtube.com/embed/N6cmqCbQllo"
                                    title="SMKN 4 Kota Bogor"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen
                                    loading="lazy"></iframe>
                            </div>
                        </div>
                        <!-- Intro Text -->
                        <div class="w-full md:w-1/2">
                           
                            <p class="text-gray-600 leading-relaxed">
                                SMK Negeri 4 Kota Bogor merupakan salah satu sekolah menengah kejuruan yang berkomitmen untuk melahirkan lulusan yang kompeten, berkarakter, dan siap bersaing di dunia kerja.
                                Dengan dukungan fasilitas pembelajaran yang memadai, kolaborasi dengan industri, serta visi dan misi yang berfokus pada pembentukan profil pelajar Pancasila, SMKN 4 Kota Bogor turut berperan aktif dalam memajukan pendidikan di Indonesia.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Departments -->
            <section>
                <div class="">
                    <h3 class="text-2xl font-bold text-[#023859] mb-4">Program Keahlian</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4 p-0">
                            <div class="w-12 h-12 rounded-full bg-sky-300 text-white flex items-center justify-center shrink-0 shadow-md">
                                <!-- computer-desktop icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                    <path d="M3.75 4.5A2.25 2.25 0 0 1 6 2.25h12A2.25 2.25 0 0 1 20.25 4.5v9A2.25 2.25 0 0 1 18 15.75H6A2.25 2.25 0 0 1 3.75 13.5v-9Z"/>
                                    <path d="M7.5 18a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 18Z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Pengembangan Perangkat Lunak dan Gim (PPLG)</p>
                                <p class="text-sm text-gray-600 mt-1">Fokus pada pemrograman, pengembangan aplikasi, serta game development.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-0">
                            <div class="w-12 h-12 rounded-full bg-emerald-300 text-white flex items-center justify-center shrink-0 shadow-md">
                                <!-- server-stack icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                    <path d="M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v.75A2.25 2.25 0 0 1 17.25 9.75H6.75A2.25 2.25 0 0 1 4.5 7.5v-.75Z"/>
                                    <path d="M4.5 12.75A2.25 2.25 0 0 1 6.75 10.5h10.5a2.25 2.25 0 0 1 2.25 2.25v.75a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 13.5v-.75Z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Teknik Jaringan Komputer dan Telekomunikasi (TJKT)</p>
                                <p class="text-sm text-gray-600 mt-1">Jaringan komputer, telekomunikasi, dan sistem keamanan jaringan.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-0">
                            <div class="w-12 h-12 rounded-full bg-amber-300 text-white flex items-center justify-center shrink-0 shadow-md">
                                <!-- wrench-screwdriver icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                    <path d="M21.75 5.25a4.5 4.5 0 0 1-6.364 4.131l-7.755 7.755a1.5 1.5 0 1 1-2.122-2.122l7.755-7.755A4.5 4.5 0 1 1 21.75 5.25Z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Teknik Pengelasan dan Fabrikasi Logam (TPFL)</p>
                                <p class="text-sm text-gray-600 mt-1">Pengelasan, fabrikasi logam, dan proses manufaktur.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 p-0">
                            <div class="w-12 h-12 rounded-full bg-violet-300 text-white flex items-center justify-center shrink-0 shadow-md">
                                <!-- cog-6-tooth icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                    <path fill-rule="evenodd" d="M11.078 2.25h1.844l.317 1.902a7.5 7.5 0 0 1 1.61.933l1.715-.989 1.303 1.303-.99 1.716c.356.5.672 1.046.933 1.61l1.902.317v1.844l-1.902.317a7.5 7.5 0 0 1-.933 1.61l.989 1.715-1.303 1.303-1.716-.99a7.5 7.5 0 0 1-1.61.933l-.317 1.902h-1.844l-.317-1.902a7.5 7.5 0 0 1-1.61-.933l-1.715.989-1.303-1.303.99-1.716a7.5 7.5 0 0 1-.933-1.61l-1.902-.317V9.75l1.902-.317c.261-.564.577-1.11.933-1.61l-.989-1.715L6.56 4.446l1.716.99a7.5 7.5 0 0 1 1.61-.933l.317-1.902Zm.922 6.75a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">Teknik Kendaraan Ringan (TKR)</p>
                                <p class="text-sm text-gray-600 mt-1">Perawatan, perbaikan, dan penanganan sistem kendaraan ringan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Vision -->
            <section>
                <div class="">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-emerald-300 rounded-full text-white flex items-center justify-center shadow-md">
                            <!-- eye icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                <path d="M12 5.25c-5.25 0-9 4.5-9 6.75s3.75 6.75 9 6.75 9-4.5 9-6.75-3.75-6.75-9-6.75Zm0 11.25a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#023859]">Visi</h3>
                    </div>
                    <p class="text-gray-700 italic leading-relaxed">
                        "Menjadi sekolah kejuruan unggulan yang menghasilkan lulusan berkarakter, kompeten, dan siap bersaing di era global."
                    </p>
                </div>
            </section>

            <!-- Mission -->
            <section>
                <div class="">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-violet-300 rounded-full text-white flex items-center justify-center shadow-md">
                            <!-- flag icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                                <path d="M3.75 3a.75.75 0 0 1 .75-.75h.75A2.25 2.25 0 0 1 7.5 4.5h8.25a2.25 2.25 0 0 1 1.59.66l1.5 1.5a.75.75 0 0 1 0 1.06l-1.5 1.5a2.25 2.25 0 0 1-1.59.66H7.5A2.25 2.25 0 0 0 5.25 12v9a.75.75 0 0 1-1.5 0V3Z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-[#023859]">Misi</h3>
                    </div>
                    <ul class="space-y-3 text-gray-700">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-purple-600 mt-1"><path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm3.28 7.22a.75.75 0 1 0-1.06-1.06L11 11.63 9.53 10.16a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l3.75-3.75Z" clip-rule="evenodd"/></svg>
                            <span>Menyelenggarakan pendidikan kejuruan berkualitas dan berstandar internasional.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-purple-600 mt-1"><path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm3.28 7.22a.75.75 0 1 0-1.06-1.06L11 11.63 9.53 10.16a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l3.75-3.75Z" clip-rule="evenodd"/></svg>
                            <span>Mengembangkan karakter dan kompetensi siswa sesuai kebutuhan industri.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-purple-600 mt-1"><path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm3.28 7.22a.75.75 0 1 0-1.06-1.06L11 11.63 9.53 10.16a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l3.75-3.75Z" clip-rule="evenodd"/></svg>
                            <span>Membangun kemitraan strategis dengan dunia industri dan dunia kerja.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-purple-600 mt-1"><path fill-rule="evenodd" d="M12 2.25a9.75 9.75 0 1 0 0 19.5 9.75 9.75 0 0 0 0-19.5Zm3.28 7.22a.75.75 0 1 0-1.06-1.06L11 11.63 9.53 10.16a.75.75 0 1 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l3.75-3.75Z" clip-rule="evenodd"/></svg>
                            <span>Menciptakan lingkungan belajar yang kondusif, inklusif, dan inovatif.</span>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    @endif
</div>
@endsection

