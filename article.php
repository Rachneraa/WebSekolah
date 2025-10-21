<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Artikel & Berita - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="/pkl/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/pkl/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/pkl/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/pkl/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables */
        :root {
            --primary-orange: #ff8303;
            --primary-blue: #00499d;
            --dark-blue: #003366;
            --text-dark: #0f1724;
            --text-gray: #6b7280;
            --border-gray: #e0e0e0;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            padding-top: 80px;
            overflow-x: hidden;
            background: #f8f9fa;
        }

        /* Loading Screen */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loader-wrapper.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid #f0f0f0;
            border-top: 5px solid var(--primary-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Hero Section */
        .artikel-hero {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            padding: 80px 20px 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .artikel-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 131, 3, 0.1), transparent);
            border-radius: 50%;
        }

        .artikel-hero h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .artikel-hero p {
            font-size: 18px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        /* Search & Filter Section */
        .search-filter-section {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid var(--border-gray);
            border-radius: 50px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-orange);
        }

        .search-box button {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-orange);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-box button:hover {
            background: var(--primary-blue);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 12px 25px;
            border: 2px solid var(--border-gray);
            background: white;
            color: var(--text-gray);
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-orange);
            color: white;
            border-color: var(--primary-orange);
        }

        /* Artikel Grid */
        .artikel-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 60px;
        }

        .artikel-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--border-gray);
        }

        .artikel-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .artikel-image {
            width: 100%;
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .artikel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .artikel-card:hover .artikel-image img {
            transform: scale(1.1);
        }

        .artikel-category {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-orange);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }

        .artikel-content {
            padding: 25px;
        }

        .artikel-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 12px;
            color: var(--text-gray);
        }

        .artikel-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .artikel-content h3 {
            font-size: 18px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-weight: 700;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .artikel-content p {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.6;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .artikel-link {
            color: var(--primary-orange);
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
        }

        .artikel-link:hover {
            gap: 10px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
        }

        .pagination button {
            padding: 12px 20px;
            border: 2px solid var(--border-gray);
            background: white;
            color: var(--text-gray);
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination button:hover:not(:disabled) {
            background: var(--primary-orange);
            color: white;
            border-color: var(--primary-orange);
        }

        .pagination button.active {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .pagination button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* Sidebar */
        .artikel-layout {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
        }

        .sidebar {
            position: sticky;
            top: 100px;
            align-self: start;
        }

        .sidebar-widget {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .sidebar-widget h3 {
            font-size: 20px;
            color: var(--primary-blue);
            margin-bottom: 20px;
            font-weight: 700;
            position: relative;
            padding-bottom: 10px;
        }

        .sidebar-widget h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-orange);
        }

        .popular-post {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-gray);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .popular-post:last-child {
            border-bottom: none;
        }

        .popular-post:hover {
            padding-left: 5px;
        }

        .popular-post img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .popular-post-content h4 {
            font-size: 14px;
            color: var(--primary-blue);
            margin-bottom: 5px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .popular-post-date {
            font-size: 12px;
            color: var(--text-gray);
        }

        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag {
            padding: 8px 18px;
            background: #f8f9fa;
            color: var(--text-gray);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tag:hover {
            background: var(--primary-orange);
            color: white;
        }

        /* Scroll to Top */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255, 131, 3, 0.4);
            z-index: 999;
        }

        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(255, 131, 3, 0.5);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .artikel-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }

            .artikel-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .artikel-hero {
                padding: 60px 20px 40px;
            }

            .artikel-hero h1 {
                font-size: 32px;
            }

            .artikel-hero p {
                font-size: 15px;
            }

            .container {
                padding: 40px 20px;
            }

            .search-filter-section {
                padding: 20px;
            }

            .search-box {
                min-width: 100%;
            }

            .filter-buttons {
                width: 100%;
            }

            .filter-btn {
                flex: 1;
                min-width: calc(50% - 5px);
            }

            .artikel-grid {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .sidebar-widget {
                padding: 20px;
            }

            .pagination {
                flex-wrap: wrap;
            }

            .pagination button {
                padding: 10px 15px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .artikel-hero h1 {
                font-size: 28px;
            }

            .filter-btn {
                min-width: 100%;
            }

            .artikel-content {
                padding: 20px;
            }

            .artikel-content h3 {
                font-size: 16px;
            }

            .popular-post img {
                width: 60px;
                height: 60px;
            }

            .popular-post-content h4 {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>

    <!-- Loading Screen -->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>

    <!-- Hero Section -->
    <section class="artikel-hero">
        <h1><i class="fas fa-newspaper"></i> Artikel & Berita</h1>
        <p>Informasi terkini seputar kegiatan, prestasi, dan berita SMK TI Garuda Nusantara</p>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Search & Filter -->
        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari artikel...">
                <button type="button" onclick="searchArtikel()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-category="all">Semua</button>
                <button class="filter-btn" data-category="prestasi">Prestasi</button>
                <button class="filter-btn" data-category="kegiatan">Kegiatan</button>
                <button class="filter-btn" data-category="teknologi">Teknologi</button>
                <button class="filter-btn" data-category="pengumuman">Pengumuman</button>
            </div>
        </div>

        <!-- Artikel Layout -->
        <div class="artikel-layout">
            <!-- Artikel Grid -->
            <div>
                <div class="artikel-grid" id="artikelGrid">
                    <!-- Artikel 1 -->
                    <article class="artikel-card" data-category="prestasi">
                        <div class="artikel-image">
                            <span class="artikel-category">Prestasi</span>
                            <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800" alt="Prestasi Siswa">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 10 Oktober 2025</span>
                                <span><i class="far fa-user"></i> Admin</span>
                            </div>
                            <h3>Siswa TKJ Raih Juara Nasional Kompetisi Jaringan</h3>
                            <p>Tim siswa SMK TI Garuda Nusantara berhasil meraih juara 1 dalam Lomba Konfigurasi Jaringan tingkat nasional yang diselenggarakan di Jakarta. Prestasi membanggakan ini merupakan hasil kerja keras dan dedikasi siswa-siswi kami.</p>
                            <a href="artikel-detail.php?id=1" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 2 -->
                    <article class="artikel-card" data-category="kegiatan">
                        <div class="artikel-image">
                            <span class="artikel-category">Kegiatan</span>
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800" alt="Workshop Coding">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 5 Oktober 2025</span>
                                <span><i class="far fa-user"></i> Guru RPL</span>
                            </div>
                            <h3>Workshop Web Development bersama Industri IT Ternama</h3>
                            <p>Sekolah mengadakan workshop intensif pengembangan web modern dengan instruktur dari perusahaan teknologi terkemuka. Workshop ini diikuti oleh siswa kelas XI dan XII jurusan RPL.</p>
                            <a href="artikel-detail.php?id=2" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 3 -->
                    <article class="artikel-card" data-category="kegiatan">
                        <div class="artikel-image">
                            <span class="artikel-category">Kegiatan</span>
                            <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800" alt="Praktik Industri">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 1 Oktober 2025</span>
                                <span><i class="far fa-user"></i> Humas</span>
                            </div>
                            <h3>Program Praktek Kerja Industri di 50+ Perusahaan Partner</h3>
                            <p>Siswa kelas XII berkesempatan mengikuti program PKL di berbagai perusahaan teknologi terkemuka di Indonesia. Program ini bertujuan memberikan pengalaman kerja nyata.</p>
                            <a href="artikel-detail.php?id=3" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 4 -->
                    <article class="artikel-card" data-category="teknologi">
                        <div class="artikel-image">
                            <span class="artikel-category">Teknologi</span>
                            <img src="https://images.unsplash.com/photo-1488590528505-98d2b5aba04b?w=800" alt="Lab Komputer">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 28 September 2025</span>
                                <span><i class="far fa-user"></i> Admin</span>
                            </div>
                            <h3>Peresmian Laboratorium Komputer dengan Teknologi Terkini</h3>
                            <p>SMK TI Garuda Nusantara meresmikan laboratorium komputer baru yang dilengkapi dengan perangkat keras dan software terbaru untuk mendukung pembelajaran siswa.</p>
                            <a href="artikel-detail.php?id=4" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 5 -->
                    <article class="artikel-card" data-category="prestasi">
                        <div class="artikel-image">
                            <span class="artikel-category">Prestasi</span>
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=800" alt="Olimpiade">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 25 September 2025</span>
                                <span><i class="far fa-user"></i> Admin</span>
                            </div>
                            <h3>Juara 2 Olimpiade Sains Nasional Bidang Informatika</h3>
                            <p>Siswa SMK TI Garuda Nusantara berhasil meraih medali perak pada Olimpiade Sains Nasional bidang Informatika yang digelar di Surabaya.</p>
                            <a href="artikel-detail.php?id=5" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 6 -->
                    <article class="artikel-card" data-category="pengumuman">
                        <div class="artikel-image">
                            <span class="artikel-category">Pengumuman</span>
                            <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800" alt="PPDB">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 20 September 2025</span>
                                <span><i class="far fa-user"></i> Panitia PPDB</span>
                            </div>
                            <h3>Pendaftaran Peserta Didik Baru Tahun Ajaran 2025/2026 Dibuka</h3>
                            <p>SMK TI Garuda Nusantara membuka pendaftaran siswa baru untuk tahun ajaran 2025/2026. Daftar sekarang dan raih masa depan cemerlangmu di bidang teknologi.</p>
                            <a href="artikel-detail.php?id=6" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 7 -->
                    <article class="artikel-card" data-category="kegiatan">
                        <div class="artikel-image">
                            <span class="artikel-category">Kegiatan</span>
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=800" alt="Study Tour">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 15 September 2025</span>
                                <span><i class="far fa-user"></i> Koordinator</span>
                            </div>
                            <h3>Study Tour ke Silicon Valley: Menginspirasi Siswa Menjadi Innovator</h3>
                            <p>Siswa berprestasi SMK TI Garuda Nusantara berkesempatan mengikuti study tour ke Silicon Valley untuk melihat langsung industri teknologi dunia.</p>
                            <a href="artikel-detail.php?id=7" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 8 -->
                    <article class="artikel-card" data-category="teknologi">
                        <div class="artikel-image">
                            <span class="artikel-category">Teknologi</span>
                            <img src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=800" alt="AI Workshop">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 10 September 2025</span>
                                <span><i class="far fa-user"></i> Guru IT</span>
                            </div>
                            <h3>Implementasi Artificial Intelligence dalam Kurikulum Pembelajaran</h3>
                            <p>SMK TI Garuda Nusantara menjadi pelopor dalam mengintegrasikan pembelajaran AI dan Machine Learning ke dalam kurikulum untuk mempersiapkan siswa menghadapi era industri 4.0.</p>
                            <a href="artikel-detail.php?id=8" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>

                    <!-- Artikel 9 -->
                    <article class="artikel-card" data-category="prestasi">
                        <div class="artikel-image">
                            <span class="artikel-category">Prestasi</span>
                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=800" alt="Startup Competition">
                        </div>
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i> 5 September 2025</span>
                                <span><i class="far fa-user"></i> Tim Pembina</span>
                            </div>
                            <h3>Siswa RPL Menangkan Kompetisi Startup Digital Tingkat Provinsi</h3>
                            <p>Tim startup siswa RPL berhasil menjuarai kompetisi startup digital dengan aplikasi inovatif yang mereka kembangkan untuk memecahkan masalah sosial.</p>
                            <a href="artikel-detail.php?id=9" class="artikel-link">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <button id="prevPage"><i class="fas fa-chevron-left"></i> Sebelumnya</button>
                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>
                    <button id="nextPage">Selanjutnya <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="sidebar">
                <!-- Popular Posts -->
                <div class="sidebar-widget">
                    <h3><i class="fas fa-fire"></i> Artikel Populer</h3>
                    <div class="popular-post" onclick="window.location.href='artikel-detail.php?id=1'">
                        <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=200" alt="Popular 1">
                        <div class="popular-post-content">
                            <h4>Siswa TKJ Raih Juara Nasional Kompetisi Jaringan</h4>
                            <span class="popular-post-date"><i class="far fa-calendar"></i> 10 Oktober 2025</span>
                        </div>
                    </div>
                    <div class="popular-post" onclick="window.location.href='artikel-detail.php?id=2'">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=200" alt="Popular 2">
                        <div class="popular-post-content">
                            <h4>Workshop Web Development bersama Industri IT Ternama</h4>
                            <span class="popular-post-date"><i class="far fa-calendar"></i> 5 Oktober 2025</span>
                        </div>
                    </div>
                    <div class="popular-post" onclick="window.location.href='artikel-detail.php?id=3'">
                        <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=200" alt="Popular 3">
                        <div class="popular-post-content">
                            <h4>Program Praktek Kerja Industri di 50+ Perusahaan</h4>
                            <span class="popular-post-date"><i class="far fa-calendar"></i> 1 Oktober 2025</span>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="sidebar-widget">
                    <h3><i class="fas fa-folder"></i> Kategori</h3>
                    <div class="tag-cloud">
                        <span class="tag">Prestasi (15)</span>
                        <span class="tag">Kegiatan (28)</span>
                        <span class="tag">Teknologi (12)</span>
                        <span class="tag">Pengumuman (8)</span>
                        <span class="tag">Beasiswa (5)</span>
                        <span class="tag">Alumni (10)</span>
                    </div>
                </div>

                <!-- Tags -->
                <div class="sidebar-widget">
                    <h3><i class="fas fa-tags"></i> Tags</h3>
                    <div class="tag-cloud">
                        <span class="tag">Programming</span>
                        <span class="tag">Networking</span>
                        <span class="tag">Design</span>
                        <span class="tag">AI</span>
                        <span class="tag">Workshop</span>
                        <span class="tag">Competition</span>
                        <span class="tag">PKL</span>
                        <span class="tag">PPDB</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <!-- Scroll to Top -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Loading Screen
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.querySelector('.loader-wrapper').classList.add('hidden');
            }, 500);
        });

        // Filter Articles
        const filterButtons = document.querySelectorAll('.filter-btn');
        const artikelCards = document.querySelectorAll('.artikel-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const category = this.getAttribute('data-category');

                artikelCards.forEach(card => {
                    if (category === 'all' || card.getAttribute('data-category') === category) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        // Search Function
        function searchArtikel() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            
            artikelCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const content = card.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchValue) || content.includes(searchValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Search on Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchArtikel();
            }
        });

        // Scroll to Top
        const scrollTopBtn = document.getElementById('scrollTop');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Pagination (Simple example)
        const paginationButtons = document.querySelectorAll('.pagination button');
        
        paginationButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (!this.id && !this.classList.contains('active')) {
                    paginationButtons.forEach(btn => {
                        if (!btn.id) btn.classList.remove('active');
                    });
                    this.classList.add('active');
                    
                    // Scroll to top of articles
                    document.querySelector('.artikel-grid').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Tag Click Handler
        document.querySelectorAll('.tag').forEach(tag => {
            tag.addEventListener('click', function() {
                const tagText = this.textContent.toLowerCase();
                document.getElementById('searchInput').value = tagText;
                searchArtikel();
                
                // Scroll to articles
                document.querySelector('.artikel-grid').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        // Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/pkl/service-worker.js')
                    .then(registration => console.log('ServiceWorker registered'))
                    .catch(err => console.log('ServiceWorker registration failed:', err));
            });
        }
    </script>
</body>

</html>