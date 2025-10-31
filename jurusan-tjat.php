<?php include 'include/nav.php' ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurusan TJAT - SMK TI Garuda Nusantara</title>
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
        <img src="https://placehold.co/1400x450/c9184a/FFFFFF?text=Jaringan+Akses+Telekomunikasi" alt="Hero Jurusan TJAT" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white text-center px-4">Teknik Jaringan Akses Telekomunikasi</h1>
        </div>
    </section>

    <!-- 2. Tentang Jurusan Section -->
    <section class="container mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Tentang TJAT</h2>
        <div class="flex flex-col md:flex-row items-center gap-10">
            <!-- Deskripsi Teks -->
            <div class="md:w-1/2 text-gray-700 leading-relaxed">
                <p class="mb-4">
                    Teknik Jaringan Akses Telekomunikasi (TJAT) adalah program keahlian yang fokus pada teknologi infrastruktur 'last mile' yang menghubungkan jaringan utama (core network) ke pelanggan. Jurusan ini adalah tulang punggung dari koneksi internet dan komunikasi yang kita nikmati setiap hari.
                </p>
                <p>
                    Siswa akan menjadi ahli dalam teknologi fiber optik (FTTH), jaringan akses tembaga, dan jaringan nirkabel (wireless access). Mereka akan belajar instalasi, pengukuran, dan pemeliharaan perangkat-perangkat telekomunikasi di lapangan.
                </p>
            </div>
            <!-- Gambar Jurusan -->
            <div class="md:w-1/2">
                <img src="https://placehold.co/600x400/ff758f/000000?text=Instalasi+Fiber+Optik" alt="Siswa TJAT" class="w-full rounded-lg shadow-lg">
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
                        <li>Dasar-dasar Telekomunikasi</li>
                        <li>Rangkaian Elektronika Dasar</li>
                        <li>Alat Ukur Telekomunikasi</li>
                        <li>K3LH (Keselamatan Kerja)</li>
                    </ul>
                </div>
                <!-- Tahun Kedua -->
                <div class="border-l-4 border-green-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Kedua</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Jaringan Akses Tembaga</li>
                        <li>Jaringan Akses Fiber Optik (Instalasi)</li>
                        <li>Pengukuran & Analisis Jaringan</li>
                        <li>Teknologi Transmisi</li>
                    </ul>
                </div>
                <!-- Tahun Ketiga -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Ketiga</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Jaringan Akses Nirkabel (Wireless)</li>
                        <li>Manajemen & Pemeliharaan Jaringan Akses</li>
                        <li>Teknologi Seluler (4G/5G)</li>
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
                    <img src="https://placehold.co/600x400/DDDDDD/000000?text=Praktek+Splicing+FO" class="w-full h-48 object-cover" alt="Splicing FO">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Praktek Penyambungan Fiber Optik</h3>
                        <p class="text-gray-700 text-sm">Siswa belajar teknik splicing dan pengukuran redaman kabel fiber optik.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/CCCCCC/000000?text=Kunjungan+ke+BTS" class="w-full h-48 object-cover" alt="Kunjungan BTS">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Kunjungan ke BTS Provider</h3>
                        <p class="text-gray-700 text-sm">Melihat langsung perangkat transmisi dan akses di Base Transceiver Station.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/EEEEEE/000000?text=Sertifikasi+Jaringan" class="w-full h-48 object-cover" alt="Sertifikasi">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Sertifikasi Kompetensi</h3>
                        <p class="text-gray-700 text-sm">Bekerja sama dengan BNSP untuk sertifikasi teknisi jaringan akses.</p>
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
                        <span>Apa bedanya TJAT dengan TKJ?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Sederhananya: TKJ fokus pada jaringan di dalam gedung/perusahaan (LAN, server, WiFi internal). TJAT fokus pada jaringan di luar gedung yang menghubungkan provider ke pelanggan (Fiber optik ke rumah, tower seluler, kabel tembaga).
                        </p>
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Apakah lulusan TJAT banyak dibutuhkan?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Sangat dibutuhkan. Seiring masifnya pembangunan infrastruktur internet (seperti Palapa Ring dan program FTTH) dan pengembangan 5G, kebutuhan akan teknisi jaringan akses telekomunikasi sangat tinggi, terutama di perusahaan provider internet (ISP) dan kontraktor telekomunikasi.
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
    </script>

</body>
</html>
<?php include 'include/footer.php' ?>