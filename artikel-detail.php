<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Berita - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" href="/icons/logo.png">
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="/pkl/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/pkl/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/pkl/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/pkl/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* styles khusus halaman detail (minimal) */
        .container {
            max-width: 980px;
            margin: 100px auto 60px;
            padding: 0 20px;
        }

        .breadcrumb {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .breadcrumb a {
            color: #6b7280;
        }

        .article-title {
            font-size: 28px;
            margin: 6px 0 8px;
            color: #0f1724;
            font-weight: 700;
        }

        .article-meta {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .article-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            display: block;
            margin: 18px 0;
        }

        .article-content {
            color: #111827;
            line-height: 1.8;
            font-size: 16px;
        }


        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Style for back button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 15px;
            margin-bottom: 20px;
            transition: color 0.3s;
        }

        .btn-back:hover {
            color: var(--accent);
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>



    <main class="container" id="articlePage">
        <div class="article-card">
            <!-- Replace breadcrumb with back button -->
            <a href="artikel.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <h1 class="article-title">Siswa TKJ Raih Juara Nasional Kompetisi IT</h1>

            <div class="article-meta">
                <span><i class="far fa-calendar"></i> 10 Okt 2025</span>
                &nbsp;•&nbsp;
                <span><i class="far fa-user"></i> SMK TI Garuda Nusantara</span>
            </div>

            <!-- Ganti path gambar berikut sesuai file di folder assets/ -->
            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800" alt="Gambar Berita" class="article-image" />

            <div class="article-content">
                <p>Cimahi — Siswa jurusan Teknik Komputer dan Jaringan (TKJ) SMK TI Garuda Nusantara kembali menorehkan prestasi membanggakan di tingkat nasional. 
                    Tim TKJ sekolah ini berhasil meraih Juara Nasional dalam ajang Kompetisi IT Antar SMK se-Indonesia yang diselenggarakan di Jakarta pada 4–6 Oktober 2025.</p>

                <h2>Harapan untuk Masa Depan</h2>
                <p>Kepala sekolah Bapa Rido menyampaikan apresiasi dan rasa bangganya atas prestasi tersebut. “Kami berharap keberhasilan ini menjadi inspirasi bagi seluruh siswa untuk terus berprestasi dan berinovasi di bidang teknologi informasi.”, ujarnya.
                Dengan kemenangan ini, SMK TI Garuda Nusantara semakin memperkuat reputasinya sebagai sekolah kejuruan unggulan yang mampu bersaing di tingkat nasional.</p>


            </div>

            <!-- Remove bottom back link -->
            <!-- Delete or comment out:
            <a class="back-link" href="artikel.php">← Kembali ke daftar artikel</a> 
            -->
        </div>
    </main>

    <?php include 'include/footer.php'; ?>

    <script>


        // ...existing scripts...
    </script>
</body>

</html>