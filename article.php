<?php
session_start();
require_once 'config/koneksi.php';
require_once 'config/functions.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artikel & Berita - SMK TI Garuda Nusantara</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .article-card {
            transition: transform 0.2s;
            height: 100%;
        }

        .article-card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-text {
            color: #666;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .article-meta {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 1rem;
        }

        .btn-primary {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2>Artikel & Berita</h2>
                <div class="input-group mb-3 mt-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari artikel...">
                    <button class="btn btn-primary" onclick="searchArticles()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row" id="articleGrid">
            <?php
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 9;
            $offset = ($page - 1) * $limit;

            $query = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT $offset, $limit";
            $result = mysqli_query($db, $query);

            if (mysqli_num_rows($result) > 0):
                while ($article = mysqli_fetch_assoc($result)):
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card article-card">
                            <?php if ($article['gambar']): ?>
                                <img src="berita/<?= htmlspecialchars($article['gambar']) ?>" class="card-img-top"
                                    alt="<?= htmlspecialchars($article['judul']) ?>">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="article-meta">
                                    <i class="far fa-calendar"></i> <?= date('d M Y', strtotime($article['tanggal'])) ?>
                                    <?php if ($article['penulis']): ?>
                                        <span class="ms-2">
                                            <i class="far fa-user"></i> <?= htmlspecialchars($article['penulis']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title"><?= htmlspecialchars($article['judul']) ?></h5>
                                <p class="card-text flex-grow-1">
                                    <?= substr(strip_tags(str_replace(['\r\n', '\n', '##'], ' ', $article['isi'])), 0, 150) ?>...
                                </p>
                                <a href="article-detail.php?id=<?= $article['id'] ?>" class="btn btn-primary mt-auto">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
            else:
                ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        Belum ada artikel yang tersedia.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php
        $query = "SELECT COUNT(*) as total FROM berita";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $total_pages = ceil($row['total'] / $limit);

        if ($total_pages > 1):
            ?>
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'include/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function searchArticles() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const articles = document.querySelectorAll('.col-md-4');

            articles.forEach(article => {
                const title = article.querySelector('.card-title').textContent.toLowerCase();
                const content = article.querySelector('.card-text').textContent.toLowerCase();

                if (title.includes(searchTerm) || content.includes(searchTerm)) {
                    article.style.display = '';
                } else {
                    article.style.display = 'none';
                }
            });
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchArticles();
            }
        });
    </script>
</body>

</html>