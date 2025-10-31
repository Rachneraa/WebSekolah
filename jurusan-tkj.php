<?php include 'include/nav.php' ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurusan TKJ - SMK TI Garuda Nusantara</title>
    <!-- Memuat Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Memuat Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Style untuk transisi accordion FAQ */
        .faq-content {
            transition: max-height 0.3s ease-out, padding 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- 1. Hero Section -->
    <section class="relative h-64 sm:h-80 lg:h-96">
        <!-- Gunakan URL gambar hero yang sesuai -->
        <img src="https://placehold.co/1400x450/003366/FFFFFF?text=Teknik+Komputer+dan+Jaringan" alt="Hero Jurusan TKJ" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white text-center px-4">Teknik Komputer & Jaringan</h1>
        </div>
    </section>

    <!-- 2. Tentang Jurusan Section -->
    <section class="container mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Tentang TKJ</h2>
        <div class="flex flex-col md:flex-row items-center gap-10">
            <!-- Deskripsi Teks -->
            <div class="md:w-1/2 text-gray-700 leading-relaxed">
                <p class="mb-4">
                    Jurusan Teknik Komputer dan Jaringan (TKJ) di SMK TI Garuda Nusantara adalah program keahlian yang membekali siswa dengan keterampilan mendalam di bidang infrastruktur teknologi informasi. Siswa tidak hanya belajar merakit komputer, tetapi juga merancang, membangun, dan mengelola jaringan skala kecil hingga menengah.
                </p>
                <p>
                    Fokus utama kami adalah pada administrasi jaringan, keamanan siber, dan pengelolaan server. Lulusan TKJ dipersiapkan untuk menjadi teknisi jaringan, administrator sistem, atau spesialis IT support yang kompeten dan siap menghadapi tantangan industri digital.
                </p>
            </div>
            <!-- Gambar Jurusan -->
            <div class="md:w-1/2">
                <img src="https://placehold.co/600x400/F0B90B/000000?text=Lab+TKJ" alt="Siswa TKJ" class="w-full rounded-lg shadow-lg">
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
                        <li>Dasar-dasar Jaringan Komputer</li>
                        <li>Perakitan dan Perawatan PC</li>
                        <li>Sistem Operasi Jaringan</li>
                        <li>Pemrograman Dasar</li>
                    </ul>
                </div>
                <!-- Tahun Kedua -->
                <div class="border-l-4 border-green-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Kedua</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Administrasi Jaringan (Router & Switch)</li>
                        <li>Keamanan Jaringan</li>
                        <li>Jaringan Nirkabel (Wireless)</li>
                        <li>Basis Data</li>
                    </ul>
                </div>
                <!-- Tahun Ketiga -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Ketiga</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Administrasi Server (Linux/Windows)</li>
                        <li>Layanan Jaringan (DNS, Web, Mail Server)</li>
                        <li>Manajemen Proyek IT</li>
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
                    <img src="https://placehold.co/600x400/DDDDDD/000000?text=Workshop+Mikrotik" class="w-full h-48 object-cover" alt="Workshop Mikrotik">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Workshop Sertifikasi Mikrotik</h3>
                        <p class="text-gray-700 text-sm">Siswa dibekali pelatihan intensif untuk mendapatkan sertifikasi MTCNA.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/CCCCCC/000000?text=Lomba+Networking" class="w-full h-48 object-cover" alt="Lomba Networking">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Lomba Kompetensi Siswa (LKS)</h3>
                        <p class="text-gray-700 text-sm">Partisipasi aktif dalam LKS bidang IT Network Systems Administration.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/EEEEEE/000000?text=Kunjungan+Industri" class="w-full h-48 object-cover" alt="Kunjungan Industri">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Kunjungan Industri ke Data Center</h3>
                        <p class="text-gray-700 text-sm">Melihat langsung infrastruktur data center skala besar milik provider.</p>
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
                        <span>Apa prospek kerja lulusan TKJ?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Lulusan TKJ memiliki prospek kerja yang sangat luas, seperti Network Administrator, IT Support, Teknisi Jaringan, System Administrator, hingga Cyber Security Analyst di berbagai perusahaan.
                        </p>
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Apakah TKJ hanya belajar merakit komputer?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Tidak. Merakit komputer hanyalah salah satu materi dasar di tahun pertama. Fokus utama TKJ adalah pada desain, implementasi, dan manajemen jaringan, serta administrasi server dan keamanan.
                        </p>
                    </div>
                </div>
                <!-- FAQ Item 3 -->
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Sertifikasi apa yang bisa didapat?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Kami memfasilitasi siswa untuk mengambil sertifikasi profesional yang diakui industri, seperti Mikrotik (MTCNA) dan sertifikasi lain di bidang jaringan dan server.
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

                // Toggle konten yang diklik
                if (content.style.maxHeight) {
                    // Tutup
                    content.style.maxHeight = null;
                    content.style.paddingTop = '0px';
                    content.style.paddingBottom = '0px';
                    icon.classList.remove('rotate-180');
                } else {
                    // Buka
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.paddingTop = '0px'; // Dihapus karena padding sudah di <p>
                    content.style.paddingBottom = '1.25rem'; // Sesuaikan dengan p-5 (pt-0)
                    icon.classList.add('rotate-180');
                }

                // (Opsional) Tutup FAQ lain saat satu dibuka
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