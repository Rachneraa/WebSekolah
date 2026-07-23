<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

$slug = isset($_GET['type']) ? $_GET['type'] : '';

if (empty($slug)) {
    header('Location: index.php#ekstra');
    exit;
}

$stmt = $db->prepare("SELECT * FROM ekstrakulikuler WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php#ekstra');
    exit;
}
$ekskul = $result->fetch_assoc();
$ekskul_id = $ekskul['id'];

// Fetch Prestasi
$prestasi = [];
$stmt_prestasi = $db->prepare("SELECT * FROM ekskul_prestasi WHERE ekskul_id = ?");
$stmt_prestasi->bind_param("i", $ekskul_id);
$stmt_prestasi->execute();
$res_prestasi = $stmt_prestasi->get_result();
while ($row = $res_prestasi->fetch_assoc()) {
    $prestasi[] = $row;
}

// Fetch Galeri
$galeri = [];
$stmt_galeri = $db->prepare("SELECT * FROM ekskul_galeri WHERE ekskul_id = ?");
$stmt_galeri->bind_param("i", $ekskul_id);
$stmt_galeri->execute();
$res_galeri = $stmt_galeri->get_result();
while ($row = $res_galeri->fetch_assoc()) {
    $galeri[] = $row;
}

$col1_images = array_slice($galeri, 0, 2);
$col2_images = array_slice($galeri, 2);

include 'include/nav.php';
?>

    <main class="flex-grow">
        
        <!-- HERO DETAIL SECTION MATCHING MOCKUP IMAGE -->
        <section class="py-12 md:py-20 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-y-6 md:gap-8 items-center">
                    
                    <!-- TITLE AREA (Mobile: 1st, Tablet/Desktop: Left Top) -->
                    <div class="order-1 md:col-span-6 md:col-start-1 md:row-start-1 space-y-4 text-center md:text-left">
                        <span id="ekskul-badge" class="inline-block px-3 py-1 bg-brand-softBlue text-brand-blue text-xs font-bold rounded-full uppercase tracking-wider">
                            <?= htmlspecialchars($ekskul['kategori']) ?>
                        </span>

                        <h1 id="ekskul-title" class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                            Ekstrakulikuler <?= htmlspecialchars($ekskul['nama']) ?>
                        </h1>
                    </div>

                    <!-- IMAGE AREA (Mobile: 2nd, Tablet/Desktop: Right Full) -->
                    <div class="order-2 md:col-span-6 md:col-start-7 md:row-start-1 md:row-span-2 flex justify-center md:justify-end">
                        <div class="relative w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl border border-slate-100 mt-4 md:mt-0">
                            <img id="ekskul-hero-img" 
                                 src="<?= htmlspecialchars($ekskul['gambar_hero']) ?>" 
                                 alt="Ekstrakulikuler <?= htmlspecialchars($ekskul['nama']) ?> SMPN 16 Cimahi" 
                                 class="w-full h-[360px] sm:h-[420px] object-cover object-center transform hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>

                    <!-- DESC & BUTTON AREA (Mobile: 3rd, Tablet/Desktop: Left Bottom) -->
                    <div class="order-3 md:col-span-6 md:col-start-1 md:row-start-2 space-y-6 text-center md:text-left">
                        <p id="ekskul-desc" class="text-base sm:text-lg text-slate-600 leading-relaxed">
                            <?= htmlspecialchars($ekskul['deskripsi']) ?>
                        </p>

                        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center md:justify-start gap-4">
                            <a href="pendaftaran.php" class="w-full sm:w-auto px-8 py-3 bg-brand-navy hover:bg-brand-blue text-white font-bold text-sm rounded-xl shadow transition-all duration-200 text-center">
                                Bergabung Sekarang
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- GURU PEMBIMBING SECTION MATCHING MOCKUP IMAGE -->
        <section class="py-16 md:py-24 bg-brand-lightBg border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
                
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Guru pembimbing</h2>

                <div class="flex flex-col items-center justify-center max-w-sm mx-auto space-y-4">
                    <!-- INSTRUCTOR CUTOUT IMAGE CONTAINER -->
                    <div class="relative w-48 h-48 sm:w-56 sm:h-56 rounded-full overflow-hidden border-4 border-white shadow-xl bg-slate-200">
                        <img id="instructor-img" 
                             src="<?= htmlspecialchars($ekskul['pembina_foto']) ?>" 
                             alt="<?= htmlspecialchars($ekskul['pembina_nama']) ?> - Guru Pembimbing" 
                             class="w-full h-full object-cover">
                    </div>

                    <div class="space-y-1">
                        <h3 id="instructor-name" class="text-2xl sm:text-3xl font-extrabold text-slate-900"><?= htmlspecialchars($ekskul['pembina_nama']) ?></h3>
                        <p id="instructor-role" class="text-sm font-semibold text-slate-500"><?= htmlspecialchars($ekskul['pembina_role']) ?></p>
                        <p id="instructor-schedule" class="text-xs text-brand-blue font-bold pt-1"><i class="fa-regular fa-clock"></i> Jadwal Latihan: <?= htmlspecialchars($ekskul['jadwal']) ?></p>
                    </div>
                </div>

            </div>
        </section>

        <?php if (count($prestasi) > 0): ?>
        <!-- PRESTASI EKSTRAKULIKULER SECTION MATCHING MOCKUP IMAGE -->
        <section class="py-16 md:py-24 bg-gradient-to-b from-white to-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Prestasi Ekstrakulikuler</h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Dedikasi dan kerja keras siswa-siswi kami dalam berbagai bidang ekstrakulikuler telah membuahkan hasil yang membanggakan di tingkat regional dan nasional.
                    </p>
                </div>

                <!-- CARDS MATCHING MOCKUP IMAGE -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($prestasi as $p): ?>
                    <!-- PRESTASI CARD -->
                    <div class="bg-white rounded-2xl p-8 border border-slate-200/80 shadow-md text-center space-y-4 hover:shadow-xl transition-shadow duration-300 flex flex-col items-center justify-between">
                        <div class="w-14 h-14 rounded-full bg-brand-navy text-white flex items-center justify-center text-xl shadow-md">
                            <i class="fa-solid <?= htmlspecialchars($p['icon']) ?>"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-extrabold text-slate-900 text-lg sm:text-xl"><?= htmlspecialchars($p['judul']) ?></h3>
                            <p class="text-slate-500 text-xs sm:text-sm"><?= htmlspecialchars($p['deskripsi']) ?></p>
                        </div>
                        <div class="w-12 h-1 bg-amber-500 rounded-full mt-2"></div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>
        <?php endif; ?>

        <?php if (count($galeri) > 0): ?>
        <!-- KOLEKSI FOTO SECTION MATCHING MOCKUP IMAGE -->
        <section class="py-16 md:py-24 bg-white border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 text-center tracking-tight">Koleksi Foto</h2>

                <!-- ASYMMETRIC / GRID PHOTO GALLERY MATCHING MOCKUP -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="space-y-6">
                        <?php foreach ($col1_images as $img): ?>
                        <div onclick="openPhotoModal(this)" class="rounded-2xl overflow-hidden shadow-md border border-slate-200 group cursor-pointer h-64 sm:h-72">
                            <img src="<?= htmlspecialchars($img['gambar']) ?>" alt="Galeri <?= htmlspecialchars($ekskul['nama']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="space-y-6">
                        <?php foreach ($col2_images as $img): ?>
                        <div onclick="openPhotoModal(this)" class="rounded-2xl overflow-hidden shadow-md border border-slate-200 group cursor-pointer h-44 sm:h-48">
                            <img src="<?= htmlspecialchars($img['gambar']) ?>" alt="Galeri <?= htmlspecialchars($ekskul['nama']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <?php endforeach; ?>
                    </div>

                </div>

                <!-- LIHAT SEMUA BUTTON MATCHING MOCKUP -->
                <div class="text-center pt-4">
                    <a href="galeri.php?filter=<?= htmlspecialchars($ekskul['slug']) ?>" 
                       class="px-8 py-3 bg-brand-softBlue hover:bg-brand-mutedBlue text-brand-blue font-bold text-sm rounded-full transition-colors inline-flex items-center gap-2">
                        <span>Lihat semua <i class="fa-solid fa-arrow-right ml-1"></i></span>
                    </a>
                </div>

            </div>
        </section>
        <?php endif; ?>
    </main>

    <!-- Modal Fullscreen Image -->
    <div id="photo-modal" class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex-col items-center justify-center">
        <!-- Close button -->
        <button onclick="closePhotoModal()" class="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>
        <!-- Modal Content -->
        <div class="w-full max-w-5xl px-4 flex items-center justify-center h-full">
            <img id="photo-modal-img" src="" alt="Galeri Layar Penuh" class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300">
        </div>
    </div>

    <!-- JS for Photo Modal -->
    <script>
        function openPhotoModal(element) {
            const modal = document.getElementById('photo-modal');
            const modalImg = document.getElementById('photo-modal-img');
            const sourceImg = element.querySelector('img');
            
            modalImg.src = sourceImg.src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Allow display to apply before fading in
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalImg.classList.remove('scale-95');
                modalImg.classList.add('scale-100');
            }, 10);
            
            document.body.style.overflow = 'hidden';
        }

        function closePhotoModal() {
            const modal = document.getElementById('photo-modal');
            const modalImg = document.getElementById('photo-modal-img');
            
            modal.classList.add('opacity-0');
            modalImg.classList.remove('scale-100');
            modalImg.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modalImg.src = '';
                document.body.style.overflow = '';
            }, 300);
        }

        // Close on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                const modal = document.getElementById('photo-modal');
                if (!modal.classList.contains('hidden')) {
                    closePhotoModal();
                }
            }
        });
    </script>

<?php include 'include/footer.php'; ?>
