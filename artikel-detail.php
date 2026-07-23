<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/koneksi.php';

// Get article ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get article details
$query = "SELECT * FROM berita WHERE id = ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$article = mysqli_fetch_assoc($result);

// Redirect if article not found
if (!$article) {
    header('Location: article.php');
    exit();
}

// Fetch main image
$all_photos = [];
$raw_gambar = $article['gambar'];
if (strpos($raw_gambar, 'http') === 0) {
    $all_photos[] = htmlspecialchars($raw_gambar);
} else if (!empty($raw_gambar)) {
    $all_photos[] = 'berita/' . htmlspecialchars($raw_gambar);
}

// Fetch gallery images
$q_galeri = "SELECT gambar FROM berita_galeri WHERE berita_id = ?";
$stmt_galeri = mysqli_prepare($db, $q_galeri);
mysqli_stmt_bind_param($stmt_galeri, "i", $id);
mysqli_stmt_execute($stmt_galeri);
$res_galeri = mysqli_stmt_get_result($stmt_galeri);
while ($row = mysqli_fetch_assoc($res_galeri)) {
    $raw_gal = $row['gambar'];
    if (strpos($raw_gal, 'http') === 0) {
        $all_photos[] = htmlspecialchars($raw_gal);
    } else if (!empty($raw_gal)) {
        $all_photos[] = 'berita/' . htmlspecialchars($raw_gal);
    }
}

if (empty($all_photos)) {
    $all_photos[] = 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';
}

// Encode to JSON for JS manipulation
$photos_json = json_encode($all_photos);

include 'include/nav.php';
?>

<main class="flex-grow bg-slate-50 min-h-screen pt-8 pb-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Back Button -->
        <a href="article.php" class="inline-flex items-center text-slate-500 hover:text-brand-navy font-semibold text-sm transition-colors mb-8">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Artikel
        </a>

        <article class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            
            <!-- KOMPAS STYLE MULTI-PHOTO GALLERY (OPTION B) -->
            <div class="w-full bg-slate-900 flex flex-col p-4 md:p-6 pb-4">
                <!-- Main Large Photo -->
                <div class="relative w-full h-[300px] md:h-[500px] bg-black rounded-xl overflow-hidden mb-4 shadow-lg group">
                    <img id="main-photo" src="<?= $all_photos[0] ?>" alt="<?= htmlspecialchars($article['judul']) ?>" class="w-full h-full object-contain transition-opacity duration-300">
                    
                    <?php if (count($all_photos) > 1): ?>
                        <button onclick="prevPhoto()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/80 backdrop-blur-md rounded-full flex items-center justify-center text-white transition-all opacity-0 group-hover:opacity-100">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <button onclick="nextPhoto()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/80 backdrop-blur-md rounded-full flex items-center justify-center text-white transition-all opacity-0 group-hover:opacity-100">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <div class="absolute top-4 right-4 bg-black/70 backdrop-blur-sm text-white px-3 py-1.5 rounded-lg text-xs font-bold tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-camera"></i>
                            <span id="photo-counter">1 / <?= count($all_photos) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Thumbnails -->
                <?php if (count($all_photos) > 1): ?>
                <div class="flex overflow-x-auto gap-3 pb-2 scrollbar-none snap-x" id="thumbnail-container">
                    <?php foreach ($all_photos as $index => $img): ?>
                        <div onclick="setPhoto(<?= $index ?>)" id="thumb-<?= $index ?>" class="thumbnail-item shrink-0 w-24 h-16 md:w-32 md:h-20 rounded-lg overflow-hidden cursor-pointer snap-start border-2 transition-all <?= $index === 0 ? 'border-brand-accent opacity-100' : 'border-transparent opacity-50 hover:opacity-100' ?>">
                            <img src="<?= $img ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content Area -->
            <div class="p-6 md:p-12">
                <div class="flex items-center gap-4 text-sm font-bold text-slate-400 mb-6">
                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar"></i> <?= date('d M Y', strtotime($article['tanggal'])) ?></span>
                    <span>•</span>
                    <span class="flex items-center gap-1.5"><i class="fa-regular fa-user"></i> <?= htmlspecialchars($article['penulis']) ?></span>
                </div>

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight mb-10">
                    <?= htmlspecialchars($article['judul']) ?>
                </h1>

                <div class="prose prose-slate prose-lg max-w-none text-slate-600 leading-relaxed">
                    <?php
                    $content = str_replace(['\r\n', '\n'], "\n", $article['isi']); // Normalize line breaks
                    $sections = array_map('trim', explode('##', $content));

                    // Print main content
                    if (!empty($sections[0])) {
                        echo '<p class="mb-6">' . nl2br(htmlspecialchars($sections[0])) . '</p>';
                    }

                    // Print sub-sections
                    for ($i = 1; $i < count($sections); $i++) {
                        $lines = explode("\n", $sections[$i]);
                        $subtitle = trim(array_shift($lines));
                        $body = trim(implode("\n", $lines));

                        echo '<h2 class="text-2xl font-bold text-slate-800 mt-10 mb-4">' . htmlspecialchars($subtitle) . '</h2>';
                        echo '<p class="mb-6">' . nl2br(htmlspecialchars($body)) . '</p>';
                    }
                    ?>
                </div>
                
                <?php if (!empty($article['tags'])): ?>
                <div class="mt-12 pt-8 border-t border-slate-100 flex flex-wrap gap-2">
                    <?php 
                    $tags = array_map('trim', explode(',', $article['tags']));
                    foreach ($tags as $tag): if(!empty($tag)):
                    ?>
                        <span class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                            <?= htmlspecialchars($tag) ?>
                        </span>
                    <?php endif; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </article>
    </div>
</main>

<script>
    const photos = <?= $photos_json ?>;
    const totalPhotos = photos.length;
    let currentIndex = 0;
    let autoplayInterval;

    const mainPhoto = document.getElementById('main-photo');
    const photoCounter = document.getElementById('photo-counter');
    
    function setPhoto(index) {
        if (totalPhotos <= 1) return;
        
        currentIndex = index;
        
        // Fade effect
        mainPhoto.style.opacity = 0.5;
        setTimeout(() => {
            mainPhoto.src = photos[currentIndex];
            mainPhoto.style.opacity = 1;
        }, 150);

        if (photoCounter) {
            photoCounter.innerText = (currentIndex + 1) + " / " + totalPhotos;
        }

        // Update thumbnails
        document.querySelectorAll('.thumbnail-item').forEach((thumb, i) => {
            if (i === currentIndex) {
                thumb.classList.add('border-brand-accent', 'opacity-100');
                thumb.classList.remove('border-transparent', 'opacity-50');
                // Scroll thumbnail into view
                thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                thumb.classList.remove('border-brand-accent', 'opacity-100');
                thumb.classList.add('border-transparent', 'opacity-50');
            }
        });
        
        resetAutoplay();
    }

    function nextPhoto() {
        let newIndex = currentIndex + 1;
        if (newIndex >= totalPhotos) newIndex = 0;
        setPhoto(newIndex);
    }

    function prevPhoto() {
        let newIndex = currentIndex - 1;
        if (newIndex < 0) newIndex = totalPhotos - 1;
        setPhoto(newIndex);
    }

    function startAutoplay() {
        if (totalPhotos > 1) {
            autoplayInterval = setInterval(nextPhoto, 4000); // Change every 4 seconds
        }
    }

    function resetAutoplay() {
        clearInterval(autoplayInterval);
        startAutoplay();
    }

    // Initialize autoplay
    if (totalPhotos > 1) {
        startAutoplay();
        
        // Pause autoplay on hover
        const photoContainer = document.querySelector('.bg-slate-900');
        if (photoContainer) {
            photoContainer.addEventListener('mouseenter', () => clearInterval(autoplayInterval));
            photoContainer.addEventListener('mouseleave', startAutoplay);
        }
    }
</script>

<?php include 'include/footer.php'; ?>