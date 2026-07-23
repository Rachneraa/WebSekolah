<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

// Filter parameter
$active_filter = isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : 'semua';

// Base Query
$where = "";
if ($active_filter !== 'semua') {
    // Escape string for security
    $safe_filter = mysqli_real_escape_string($db, $active_filter);
    $where = "WHERE tags LIKE '%$safe_filter%' OR kategori LIKE '%$safe_filter%'";
}

$per_page = 6;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Hitung total berita
$total_query = mysqli_query($db, "SELECT COUNT(*) as total FROM berita $where");
$total_row = mysqli_fetch_assoc($total_query);
$total_berita = $total_row['total'];
$total_pages = ceil($total_berita / $per_page);

// Ambil berita
$query = "
    SELECT b.*, 
           (SELECT COUNT(*) FROM berita_galeri bg WHERE bg.berita_id = b.id) as photo_count
    FROM berita b 
    $where 
    ORDER BY tanggal DESC 
    LIMIT $per_page OFFSET $offset
";
$result = mysqli_query($db, $query);

// helper function for image path
function get_image_src($raw_gambar) {
    if (strpos($raw_gambar, 'http') === 0) {
        return htmlspecialchars($raw_gambar);
    } else if (!empty($raw_gambar)) {
        return 'berita/' . htmlspecialchars($raw_gambar);
    }
    return 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';
}

include 'include/nav.php';
?>

<main class="flex-grow bg-slate-50 min-h-screen pt-12 pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- HEADER & FILTER -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div class="max-w-2xl space-y-3">
                <h1 class="text-4xl md:text-5xl font-extrabold text-brand-navy tracking-tight">Artikel & Berita Terbaru</h1>
            </div>
            
            <!-- HORIZONTALLY SCROLLABLE FILTER BUTTONS (1 COLUMN ON EVERY DEVICE) -->
            <div class="w-full md:w-auto overflow-x-auto scrollbar-none pb-2 -mx-4 px-4 md:mx-0 md:px-0">
                <div class="flex items-center gap-2 min-w-max">
                    <?php
                    $filters = [
                        'semua' => 'SEMUA',
                        'akademik' => 'AKADEMIK',
                        'prestasi' => 'PRESTASI',
                        'kegiatan' => 'KEGIATAN'
                    ];
                    foreach ($filters as $key => $label):
                        $is_active = ($active_filter === $key);
                        $btn_class = $is_active ? 'bg-brand-navy text-white border-brand-navy' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-100 hover:text-slate-800';
                    ?>
                        <a href="?filter=<?= $key ?>" class="px-4 py-1.5 rounded-full border text-[11px] md:text-xs font-bold tracking-wider transition-colors shadow-sm whitespace-nowrap <?= $btn_class ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- NEWS CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($berita = mysqli_fetch_assoc($result)):
                    $judul = htmlspecialchars($berita['judul']);
                    $kategori = !empty($berita['tags']) ? htmlspecialchars(explode(',', $berita['tags'])[0]) : 'Kegiatan';
                    $tanggal = date('d M Y', strtotime($berita['tanggal']));
                    $gambar = get_image_src($berita['gambar']);
                    $isi_bersih = str_replace(["\r", "\n"], ' ', preg_replace('/^##.*$/m', '', $berita['isi']));
                    $excerpt = mb_strimwidth(trim($isi_bersih), 0, 110, '...');
                    ?>
                    
                    <a href="artikel-detail.php?id=<?= $berita['id'] ?>" class="berita-item bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 cursor-pointer group flex flex-col justify-between h-full">
                        <div>
                            <div class="relative h-48 overflow-hidden bg-slate-100 shrink-0">
                                <img src="<?= $gambar ?>" alt="<?= $judul ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-3 left-3 bg-brand-navy text-white text-[11px] font-semibold px-2.5 py-1 rounded"><?= $kategori ?></span>
                                
                                <?php if ($berita['photo_count'] > 0): ?>
                                <!-- Gallery Icon -->
                                <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur-sm text-white px-2.5 py-1 rounded-md flex items-center gap-1.5">
                                    <i class="fa-solid fa-camera text-xs"></i>
                                    <span class="text-[10px] font-bold"><?= $berita['photo_count'] + 1 ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6 space-y-2 flex-grow">
                                <p class="text-xs text-slate-400 font-medium"><?= $tanggal ?></p>
                                <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-brand-accent transition-colors line-clamp-2">
                                    <?= $judul ?>
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
                endwhile;
            } else {
                echo '<div class="col-span-full text-center text-slate-500 py-10">Tidak ada berita untuk kategori ini.</div>';
            }
            ?>
        </div>

        <!-- PAGINATION -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center gap-2">
                <?php if ($page > 1): ?>
                    <a href="?filter=<?= $active_filter ?>&page=<?= $page - 1 ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?filter=<?= $active_filter ?>&page=<?= $i ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border <?= $i == $page ? 'bg-brand-navy text-white border-brand-navy font-bold' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 font-medium' ?> transition-colors text-sm">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?filter=<?= $active_filter ?>&page=<?= $page + 1 ?>" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">Berikutnya</a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php include 'include/footer.php'; ?>