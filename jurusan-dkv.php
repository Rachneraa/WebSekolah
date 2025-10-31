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
    <title>Jurusan DKV - SMK TI Garuda Nusantara</title>
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
        <img src="https://placehold.co/1400x450/ff6d00/FFFFFF?text=Desain+Komunikasi+Visual" alt="Hero Jurusan DKV" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white text-center px-4">Desain Komunikasi Visual</h1>
        </div>
    </section>

    <!-- 2. Tentang Jurusan Section -->
    <section class="container mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Tentang DKV</h2>
        <div class="flex flex-col md:flex-row items-center gap-10">
            <!-- Deskripsi Teks -->
            <div class="md:w-1/2 text-gray-700 leading-relaxed">
                <p class="mb-4">
                    Desain Komunikasi Visual (DKV) adalah program keahlian yang mempelajari cara menyampaikan pesan secara visual. Siswa belajar mengubah ide dan informasi menjadi karya desain yang komunikatif, estetis, dan efektif, baik untuk media cetak maupun digital.
                </p>
                <p>
                    Jurusan ini menggabungkan seni, kreativitas, dan teknologi. Siswa akan menguasai software desain grafis, fotografi, videografi, dan ilustrasi untuk menciptakan solusi visual seperti logo, branding, poster, desain web, dan konten media sosial.
                </p>
            </div>
            <!-- Gambar Jurusan -->
            <div class="md:w-1/2">
                <img src="https://placehold.co/600x400/ff9e00/000000?text=Studio+Desain+Grafis" alt="Siswa DKV" class="w-full rounded-lg shadow-lg">
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
                        <li>Dasar-dasar Seni Rupa (Nirmana)</li>
                        <li>Sketsa & Gambar Dasar</li>
                        <li>Tipografi (Seni Huruf)</li>
                        <li>Pengenalan Software Desain</li>
                    </ul>
                </div>
                <!-- Tahun Kedua -->
                <div class="border-l-4 border-green-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Kedua</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Desain Grafis (Logo, Poster, Layout)</li>
                        <li>Fotografi Dasar & Olah Digital</li>
                        <li>Ilustrasi Digital</li>
                        <li>Videografi & Editing Video</li>
                    </ul>
                </div>
                <!-- Tahun Ketiga -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Ketiga</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Branding & Identitas Visual</li>
                        <li>Desain Web (UI/UX Design)</li>
                        <li>Manajemen Proyek Desain</li>
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
                    <img src="https://placehold.co/600x400/DDDDDD/000000?text=Pameran+Karya" class="w-full h-48 object-cover" alt="Pameran DKV">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Pameran Karya "VisualFest"</h3>
                        <p class="text-gray-700 text-sm">Ajang tahunan untuk memamerkan portofolio dan karya terbaik siswa DKV.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 2 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/CCCCCC/000000?text=Workshop+Branding" class="w-full h-48 object-cover" alt="Workshop Branding">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Workshop Re-branding UMKM</h3>
                        <p class="text-gray-700 text-sm">Siswa membantu UMKM lokal membuat identitas visual (logo, kemasan) baru.</p>
                    </div>
                </div>
                <!-- Card Kegiatan 3 -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/EEEEEE/000000?text=Lomba+Desain" class="w-full h-48 object-cover" alt="Lomba Desain">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Lomba Desain Poster Nasional</h3>
                        <p class="text-gray-700 text-sm">Berpartisipasi aktif dan menjuarai berbagai kompetisi desain tingkat nasional.</p>
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
                        <span>Apa bedanya DKV dengan Animasi?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            DKV memiliki cakupan yang lebih luas, termasuk desain grafis, branding, fotografi, dan UI/UX. Animasi adalah salah satu bagian dari DKV, namun di jurusan Animasi, fokusnya jauh lebih mendalam pada proses membuat gambar bergerak (film 2D/3D).
                        </p>
                    </div>
                </div>
                <!-- FAQ Item 2 -->
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Lulusan DKV bisa kerja jadi apa?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Peluangnya sangat banyak di industri kreatif. Lulusan bisa menjadi Graphic Designer, UI/UX Designer, Illustrator, Fotografer, Videografer, Content Creator, atau bekerja di agency periklanan dan digital marketing.
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