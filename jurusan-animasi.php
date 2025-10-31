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
    <title>Jurusan Animasi - SMK TI Garuda Nusantara</title>
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

    <section class="relative h-64 sm:h-80 lg:h-96">
        <img src="https://placehold.co/1400x450/4a0076/FFFFFF?text=Dunia+Animasi" alt="Hero Jurusan Animasi" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white text-center px-4">Animasi</h1>
        </div>
    </section>

    <section class="container mx-auto px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">Tentang Animasi</h2>
        <div class="flex flex-col md:flex-row items-center gap-10">
            <div class="md:w-1/2 text-gray-700 leading-relaxed">
                <p class="mb-4">
                    Jurusan Animasi di SMK TI Garuda Nusantara adalah kawah candradimuka bagi para kreator visual masa depan. Siswa akan menyelami proses 'menghidupkan' gambar, mulai dari konsep cerita, desain karakter, hingga menjadi sebuah karya film animasi 2D maupun 3D yang utuh.
                </p>
                <p>
                    Kami membekali siswa dengan standar industri, menggunakan perangkat lunak profesional untuk modeling, texturing, rigging, animating, dan compositing. Lulusan dipersiapkan untuk berkarir di studio animasi, rumah produksi film, industri game, atau sebagai freelance animator.
                </p>
            </div>
            <div class="md:w-1/2">
                <img src="https://placehold.co/600x400/c77dff/000000?text=Studio+Animasi+3D" alt="Siswa Animasi" class="w-full rounded-lg shadow-lg">
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Kurikulum</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="border-l-4 border-blue-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Pertama</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Dasar Seni Rupa (Nirmana)</li>
                        <li>Gambar & Sketsa Dasar</li>
                        <li>Prinsip Dasar Animasi (12 Principles)</li>
                        <li>Animasi 2D Dasar</li>
                    </ul>
                </div>
                <div class="border-l-4 border-green-600 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Kedua</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Desain Karakter</li>
                        <li>Storyboarding & Sinematografi</li>
                        <li>Modeling 3D (Hard Surface & Organic)</li>
                        <li>Texturing & Lighting 3D</li>
                    </ul>
                </div>
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Tahun Ketiga</h3>
                    <ul class="list-disc list-inside text-gray-700 space-y-2">
                        <li>Rigging & Animasi Karakter 3D</li>
                        <li>Visual Effects (VFX) & Compositing</li>
                        <li>Manajemen Proyek Animasi</li>
                        <li>Praktek Kerja Industri</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Kegiatan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/DDDDDD/000000?text=Workshop+Industri" class="w-full h-48 object-cover" alt="Workshop Animasi">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Workshop dengan Praktisi</h3>
                        <p class="text-gray-700 text-sm">Belajar langsung dari animator profesional di studio ternama.</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/CCCCCC/000000?text=Festival+Film" class="w-full h-48 object-cover" alt="Festival Film">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Festival Film Animasi Sekolah</h3>
                        <p class="text-gray-700 text-sm">Menampilkan karya-karya terbaik siswa dalam ajang screening internal.</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden transition-transform duration-300 hover:scale-105">
                    <img src="https://placehold.co/600x400/EEEEEE/000000?text=Kunjungan+Studio" class="w-full h-48 object-cover" alt="Kunjungan Studio">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Kunjungan ke Studio Animasi</h3>
                        <p class="text-gray-700 text-sm">Melihat pipeline produksi dan budaya kerja di industri animasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16">
        <div class="container mx-auto px-6 max-w-3xl">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">FAQ</h2>
            <div class="space-y-4">
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Apakah harus bisa menggambar untuk masuk Animasi?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Kemampuan menggambar adalah nilai tambah yang besar, terutama untuk 2D dan desain karakter. Namun, bukan syarat mutlak. Animasi 3D memiliki banyak peran teknis (spt. rigging, lighting, VFX) yang tidak selalu butuh keahlian menggambar. Yang terpenting adalah kemauan belajar dan kreativitas.
                        </p>
                    </div>
                </div>
                <div class="border rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100">
                        <span>Software apa yang akan dipelajari?</span>
                        <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden">
                        <p class="p-5 pt-0 text-gray-700">
                            Siswa akan belajar menggunakan software standar industri seperti Blender untuk 3D (Modeling, Animating, Rigging), Adobe Animate/Toon Boom Harmony untuk 2D, dan Adobe After Effects untuk compositing dan visual effects.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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