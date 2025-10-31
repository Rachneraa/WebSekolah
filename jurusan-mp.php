<?php include 'include/nav.php' ?>
<!DOCTYPE html>
<html lang="id">
<head>
        <!-- PWA META TAGS -->
<meta name="theme-color" content="#00499D">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="SMK TI GNC">

<!-- ICONS -->
<link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
<link rel="shortcut icon" href="icons/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
<link rel="manifest" href="manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurusan Manajemen Perkantoran - SMK TI Garuda Nusantara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .faq-content {
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- 1. Hero Section -->
    <section class="relative h-64 sm:h-80 lg:h-96">
        <img src="https://placehold.co/1400x450/005f73/FFFFFF?text=Manajemen+Perkantoran" alt="Hero Jurusan Manajemen Perkantoran" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white text-center px-4">Manajemen Perkantoran</h1>
        </div>
    </section>

    <!-- 2. Tentang Jurusan Section -->
    <section class="container mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Tentang Manajemen Perkantoran</h2>
        <div class="flex flex-col md:flex-row items-center gap-10">
            <!-- Deskripsi Teks -->
            <div class="md:w-1/2 text-gray-700 leading-relaxed">
                <p class="mb-4">
                    Jurusan Manajemen Perkantoran (MP) adalah program yang dirancang untuk menghasilkan tenaga ahli madya di bidang administrasi perkantoran yang modern dan berbasis teknologi. Siswa dididik untuk menjadi 'jantung' dari sebuah organisasi, mengelola alur informasi, dokumen, dan komunikasi internal-eksternal.
                </p>
                <p>
                    Dengan fokus pada teknologi perkantoran, kearsipan digital, dan layanan prima, lulusan MP siap menjadi sekretaris profesional, asisten eksekutif, atau staf administrasi andal yang mampu meningkatkan efisiensi operasional perusahaan.
                </p>
            </div>
            <!-- Gambar Jurusan -->
            <div class="md:w-1/2">
                <img src="https://placehold.co/600x400/94d2bd/000000?text=Lab+Manajemen+Perkantoran" alt="Siswa MP" class="w-full rounded-lg shadow-lg">
            </div>
        </div>
    </section>

    <!-- 3. Kurikulum Section -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Kurikulum</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Tahun Pertama -->
                <div class="border-l-4 border-blue-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Pertama</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Teknologi Perkantoran</li>
                        <li>Korespondensi (Surat-menyurat)</li>
                        <li>Dasar-dasar Kearsipan</li>
                        <li>Ekonomi Bisnis</li>
                    </ul>
                </div>
                <!-- Tahun Kedua -->
                <div class="border-l-4 border-green-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Kedua</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Manajemen Kearsipan Digital & Fisik</li>
                        <li>Administrasi Keuangan Sederhana</li>
                        <li>Otomatisasi Tata Kelola Humas</li>
                        <li>Bahasa Inggris Bisnis</li>
                    </ul>
                </div>
                <!-- Tahun Ketiga -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Ketiga</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Otomatisasi Tata Kelola Sarana Prasarana</li>
                        <li>Manajemen Rapat dan Acara</li>
                        <li>Layanan Prima (Service Excellence)</li>
                        <li>Praktek Kerja Industri</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Kegiatan Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Kegiatan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card Kegiatan 1 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/DDDDDD/000000?text=Pelatihan+Table+Manner" class="w-full h-48 object-cover" alt="Table Manner">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Pelatihan Table Manner</h3>
                        <p class="text-gray-700 text-sm">Membekali siswa dengan etika jamuan bisnis profesional di hotel berbintang.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/CCCCCC/000000?text=Kunjungan+Bank" class="w-full h-48 object-cover" alt="Kunjungan Bank">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Kunjungan Industri ke Perbankan</h3>
                        <p class="text-gray-700 text-sm">Mempelajari alur kerja administrasi dan layanan nasabah di bank ternama.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/EEEEEE/000000?text=Lomba+Sekretaris" class="w-full h-48 object-cover" alt="Lomba Sekretaris">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Lomba Keterampilan Sekretaris</h3>
                        <p class="text-gray-700 text-sm">Ajang unjuk gigi dalam kecepatan mengetik, kearsipan, dan korespondensi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. FAQ Section -->
    <section class="bg-white py-16">
        <div class="container mx-auto px-6 max-w-3xl">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">FAQ</h2>
            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Lulusan Manajemen Perkantoran kerjanya apa?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Lulusan dapat berkarir sebagai Sekretaris, Asisten Eksekutif, Staf Administrasi Umum, Staf Personalia (HRD), Arsiparis, atau Event Organizer di berbagai jenis perusahaan, instansi pemerintah, maupun lembaga pendidikan.
                        </p>
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Apakah jurusan ini hanya untuk perempuan?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Tentu saja tidak. Jurusan ini terbuka untuk siapa saja. Saat ini, banyak perusahaan mencari staf administrasi dan manajer kantor pria yang dinilai memiliki ketelitian dan kemampuan manajerial yang baik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript untuk Accordion FAQ -->
    <script>
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('svg');

                if (content.style.maxHeight) {
                    content.style.maxHeight = null;
                    content.style.paddingTop = '0px';
                    content.style.paddingBottom = '0px';
                    icon.classList.remove('rotate-180');
                } else {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.paddingBottom = '1.25rem';
                    icon.classList.add('rotate-180');
                }

                document.querySelectorAll('.faq-content').forEach(item => {
                    if (item !== content) {
                        item.style.maxHeight = null;
                        item.style.paddingTop = '0px';
                        item.style.paddingBottom = '0px';
                        item.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
                    }
                });
            });
        });
        // SERVICE WORKER REGISTRATION
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .then(registration => console.log('SW Registered'))
    .catch(error => console.log('SW Registration failed:', error));
}
    </script>

</body>
</html>
<?php include 'include/footer.php' ?>