<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

// Fetch all ekskuls that have at least one photo
$ekskuls = [];
$stmt = $db->prepare("
    SELECT e.id, e.slug, e.nama 
    FROM ekstrakulikuler e
    JOIN ekskul_galeri g ON e.id = g.ekskul_id
    GROUP BY e.id
    ORDER BY e.nama ASC
");
$stmt->execute();
$res_ekskul = $stmt->get_result();
while ($row = $res_ekskul->fetch_assoc()) {
    $ekskuls[] = $row;
}

// Fetch all photos with their ekskul slug and name
$photos = [];
$stmt_photos = $db->prepare("
    SELECT g.id, g.gambar, e.slug, e.nama
    FROM ekskul_galeri g
    JOIN ekstrakulikuler e ON g.ekskul_id = e.id
    ORDER BY RAND() -- Randomize to mix photos on initial load
");
$stmt_photos->execute();
$res_photos = $stmt_photos->get_result();
while ($row = $res_photos->fetch_assoc()) {
    $photos[] = $row;
}

// Check if there is an active filter from URL
$active_filter = isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : 'semua';

include 'include/nav.php';
?>

    <main class="flex-grow bg-slate-50 min-h-screen pt-12 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- HEADER -->
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-4">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">Galeri Ekstrakulikuler</h1>
                <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                    Kumpulan momen terbaik dan dokumentasi kegiatan dari berbagai ekstrakulikuler di SMPN Cimahi.
                </p>
            </div>

            <!-- FILTER BUTTONS -->
            <div class="flex overflow-x-auto snap-x snap-mandatory gap-3 mb-12 pb-4 scrollbar-none" id="filter-container">
                <button data-filter="semua" 
                        class="filter-btn shrink-0 snap-center px-6 py-2 rounded-full font-bold text-sm transition-all shadow-sm border <?= $active_filter === 'semua' ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900' ?>">
                    Semua
                </button>
                
                <?php foreach ($ekskuls as $eks): ?>
                    <button data-filter="<?= htmlspecialchars($eks['slug']) ?>" 
                            class="filter-btn shrink-0 snap-center px-6 py-2 rounded-full font-bold text-sm transition-all shadow-sm border <?= $active_filter === $eks['slug'] ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100 hover:text-slate-900' ?>">
                        <?= htmlspecialchars($eks['nama']) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- MASONRY/GRID GALLERY -->
            <?php if (count($photos) > 0): ?>
                <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6" id="gallery-grid">
                    <?php foreach ($photos as $photo): ?>
                        <div class="gallery-item break-inside-avoid rounded-2xl overflow-hidden shadow-md border border-slate-200 group cursor-pointer relative" 
                             data-category="<?= htmlspecialchars($photo['slug']) ?>"
                             onclick="openPhotoModal(this)">
                            <img src="<?= htmlspecialchars($photo['gambar']) ?>" 
                                 alt="Galeri <?= htmlspecialchars($photo['nama']) ?>" 
                                 class="w-full object-cover group-hover:scale-105 transition-transform duration-500 bg-slate-100">
                            <!-- Overlay Badge on Hover -->
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                                <span class="bg-brand-navy text-white text-[11px] font-semibold px-3 py-1.5 rounded-full shadow-sm backdrop-blur-md">
                                    <?= htmlspecialchars($photo['nama']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- EMPTY STATE (Hidden by default) -->
                <div id="empty-state" class="hidden text-center py-20">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                        <i class="fa-solid fa-image text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Tidak ada foto</h3>
                    <p class="text-slate-500 mt-2">Belum ada dokumentasi untuk kategori ini.</p>
                </div>
            <?php else: ?>
                <div class="text-center py-20">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                        <i class="fa-solid fa-image text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Galeri Masih Kosong</h3>
                    <p class="text-slate-500 mt-2">Belum ada dokumentasi ekstrakulikuler yang diunggah.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <!-- Modal Fullscreen Image -->
    <div id="photo-modal" class="fixed inset-0 z-[100] bg-black/95 hidden opacity-0 transition-opacity duration-300 flex-col items-center justify-center">
        <button onclick="closePhotoModal()" class="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark text-2xl"></i>
        </button>
        <div class="w-full max-w-5xl px-4 flex flex-col items-center justify-center h-full gap-4">
            <img id="photo-modal-img" src="" alt="Galeri Layar Penuh" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl scale-95 transition-transform duration-300">
            <div id="photo-modal-caption" class="text-white text-sm font-semibold tracking-wide bg-white/10 px-4 py-2 rounded-full backdrop-blur-md"></div>
        </div>
    </div>

    <!-- JS for Filtering and Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');
            const emptyState = document.getElementById('empty-state');
            
            // Function to apply filter visually
            const applyFilter = (filterValue) => {
                let visibleCount = 0;
                
                galleryItems.forEach(item => {
                    if (filterValue === 'semua' || item.getAttribute('data-category') === filterValue) {
                        item.style.display = 'block';
                        // Add a slight fade-in animation
                        item.style.animation = 'fadeIn 0.5s ease forwards';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                if (emptyState) {
                    if (visibleCount === 0) {
                        emptyState.classList.remove('hidden');
                    } else {
                        emptyState.classList.add('hidden');
                    }
                }
            };
            
            // Initial filter application (if URL has ?filter=...)
            const activeFilterOnLoad = '<?= $active_filter ?>';
            applyFilter(activeFilterOnLoad);

            // Filter button click event
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Update active button styling
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-brand-navy', 'text-white', 'border-brand-navy');
                        b.classList.add('bg-white', 'text-slate-600', 'border-slate-200');
                    });
                    
                    this.classList.add('bg-brand-navy', 'text-white', 'border-brand-navy');
                    this.classList.remove('bg-white', 'text-slate-600', 'border-slate-200');
                    
                    // Apply filtering
                    applyFilter(filterValue);
                    
                    // Optional: Update URL without reloading to allow sharing the filtered link
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('filter', filterValue);
                    window.history.pushState({}, '', newUrl);
                });
            });
        });

        // Photo Modal Logic
        function openPhotoModal(element) {
            const modal = document.getElementById('photo-modal');
            const modalImg = document.getElementById('photo-modal-img');
            const modalCaption = document.getElementById('photo-modal-caption');
            const sourceImg = element.querySelector('img');
            const badgeText = element.querySelector('span').innerText;
            
            modalImg.src = sourceImg.src;
            modalCaption.innerText = badgeText;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
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

        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                const modal = document.getElementById('photo-modal');
                if (!modal.classList.contains('hidden')) {
                    closePhotoModal();
                }
            }
        });
    </script>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

<?php include 'include/footer.php'; ?>
