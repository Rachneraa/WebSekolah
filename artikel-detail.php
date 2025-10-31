<?php
session_start();
require_once 'config/koneksi.php';
require_once 'config/functions.php';

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
?>
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
    <style>
        .article-container {
            max-width: 800px;
            margin: 80px auto;
            padding: 0 20px;
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .article-meta {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 1rem;
        }

        .article-image {
            margin: 2rem 0;
            text-align: center;
        }

        .article-image img {
            max-width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }

        .article-content {
            line-height: 1.8;
            font-size: 1.1rem;
            font-weight: 400;
        }

        .article-subtitle {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }

        .main-content,
        .sub-content {
            margin-bottom: 2rem;
        }

        .article-tags {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }

        .tag-badge {
            background: #e9ecef;
            color: #495057;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-right: 0.5rem;
            text-decoration: none;
        }

        .back-button {
            color: #6c757d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .back-button:hover {
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>

    <main class="article-container">
        <a href="article.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Kembali ke Artikel
        </a>

        <article>
            <h1 class="article-title"><?= htmlspecialchars($article['judul']) ?></h1>

            <div class="article-meta">
                <i class="far fa-calendar"></i> <?= date('d M Y', strtotime($article['tanggal'])) ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <i class="far fa-user"></i> <?= htmlspecialchars($article['penulis']) ?>
            </div>

            <?php if ($article['gambar']): ?>
                <div class="article-image">
                    <img src="berita/<?= htmlspecialchars($article['gambar']) ?>"
                        alt="<?= htmlspecialchars($article['judul']) ?>">
                </div>
            <?php endif; ?>

            <div class="article-content">
                <?php
                $content = str_replace(['\r\n', '\n'], "\n", $article['isi']); // Normalize line breaks
                $sections = array_map('trim', explode('##', $content));

                // Print main content
                if (!empty($sections[0])) {
                    echo '<div class="main-content">';
                    echo nl2br(htmlspecialchars($sections[0]));
                    echo '</div>';
                }

                // Print sections with subheadings
                for ($i = 1; $i < count($sections); $i++) {
                    if (!empty($sections[$i])) {
                        $parts = explode("\n", $sections[$i], 2);
                        if (count($parts) > 1) {
                            // Sub judul
                            echo "<h2 class='article-subtitle'>" . htmlspecialchars(trim($parts[0])) . "</h2>";
                            // Konten sub judul
                            echo "<div class='sub-content'>";
                            echo nl2br(htmlspecialchars(trim($parts[1])));
                            echo "</div>";
                        } else {
                            // If no line break after ##, just show as content
                            echo "<div class='sub-content'>";
                            echo nl2br(htmlspecialchars(trim($sections[$i])));
                            echo "</div>";
                        }
                    }
                }
                ?>
            </div>

            <?php if (!empty($article['tags'])): ?>
                <div class="article-tags">
                    <?php
                    $tags = explode(',', $article['tags']);
                    foreach ($tags as $tag):
                        ?>
                        <a href="article.php?tag=<?= urlencode(trim($tag)) ?>" class="tag-badge">
                            #<?= htmlspecialchars(trim($tag)) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    </main>

    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// SERVICE WORKER REGISTRATION
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .then(registration => console.log('SW Registered'))
    .catch(error => console.log('SW Registration failed:', error));
}
    </script>

</body>

</html>