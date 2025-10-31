<?php
session_start();
require_once 'config/koneksi.php';

$per_page = 6;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Hitung total berita
$total_query = mysqli_query($db, "SELECT COUNT(*) as total FROM berita");
$total_row = mysqli_fetch_assoc($total_query);
$total_berita = $total_row['total'];
$total_pages = ceil($total_berita / $per_page);

// Ambil berita sesuai halaman
$query = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT $per_page OFFSET $offset";
$result = mysqli_query($db, $query);
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
        /* Card Artikel Style dari index.php */
        .artikel-slider {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .artikel-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #eee;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .artikel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .artikel-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .artikel-card:hover img {
            transform: scale(1.1);
        }

        .artikel-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .artikel-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 12px;
            color: #6b7280;
        }

        .artikel-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .artikel-content h3 {
            font-size: 18px;
            color: #00499d;
            margin-bottom: 10px;
            font-weight: 700;
            line-height: 1.4;
        }

        .artikel-content p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .artikel-link {
            color: #ff8303;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
            margin-top: auto;
        }

        .artikel-link:hover {
            gap: 10px;
        }

            .section-title {
                font-size: 24px;
            }

                    .section-title {
            font-size: 36px;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 40px;
            position: relative;
            display: inline-block;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 60px;
            height: 5px;
            background: var(--primary-orange);
            border-radius: 3px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 70px;
            bottom: -10px;
            width: 30px;
            height: 5px;
            background: var(--primary-orange);
            opacity: 0.5;
            border-radius: 3px;
        }

        @media (max-width: 1024px) {
            .artikel-slider {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .artikel-slider {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .artikel-card img {
                height: 200px;
            }
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>

    <div class="container py-5">
        <section id="artikel" class="artikel-container fade-in">
            <div class="artikel-header">
                <h2 class="section-title">Artikel & Berita Terbaru</h2>
                <!-- Hapus tombol kembali ke beranda -->
            </div>
            <div class="artikel-slider">
                <?php while ($berita = mysqli_fetch_assoc($result)): ?>
                    <article class="artikel-card">
                        <img src="berita/<?= htmlspecialchars($berita['gambar']) ?>"
                            alt="<?= htmlspecialchars($berita['judul']) ?>">
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i>
                                    <?= date('d F Y', strtotime($berita['tanggal'])) ?></span>
                                <span><i class="far fa-user"></i> <?= htmlspecialchars($berita['penulis']) ?></span>
                            </div>
                            <h3><?= htmlspecialchars($berita['judul']) ?></h3>
                            <p>
                                <?php
                                $isi = $berita['isi'];
                                $isi_bersih = preg_replace('/^##.*$/m', '', $isi);
                                $isi_bersih = str_replace(["\r", "\n"], ' ', $isi_bersih);
                                $excerpt = mb_strimwidth(trim($isi_bersih), 0, 120, '...');
                                echo htmlspecialchars($excerpt);
                                ?>
                            </p>
                            <a href="artikel-detail.php?id=<?= $berita['id'] ?>" class="artikel-link">
                                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Sebelumnya</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Berikutnya</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </section>
    </div>

    <?php include 'include/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>