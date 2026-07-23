<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

include 'include/nav.php';
?>

<main class="flex-grow">
    <!-- HERO SECTION (CLASSIC/TRADITIONAL SCHOOL STYLE) -->
    <section id="beranda"
        class="relative pt-16 pb-24 md:pt-36 md:pb-40 overflow-hidden bg-slate-800 flex items-center justify-center min-h-[75vh] md:min-h-[85vh] border-b-4 border-blue-800">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1920&q=80"
                alt="Lingkungan SMPN Cimahi" class="w-full h-full object-cover object-center opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-800/40 to-slate-900/80"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center">

            <!-- School Logo / Crest Placeholder -->
            <div
                class="mb-4 md:mb-6 bg-white/10 p-3 md:p-5 rounded-full backdrop-blur-sm border border-white/20 shadow-lg">
                <i class="fa-solid fa-school-flag text-3xl md:text-5xl text-white"></i>
            </div>

            <!-- Accreditation Badge -->
            <div
                class="inline-flex items-center gap-1.5 md:gap-2 px-3 md:px-4 py-1 md:py-1.5 rounded-full bg-blue-700/80 border border-blue-500 text-white text-[10px] md:text-xs font-bold tracking-widest uppercase mb-4 md:mb-6 shadow-sm">
                <i class="fa-solid fa-award"></i> Akreditasi A Unggul
            </div>

            <!-- Welcome Text -->
            <p
                class="text-blue-200 font-semibold tracking-widest uppercase text-xs sm:text-base mb-2 md:mb-3 drop-shadow-md">
                Selamat Datang di Portal Resmi
            </p>

            <!-- Formal Headline -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white tracking-tight leading-tight mb-4 md:mb-6 drop-shadow-lg"
                style="font-family: 'Poppins', sans-serif;">
                SMP Negeri Cimahi
            </h1>

            <!-- Formal Description -->
            <p
                class="text-sm sm:text-xl text-slate-200 max-w-3xl mx-auto leading-relaxed mb-8 md:mb-10 font-medium drop-shadow-md px-2 md:px-0">
                Mewujudkan generasi penerus bangsa yang unggul dalam prestasi akademik, tangguh dalam iman dan takwa,
                serta berwawasan lingkungan dan global.
            </p>

            <!-- Classic CTA Buttons -->
            <div
                class="flex flex-col sm:flex-row items-center justify-center gap-3 md:gap-4 w-full sm:w-auto px-4 sm:px-0">
                <a href="pendaftaran.php"
                    class="w-full sm:w-auto px-6 py-2.5 md:px-8 md:py-3.5 bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs md:text-sm rounded-full shadow-md border-b-4 border-blue-900 transition-all active:translate-y-1 active:border-b-0 flex items-center justify-center gap-2 uppercase tracking-wide">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Pendaftaran Siswa Baru</span>
                </a>

                <a href="#tentang"
                    class="w-full sm:w-auto px-6 py-2.5 md:px-8 md:py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs md:text-sm rounded-full shadow-sm border border-white/50 backdrop-blur-sm transition-all flex items-center justify-center gap-2 uppercase tracking-wide">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Profil Sekolah</span>
                </a>
            </div>
        </div>
    </section>

    <!-- STATS COUNTER BAR -->
    <section class="bg-white border-y border-slate-200 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x-0 md:divide-x divide-slate-200">

                <div class="p-2 space-y-1">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        <span class="counter" data-target="300">0</span>+
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-wider">Siswa/Siswi
                    </div>
                </div>

                <div class="p-2 space-y-1">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">A</div>
                    <div class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-wider">Akreditasi</div>
                </div>

                <div class="p-2 space-y-1">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        <span class="counter" data-target="50">0</span>+
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-wider">Guru/Staf</div>
                </div>

                <div class="p-2 space-y-1">
                    <div class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        <span class="counter" data-target="6">0</span>
                    </div>
                    <div class="text-xs sm:text-sm font-medium text-slate-500 uppercase tracking-wider">Ekstrakulikuler
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- TENTANG KAMI SECTION -->
    <section id="tentang" class="py-16 md:py-24 bg-brand-lightBg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- SECTION HEADER -->
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Tentang Kami</h2>
                <p class="text-brand-grayText text-base sm:text-lg leading-relaxed">
                    SMPN Cimahi senantiasa berkomitmen menciptakan lingkungan belajar yang kondusif, berteknologi, serta
                    membentuk karakter siswa yang mandiri, cerdas, dan santun.
                </p>
            </div>

            <!-- CONTENT GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                <!-- LEFT COLUMN (DESKTOP LEFT, MOBILE ALL-IN-ONE WITH ORDER) -->
                <div class="lg:col-span-6 flex flex-col space-y-6">

                    <!-- 1. BADGE & TITLE -->
                    <div class="order-1 space-y-3 text-center lg:text-left">
                        <div
                            class="inline-block px-3 py-1 bg-slate-200 text-slate-700 text-xs font-semibold rounded uppercase tracking-wider">
                            Visi & Misi
                        </div>

                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-snug">
                            Mewujudkan Generasi Cerdas Berkarakter
                        </h3>
                    </div>

                    <!-- 2. FEATURE IMAGE (MOBILE ONLY: order-2) -->
                    <div class="order-2 lg:hidden my-2 w-full">
                        <div class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200 group">
                            <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80"
                                alt="Fasilitas dan Lingkungan Belajar SMPN Cimahi"
                                class="w-full h-[300px] sm:h-[380px] object-cover">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent">
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-6 text-white space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-brand-softBlue">Fasilitas
                                    Sekolah</p>
                                <p class="text-base sm:text-lg font-bold">Lingkungan belajar yang asri, bersih, dan
                                    berteknologi tinggi.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. DESCRIPTION -->
                    <p class="order-3 text-slate-600 leading-relaxed text-sm sm:text-base text-center lg:text-left">
                        Kami menyelenggarakan pendidikan menengah yang tidak hanya berfokus pada prestasi akademik,
                        tetapi juga pada penguatan nilai karakter moral, keterampilan sosial, serta kesiapan dalam
                        menghadapi era digital.
                    </p>

                    <!-- 4. CHECKLIST ITEMS -->
                    <div class="order-4 space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                Menyelenggarakan pembelajaran unggul berbasis kurikulum nasional yang inovatif.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                Menyediakan laboratorium komputer, perpustakaan modern, dan sarana olahraga lengkap.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                Mengembangkan potensi peserta didik melalui kegiatan ekstrakulikuler berprestasi.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <div
                                class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <p class="text-sm font-medium text-slate-700">
                                Membina kedisiplinan, toleransi, dan nilai-nilai keagamaan dalam kehidupan sehari-hari.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN (DESKTOP ONLY FEATURE IMAGE) -->
                <div class="hidden lg:block lg:col-span-6 w-full">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl border border-slate-200 group">
                        <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80"
                            alt="Fasilitas dan Lingkungan Belajar SMPN Cimahi"
                            class="w-full h-[420px] object-cover group-hover:scale-105 transition-transform duration-500">

                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent">
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-wider text-brand-softBlue">Fasilitas
                                Sekolah</p>
                            <p class="text-base sm:text-lg font-bold">Lingkungan belajar yang asri, bersih, dan
                                berteknologi tinggi.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- EKSTRAKULIKULER SECTION -->
    <section id="ekstra" class="py-16 md:py-24 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- SECTION HEADER -->
            <div class="text-center max-w-3xl mx-auto mb-10 space-y-3">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Ekstrakulikuler</h2>
                <p class="text-brand-grayText text-base leading-relaxed">
                    Wadah pengembangan bakat, minat, serta karakter kepemimpinan dan kreativitas siswa SMPN Cimahi.
                </p>
            </div>


            <!-- CARDS CONTAINER (MOBILE: 1 ROW SLIDER | DESKTOP: 3 COLUMNS GRID) -->
            <div id="ekstra-grid"
                class="flex md:grid overflow-x-auto md:overflow-visible snap-x snap-mandatory md:snap-none grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-8 pb-4 md:pb-0 scrollbar-none">

                <!-- CARD 1: PASKIBRA -->
                <div onclick="window.location.href='ekstrakulikuler.php?type=paskibra'"
                    class="ekstra-item leadership w-[85vw] max-w-[320px] md:max-w-none md:w-full shrink-0 snap-center bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between cursor-pointer group">
                    <div>
                        <div class="relative h-48 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1526976668912-1a811878dd37?auto=format&fit=crop&w=600&q=80"
                                alt="Paskibra"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span
                                class="absolute top-3 left-3 bg-brand-navy/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded">Kepemimpinan</span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3
                                class="text-xl font-bold text-slate-900 group-hover:text-brand-accent transition-colors flex items-center justify-between">
                                <span>Paskibra</span>
                                <i
                                    class="fa-solid fa-arrow-right text-xs text-slate-400 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"></i>
                            </h3>
                            <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                                Membentuk kedisiplinan, ketahanan fisik, serta jiwa patriotisme melalui latihan
                                baris-berbaris dan upacara.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-clock text-slate-400"></i>
                            <span>Setiap Selasa & Kamis • 15:00 WIB</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-user text-slate-400"></i>
                            <span>Pembina: Susan S.T</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: PRAMUKA -->
                <div onclick="window.location.href='ekstrakulikuler.php?type=pramuka'"
                    class="ekstra-item leadership w-[85vw] max-w-[320px] md:max-w-none md:w-full shrink-0 snap-center bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between cursor-pointer group">
                    <div>
                        <div class="relative h-48 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=600&q=80"
                                alt="Pramuka"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span
                                class="absolute top-3 left-3 bg-amber-700/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded">Kepemimpinan</span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3
                                class="text-xl font-bold text-slate-900 group-hover:text-brand-accent transition-colors flex items-center justify-between">
                                <span>Pramuka</span>
                                <i
                                    class="fa-solid fa-arrow-right text-xs text-slate-400 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"></i>
                            </h3>
                            <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                                Melatih kemandirian, kecintaan pada alam, kebersamaan, dan keterampilan kepanduan yang
                                solid.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-clock text-slate-400"></i>
                            <span>Setiap Jumat • 14:00 WIB</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-user text-slate-400"></i>
                            <span>Pembina: Dra. Siti Rahma</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: ENGLISH CLUB -->
                <div onclick="window.location.href='ekstrakulikuler.php?type=english'"
                    class="ekstra-item arts w-[85vw] max-w-[320px] md:max-w-none md:w-full shrink-0 snap-center bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between cursor-pointer group">
                    <div>
                        <div class="relative h-48 overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80"
                                alt="English Club"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span
                                class="absolute top-3 left-3 bg-indigo-600/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded">Seni
                                & Bahasa</span>
                        </div>
                        <div class="p-6 space-y-3">
                            <h3
                                class="text-xl font-bold text-slate-900 group-hover:text-brand-accent transition-colors flex items-center justify-between">
                                <span>English Club</span>
                                <i
                                    class="fa-solid fa-arrow-right text-xs text-slate-400 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"></i>
                            </h3>
                            <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                                Wadah berkomunikasi bahasa Inggris melalui speech, storytelling, debate, dan publikasi
                                berbahasa asing.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-clock text-slate-400"></i>
                            <span>Setiap Rabu • 15:30 WIB</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-user text-slate-400"></i>
                            <span>Pembina: Maya Indriani, M.Pd</span>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: PMR -->
                <div onclick="window.location.href='ekstrakulikuler.php?type=pmr'" class="ekstra-item leadership w-[85vw] max-w-[320px] md:max-w-none md:w-full shrink-0 snap-center bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between cursor-pointer group">
                        <div>
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=600&q=80" alt="PMR" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-3 left-3 bg-red-600/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded">Kepemimpinan</span>
                            </div>
                            <div class="p-6 space-y-3">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-accent transition-colors flex items-center justify-between">
                                    <span>Palang Merah Remaja</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-slate-400 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"></i>
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                                    Edukasi pertolongan pertama, kesehatan remaja, dan kepedulian sosial kemanusiaan bagi sesama.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                <span>Setiap Sabtu • 09:00 WIB</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-user text-slate-400"></i>
                                <span>Pembina: drh. Anita Widyastuti</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 5: BASKET -->
                    <div onclick="window.location.href='ekstrakulikuler.php?type=basket'" class="ekstra-item sports w-[85vw] max-w-[320px] md:max-w-none md:w-full shrink-0 snap-center bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between cursor-pointer group">
                        <div>
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=600&q=80" alt="Basket" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-3 left-3 bg-emerald-600/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded">Olahraga</span>
                            </div>
                            <div class="p-6 space-y-3">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-accent transition-colors flex items-center justify-between">
                                    <span>Bola Basket & Voli</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-slate-400 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"></i>
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                                    Latihan strategi permainan, ketangkasan fisik, serta sportivitas kompetisi antar sekolah.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                <span>Setiap Senin & Kamis • 15:30 WIB</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-user text-slate-400"></i>
                                <span>Pembina: Ahmad Hidayat, S.Pd</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 6: SENI MUSIK -->
                    <div onclick="window.location.href='ekstrakulikuler.php?type=musik'" class="ekstra-item arts w-[85vw] max-w-[320px] md:max-w-none md:w-full shrink-0 snap-center bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between cursor-pointer group">
                        <div>
                            <div class="relative h-48 overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80" alt="Seni Musik" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-3 left-3 bg-purple-600/90 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded">Seni & Bahasa</span>
                            </div>
                            <div class="p-6 space-y-3">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-accent transition-colors flex items-center justify-between">
                                    <span>Seni Musik & Band</span>
                                    <i class="fa-solid fa-arrow-right text-xs text-slate-400 group-hover:text-brand-accent group-hover:translate-x-1 transition-all"></i>
                                </h3>
                                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                                    Pengembangan bakat bermusik (gitar, vokal, keyboard, drum) untuk perlombaan seni dan pentas sekolah.
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                <span>Setiap Rabu & Sabtu • 14:00 WIB</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fa-regular fa-user text-slate-400"></i>
                                <span>Pembina: Rian Febrian, S.Sn</span>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- MOBILE SWIPE DOTS (MOBILE ONLY) -->
                <div class="md:hidden flex justify-center mt-6">
                    <div id="ekstra-dots" class="flex items-center gap-1.5"></div>
                </div>

            </div>
        </section>

        <!-- BERITA & ARTIKEL SECTION -->
        <section id="berita" class="py-16 md:py-24 bg-brand-lightBg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- SECTION HEADER -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Berita</h2>
                    <p class="text-brand-grayText text-base">
                        Informasi terbaru seputar kegiatan, prestasi, dan pengumuman resmi SMPN Cimahi.
                    </p>
                </div>

                <!-- NEWS CARDS GRID -->
                <div id="berita-grid" class="flex overflow-x-auto snap-x snap-mandatory gap-6 pb-6 scrollbar-none scroll-smooth">
                    <?php
                    $has_berita = false;
                    if (isset($db)) {
                        $query_berita = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 6";
                        $result_berita = @mysqli_query($db, $query_berita);
                        if ($result_berita && mysqli_num_rows($result_berita) > 0) {
                            $has_berita = true;
                            $index = 0;
                            while ($berita = mysqli_fetch_assoc($result_berita)):
                                $judul = htmlspecialchars($berita['judul']);
                                $kategori = !empty($berita['tags']) ? htmlspecialchars(explode(',', $berita['tags'])[0]) : (!empty($berita['kategori']) ? htmlspecialchars($berita['kategori']) : 'Kegiatan');
                                $tanggal = date('d M Y', strtotime($berita['tanggal']));

                                $raw_gambar = $berita['gambar'];
                                if (strpos($raw_gambar, 'http') === 0) {
                                    $gambar = htmlspecialchars($raw_gambar);
                                } else if (!empty($raw_gambar)) {
                                    $gambar = 'berita/' . htmlspecialchars($raw_gambar);
                                } else {
                                    $gambar = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';
                                }

                                $isi_raw = $berita['isi'];
                                $isi_bersih = preg_replace('/^##.*$/m', '', $isi_raw);
                                $isi_bersih = str_replace(["\r", "\n"], ' ', $isi_bersih);
                                $excerpt = mb_strimwidth(trim($isi_bersih), 0, 110, '...');

                                ?>
                                <a href="artikel-detail.php?id=<?= $berita['id'] ?>" 
                                         class="berita-item shrink-0 snap-center w-[85vw] sm:w-[320px] md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col justify-between h-full">
                                    <div>
                                        <div class="relative h-48 overflow-hidden bg-slate-100">
                                            <img src="<?= $gambar ?>" 
                                                 alt="<?= htmlspecialchars($judul) ?>" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            <span class="absolute top-3 left-3 bg-brand-navy text-white text-[11px] font-semibold px-2.5 py-1 rounded"><?= htmlspecialchars($kategori) ?></span>
                                        </div>
                                        <div class="p-6 space-y-2 flex-grow">
                                            <p class="text-xs text-slate-400 font-medium"><?= $tanggal ?></p>
                                            <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-brand-accent transition-colors line-clamp-2">
                                                <?= htmlspecialchars($judul) ?>
                                            </h3>
                                            <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                                <?= htmlspecialchars($excerpt) ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-6 pt-0">
                                        <div class="flex items-center text-brand-navy text-sm font-bold group-hover:text-brand-blue transition-colors">
                                            <span>Baca detail</span>
                                            <i class="fa-solid fa-arrow-right ml-1 text-xs transition-transform group-hover:translate-x-1"></i>
                                        </div>
                                    </div>
                                </a>
                                <?php
                                $index++;
                            endwhile;
                        }
                    }

                    if (!$has_berita):
                        ?>
                        <!-- FALLBACK STATIC NEWS CONTENT -->
                        <article onclick="openNewsModal('Implementasi Kurikulum Merdeka Terpadu Partisipasi Siswa', 'Akademik', '12 Mei <?= date('Y') ?>', 'SMPN Cimahi secara aktif mengimplementasikan Kurikulum Merdeka dengan fokus pada Projek Penguatan Profil Pelajar Pancasila (P5). Kegiatan ini melibatkan partisipasi penuh seluruh siswa.', 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80')" 
                                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col justify-between">
                            <div>
                                <div class="relative h-48 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80" 
                                         alt="Berita Kurikulum Merdeka" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <span class="absolute top-3 left-3 bg-brand-navy text-white text-[11px] font-semibold px-2.5 py-1 rounded">Akademik</span>
                                </div>
                                <div class="p-6 space-y-2">
                                    <p class="text-xs text-slate-400 font-medium">12 Mei <?= date('Y') ?></p>
                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-brand-accent transition-colors line-clamp-2">
                                        Implementasi Kurikulum Merdeka Terpadu Partisipasi Siswa
                                    </h3>
                                    <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                        Projek Penguatan Profil Pelajar Pancasila (P5) sukses dilaksanakan dengan pameran karya siswa.
                                    </p>
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                <span class="text-xs font-bold text-brand-blue group-hover:text-brand-accent flex items-center gap-1">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        </article>

                        <article onclick="openNewsModal('Semarak Jalan Karya RS Hingga Potensi Budaya Lokal', 'Kegiatan', '08 Mei <?= date('Y') ?>', 'Dalam rangka menyambut peka seni, SMPN Cimahi menggelar jalan santai dan karnaval busana adat nusantara. Kegiatan ini bertujuan mengenalkan kekayaan budaya Indonesia.', 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80')" 
                                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col justify-between">
                            <div>
                                <div class="relative h-48 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=600&q=80" 
                                         alt="Berita Semarak Jalan Karya" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <span class="absolute top-3 left-3 bg-brand-navy text-white text-[11px] font-semibold px-2.5 py-1 rounded">Kegiatan</span>
                                </div>
                                <div class="p-6 space-y-2">
                                    <p class="text-xs text-slate-400 font-medium">08 Mei <?= date('Y') ?></p>
                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-brand-accent transition-colors line-clamp-2">
                                        Semarak Jalan Karya RS Hingga Potensi Budaya Lokal
                                    </h3>
                                    <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                        Karnaval kebudayaan dan karya seni tari daerah memeriahkan peringatan pekan seni sekolah.
                                    </p>
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                <span class="text-xs font-bold text-brand-blue group-hover:text-brand-accent flex items-center gap-1">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        </article>

                        <article onclick="openNewsModal('Siswa SMPN Cimahi Meraih Medali FLS2N Tingkat Kota', 'Prestasi', '02 Mei <?= date('Y') ?>', 'Prestasi membanggakan kembali diraih oleh kontingen Festival dan Lomba Seni Siswa Nasional (FLS2N) SMPN Cimahi. Ananda Rian dan tim berhasil meraih Juara 1 cabang Lomba Gitar Duet.', 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80')" 
                                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col justify-between">
                            <div>
                                <div class="relative h-48 overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=600&q=80" 
                                         alt="Berita Prestasi FLS2N" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <span class="absolute top-3 left-3 bg-brand-navy text-white text-[11px] font-semibold px-2.5 py-1 rounded">Prestasi</span>
                                </div>
                                <div class="p-6 space-y-2">
                                    <p class="text-xs text-slate-400 font-medium">02 Mei <?= date('Y') ?></p>
                                    <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-brand-accent transition-colors line-clamp-2">
                                        Siswa SMPN Cimahi Meraih Medali FLS2N Tingkat Kota
                                    </h3>
                                    <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                        Perwakilan seni musik instrumen akustik berhasil membawa pulang piala kejuaraan FLS2N.
                                    </p>
                                </div>
                            </div>
                            <div class="p-6 pt-0">
                                <span class="text-xs font-bold text-brand-blue group-hover:text-brand-accent flex items-center gap-1">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>

                <!-- ALL NEWS BUTTON -->
                <div class="mt-12 text-center">
                    <a href="article.php" 
                       class="px-8 py-3 bg-brand-softBlue hover:bg-brand-mutedBlue text-brand-blue font-bold text-sm rounded-full transition-colors inline-flex items-center gap-2">
                        <span>Lihat Semua Berita</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>

            </div>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('ekstra-grid');
        const dotsContainer = document.getElementById('ekstra-dots');
        if (!container || !dotsContainer) return;

        const cards = container.querySelectorAll('.ekstra-item');
        const totalCards = cards.length;
        if (totalCards === 0) return;

        let currentSlide = 0;
        let autoplayTimer = null;

        // Render dots
        dotsContainer.innerHTML = '';
        cards.forEach((_, idx) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.setAttribute('aria-label', `Ekstra Slide ${idx + 1}`);
            dot.className = idx === 0 
                ? 'w-4 h-2 rounded-full bg-brand-navy transition-all duration-300' 
                : 'w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300';
            dot.onclick = () => scrollToSlide(idx);
            dotsContainer.appendChild(dot);
        });

        function updateDots(activeIdx) {
            const dots = dotsContainer.children;
            for (let i = 0; i < dots.length; i++) {
                if (i === activeIdx) {
                    dots[i].className = 'w-4 h-2 rounded-full bg-brand-navy transition-all duration-300';
                } else {
                    dots[i].className = 'w-2 h-2 rounded-full bg-slate-300 hover:bg-slate-400 transition-all duration-300';
                }
            }
        }

        function scrollToSlide(index) {
            if (window.innerWidth >= 768) return;
            currentSlide = (index + totalCards) % totalCards;
            const targetCard = cards[currentSlide];
            if (targetCard) {
                container.scrollTo({
                    left: targetCard.offsetLeft - container.offsetLeft - 16,
                    behavior: 'smooth'
                });
                updateDots(currentSlide);
            }
        }

        container.addEventListener('scroll', function() {
            if (window.innerWidth >= 768) return;
            const scrollLeft = container.scrollLeft;
            const cardWidth = cards[0].offsetWidth + 20;
            const activeIndex = Math.round(scrollLeft / cardWidth);
            if (activeIndex >= 0 && activeIndex < totalCards && activeIndex !== currentSlide) {
                currentSlide = activeIndex;
                updateDots(currentSlide);
            }
        }, { passive: true });

        function startAutoplay() {
            if (autoplayTimer) clearInterval(autoplayTimer);
            autoplayTimer = setInterval(function() {
                if (window.innerWidth < 768) {
                    currentSlide = (currentSlide + 1) % totalCards;
                    scrollToSlide(currentSlide);
                }
            }, 15000);
        }

        startAutoplay();

        // BERITA CAROUSEL AUTOPLAY
        const beritaContainer = document.getElementById('berita-grid');
        if (beritaContainer) {
            const beritaCards = beritaContainer.querySelectorAll('.berita-item');
            const totalBerita = beritaCards.length;
            if (totalBerita > 0) {
                let currentBeritaSlide = 0;
                let beritaAutoplayTimer = null;

                function scrollToBeritaSlide(index) {
                    currentBeritaSlide = index % totalBerita;
                    const targetCard = beritaCards[currentBeritaSlide];
                    if (targetCard) {
                        beritaContainer.scrollTo({
                            left: targetCard.offsetLeft - beritaContainer.offsetLeft - 16,
                            behavior: 'smooth'
                        });
                    }
                }

                function startBeritaAutoplay() {
                    if (beritaAutoplayTimer) clearInterval(beritaAutoplayTimer);
                    beritaAutoplayTimer = setInterval(function() {
                        currentBeritaSlide = (currentBeritaSlide + 1) % totalBerita;
                        scrollToBeritaSlide(currentBeritaSlide);
                    }, 15000);
                }

                startBeritaAutoplay();
                
                // Pause on hover
                beritaContainer.addEventListener('mouseenter', () => clearInterval(beritaAutoplayTimer));
                beritaContainer.addEventListener('mouseleave', startBeritaAutoplay);
            }
        }
    });
    </script>

<?php include 'include/footer.php'; ?>