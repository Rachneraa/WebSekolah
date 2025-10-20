<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMK TI Garuda Nusantara - Artikel</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
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
        /* Only keep artikel-specific styles */
        .artikel-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .artikel-header {
            max-width: 1200px;
            margin: 0 auto 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
        }

        .artikel-header h2 {
            font-size: 32px;
            color: var(--text-dark);
        }

        .artikel-slider {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            padding: 0 40px;
        }

        .artikel-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s;
        }

        .artikel-card:hover {
            transform: translateY(-5px);
        }

        .artikel-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .artikel-content {
            padding: 25px;
        }

        .artikel-meta {
            display: flex;
            gap: 15px;
            color: var(--text-gray);
            font-size: 13px;
            margin-bottom: 12px;
        }

        .artikel-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .artikel-title {
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 12px;
            line-height: 1.4;
            font-weight: 600;
        }

        .artikel-desc {
            color: var(--text-gray);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }



        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .artikel-slider {
                grid-template-columns: 1fr;
                padding: 0 20px;
            }

            .artikel-header {
                flex-direction: column;
                gap: 20px;
                padding: 0 20px;
                text-align: center;
            }
        }
    </style>
</head>

<body>


    <?php include 'include/nav.php'; ?>

    <!-- Artikel & Berita Section -->
    <section class="artikel-section">
        <div class="artikel-header">
            <h2>Artikel & Berita Terbaru</h2>
        </div>

        <div class="artikel-slider">
            <div class="artikel-card">
                <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800" alt="Siswa TKJ"
                    class="artikel-img">
                <div class="artikel-content">
                    <div class="artikel-meta">
                        <span><i class="far fa-calendar"></i> 1 Okt 2025</span>
                        <span><i class="far fa-user"></i> Admin</span>
                    </div>
                    <h3 class="artikel-title">Siswa TKJ Raih Juara Nasional Kompetisi IT</h3>
                    <p class="artikel-desc">Tim TKJ SMK TI Garuda Nusantara berhasil meraih juara dalam kompetisi
                        tingkat SMK se-Indonesia...</p>
                    <a href="artikel-detail.php" class="btn-detail">Baca Selengkapnya <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="artikel-card">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800" alt="Workshop Web"
                    class="artikel-img">
                <div class="artikel-content">
                    <div class="artikel-meta">
                        <span><i class="far fa-calendar"></i> 28 Sep 2025</span>
                        <span><i class="far fa-user"></i> Admin</span>
                    </div>
                    <h3 class="artikel-title">Workshop Web Development bersama Industry Expert</h3>
                    <p class="artikel-desc">SMK TI Garuda Nusantara mengadakan workshop web development untuk
                        meningkatkan skill siswa...</p>
                    <a href="artikel-detail.php" class="btn-detail">Baca Selengkapnya <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <div class="artikel-card">
                <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800" alt="Program PKL"
                    class="artikel-img">
                <div class="artikel-content">
                    <div class="artikel-meta">
                        <span><i class="far fa-calendar"></i> 15 Sep 2025</span>
                        <span><i class="far fa-user"></i> Admin</span>
                    </div>
                    <h3 class="artikel-title">Program PKL di Industri IT Terkemuka</h3>
                    <p class="artikel-desc">Siswa menjalani program Praktik Kerja Lapangan di berbagai perusahaan
                        teknologi ternama...</p>
                    <a href="artikel-detail.php" class="btn-detail">Baca Selengkapnya <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'include/footer.php'; ?>

    <script>

    </script>
</body>

</html>