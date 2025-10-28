<?php
session_start();
include 'config/koneksi.php';

// Ambil data pendaftar yang statusnya diterima atau proses
$query = "SELECT id, nama_lengkap, jenis_kelamin, jurusan, status FROM ppdb_pendaftar WHERE status IN ('diterima', 'proses') ORDER BY id DESC";
$result = mysqli_query($db, $query);

$pendaftar = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pendaftar[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPDB 2025 - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="//manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="//icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="//icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="//icons/apple-touch-icon.png">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-ppdb {
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.95), rgba(0, 51, 102, 0.95)),
                url('https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=1600') center/cover;
            padding: 80px 20px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-ppdb::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 131, 3, 0.1), transparent);
            border-radius: 50%;
        }

        .hero-ppdb h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }

        .hero-ppdb p {
            font-size: 20px;
            max-width: 700px;
            margin: 0 auto 30px;
            position: relative;
            z-index: 1;
        }

        .hero-ppdb-badge {
            display: inline-block;
            background: var(--primary-orange);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 5px 20px rgba(255, 131, 3, 0.4);
            margin-top: 10px;
            position: relative;
            z-index: 1;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* ========================================
           PPDB CONTAINER
        ========================================= */
        .ppdb-container {
            max-width: 1200px;
            margin: -50px auto 80px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        /* Search & Stats Bar */
        .search-stats-bar {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-bar {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            padding: 12px 20px;
            border-radius: 50px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            min-width: 300px;
        }

        .search-bar:hover {
            border-color: var(--primary-blue);
        }

        .search-bar i {
            color: #999;
            font-size: 18px;
        }

        .search-bar input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
        }

        .search-bar input::placeholder {
            color: #999;
        }

        .stats-box {
            display: flex;
            gap: 15px;
        }

        .stat-item {
            background: linear-gradient(135deg, var(--light-blue), #fff);
            padding: 15px 25px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid var(--primary-blue);
        }

        .stat-item .number {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-blue);
        }

        .stat-item .label {
            font-size: 12px;
            color: var(--text-gray);
            font-weight: 600;
            text-transform: uppercase;
        }


        /* Data Table */
        .data-table-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-filter {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        thead {
            background: #f8f9fa;
        }

        thead th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--light-blue);
        }

        tbody td {
            padding: 18px 20px;
            color: var(--text-gray);
            font-size: 14px;
        }

        .student-name {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 250px;
        }

        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-orange));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        .student-info .name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .student-info .nisn {
            font-size: 12px;
            color: #999;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-male {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-female {
            background: #fce4ec;
            color: #c2185b;
        }

        .badge-agama {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .badge-jurusan {
            background: #e8f5e9;
            color: #388e3c;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .status-diterima {
            background: #d4edda;
            color: #155724;
        }

        .status-proses {
            background: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background: #e2e3e5;
            color: #383d41;
        }

        .status-ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 25px;
            border-top: 1px solid #e9ecef;
            flex-wrap: wrap;
        }

        .pagination button {
            padding: 10px 18px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            min-width: 45px;
        }

        .pagination button:hover {
            background: var(--light-blue);
            border-color: var(--primary-blue);
            color: var(--primary-blue);
        }

        .pagination button.active {
            background: var(--primary-blue);
            color: white;
            border-color: var(--primary-blue);
        }

        .recent-post-item {
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .recent-post-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .recent-post-item a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            line-height: 1.5;
            transition: all 0.3s ease;
        }

        .recent-post-item a:hover {
            color: var(--primary-orange);
            padding-left: 5px;
        }

        .recent-post-date {
            display: block;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .social-links a:hover {
            background: var(--primary-orange);
            transform: translateY(-3px);
        }


        /* ========================================
           SCROLL TO TOP BUTTON
        ========================================= */
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

        /* ========================================
           RESPONSIVE - MOBILE OPTIMIZED
        ========================================= */
        @media (max-width: 1024px) {}

        @media (max-width: 768px) {

            /* HEADER MOBILE */
            header {
                padding: 12px 20px;
            }

            .logo {
                font-size: 12px;
                gap: 8px;
                z-index: 1002;
            }

            .logo-img {
                width: 35px;
                height: 35px;
            }

            header nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 70%;
                height: 100vh;
                background: #ffffff;
                padding: 80px 30px 30px;
                transition: 0.3s;
                box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
            }

            header nav.active {
                right: 0;
            }

            header nav ul {
                flex-direction: column;
                gap: 0;
            }

            header nav ul li {
                width: 100%;
                border-bottom: 1px solid var(--border-gray);
            }

            header nav ul li a {
                font-size: 16px;
                color: var(--text-dark);
                display: block;
                padding: 15px 10px;
                width: 100%;
            }

            header nav ul li a::after {
                display: none;
            }

            .btn-register {
                display: none;
            }

            .mobile-register-btn {
                display: block;
                margin-top: 25px;
                padding: 14px 20px;
                font-size: 13px;
            }

            /* HERO PPDB MOBILE */
            .hero-ppdb {
                padding: 50px 20px;
            }

            .hero-ppdb h1 {
                font-size: 28px;
                line-height: 1.3;
                margin-bottom: 12px;
            }

            .hero-ppdb p {
                font-size: 15px;
                margin-bottom: 20px;
            }

            .hero-ppdb-badge {
                font-size: 13px;
                padding: 10px 20px;
            }

            /* PPDB CONTAINER MOBILE */
            .ppdb-container {
                margin: -30px auto 50px;
                padding: 0 15px;
            }

            /* SEARCH & STATS BAR MOBILE - SUPER RAPI */
            .search-stats-bar {
                padding: 20px 15px;
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .search-bar {
                min-width: 100%;
                width: 100%;
                padding: 12px 18px;
                margin-bottom: 5px;
            }

            .search-bar i {
                font-size: 16px;
            }

            .search-bar input {
                font-size: 14px;
            }

            .stats-box {
                flex-wrap: wrap;
                gap: 10px;
                justify-content: space-between;
            }

            .stat-item {
                flex: 1;
                min-width: calc(33.333% - 7px);
                padding: 12px 10px;
            }

            .stat-item .number {
                font-size: 22px;
            }

            .stat-item .label {
                font-size: 10px;
                line-height: 1.3;
            }

            /* TABLE MOBILE - HORIZONTAL SCROLL WITH BETTER UX */
            .data-table-container {
                margin-bottom: 20px;
            }

            .table-header {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
                padding: 18px 15px;
            }

            .table-header h3 {
                font-size: 16px;
            }

            .table-filter {
                width: 100%;
                justify-content: flex-start;
            }

            .filter-btn {
                padding: 8px 15px;
                font-size: 11px;
                flex: 1;
            }

            .table-wrapper {
                position: relative;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            .table-wrapper::-webkit-scrollbar {
                height: 6px;
            }

            .table-wrapper::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .table-wrapper::-webkit-scrollbar-thumb {
                background: var(--primary-blue);
                border-radius: 3px;
            }

            table {
                min-width: 900px;
                font-size: 13px;
            }

            thead th {
                padding: 14px 12px;
                font-size: 11px;
            }

            tbody td {
                padding: 14px 12px;
                font-size: 12px;
            }

            .student-name {
                min-width: 220px;
                gap: 10px;
            }

            .student-avatar {
                width: 38px;
                height: 38px;
                font-size: 14px;
            }

            .student-info .name {
                font-size: 13px;
            }

            .student-info .nisn {
                font-size: 11px;
            }

            .badge {
                padding: 5px 10px;
                font-size: 10px;
            }

            .status-badge {
                padding: 5px 10px;
                font-size: 10px;
                gap: 4px;
            }

            /* PAGINATION MOBILE - LEBIH RAPI */
            .pagination {
                padding: 18px 10px;
                gap: 6px;
            }

            .pagination button {
                padding: 8px 12px;
                font-size: 13px;
                min-width: 38px;
            }

            /* Hide some pagination numbers on very small screens */
            .pagination button:nth-child(n+5):nth-child(-n+7) {
                display: none;
            }



            .social-links {
                justify-content: center;
                margin-top: 15px;
            }

            .social-links a {
                width: 38px;
                height: 38px;
                font-size: 16px;
            }



            /* SCROLL TO TOP MOBILE */
            .scroll-top {
                bottom: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {

            /* EXTRA SMALL MOBILE */
            .hero-ppdb h1 {
                font-size: 24px;
            }

            .hero-ppdb p {
                font-size: 13px;
            }

            .hero-ppdb-badge {
                font-size: 12px;
                padding: 8px 16px;
            }

            .ppdb-container {
                padding: 0 10px;
            }

            .search-stats-bar {
                padding: 15px 12px;
            }

            .stat-item {
                min-width: calc(33.333% - 7px);
            }

            .stat-item .number {
                font-size: 20px;
            }

            .stat-item .label {
                font-size: 9px;
            }

            .tab-btn {
                min-width: 120px;
                padding: 12px 10px;
                font-size: 12px;
            }

            .tab-btn i {
                font-size: 14px;
            }

            .table-header h3 {
                font-size: 15px;
            }

            .filter-btn {
                font-size: 10px;
                padding: 7px 12px;
            }

            table {
                min-width: 850px;
            }

            .pagination button {
                padding: 7px 10px;
                font-size: 12px;
                min-width: 35px;
            }

            /* Hide more pagination on smallest screens */
            .pagination button:nth-child(n+4):nth-child(-n+8) {
                display: none;
            }
        }

        /* LANDSCAPE MODE MOBILE */
        @media (max-height: 600px) and (orientation: landscape) {
            .hero-ppdb {
                padding: 40px 20px;
            }

            .hero-ppdb h1 {
                font-size: 26px;
                margin-bottom: 10px;
            }

            .hero-ppdb p {
                font-size: 14px;
                margin-bottom: 15px;
            }

            .hero-ppdb-badge {
                padding: 8px 20px;
                font-size: 13px;
            }

            .ppdb-container {
                margin: -20px auto 40px;
            }
        }

        /* Scroll Indicator for Table on Mobile */
        @media (max-width: 768px) {
            .table-wrapper::after {
                content: '← Geser untuk melihat lebih banyak →';
                position: sticky;
                left: 0;
                bottom: 0;
                display: block;
                padding: 8px;
                background: linear-gradient(to top, rgba(0, 73, 157, 0.95), rgba(0, 73, 157, 0.8));
                color: white;
                text-align: center;
                font-size: 11px;
                font-weight: 600;
                z-index: 1;
                animation: fadeIn 2s ease-in-out infinite;
            }

            @keyframes fadeIn {

                0%,
                100% {
                    opacity: 0.6;
                }

                50% {
                    opacity: 1;
                }
            }

            /* Hide scroll indicator after first scroll */
            .table-wrapper.scrolled::after {
                display: none;
            }
        }

        /* Custom Styles for PPDB Page */
        .filter-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .filter-bar input,
        .filter-bar select {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .status-proses {
            color: #ff8303;
            font-weight: bold;
        }

        .status-diterima {
            color: #00499d;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            color: #888;
            padding: 30px;
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>



    <!-- Hero PPDB -->
    <section class="hero-ppdb">
        <h1>Penerimaan Peserta Didik Baru</h1>
        <p>Data Pendaftar Tahun Ajaran 2025/2026</p>
        <div class="hero-ppdb-badge">
            <i class="fas fa-users"></i> 245 Pendaftar Terdaftar
        </div>
    </section>

    <!-- PPDB Container -->
    <div class="ppdb-container">

        <!-- Search & Stats Bar -->
        <div class="search-stats-bar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Cari berdasarkan nama atau NISN...">
            </div>
            <div class="stats-box">
                <div class="stat-item">
                    <div class="number">245</div>
                    <div class="label">Total Pendaftar</div>
                </div>
                <div class="stat-item">
                    <div class="number">189</div>
                    <div class="label">Diterima</div>
                </div>
                <div class="stat-item">
                    <div class="number">42</div>
                    <div class="label">Proses</div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="data-table-container">
            <div class="table-header">
                <h3><i class="fas fa-table"></i> Data Pendaftar PPDB 2025</h3>
                <div class="table-filter">
                    <button class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
                </div>
            </div>

            <div class="table-wrapper" id="tableWrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>Jurusan</th>
                            <th>Status Proses</th>
                        </tr>
                    </thead>
                    <tbody id="ppdbTableBody">
                        <?php
                        // Ambil semua data pendaftar
                        $query = "SELECT nisn, nama_lengkap, jenis_kelamin, agama, jurusan, status FROM ppdb_pendaftar WHERE status IN ('diterima', 'proses') ORDER BY id DESC";
                        $result = mysqli_query($db, $query);
                        $rows = [];
                        while ($row = mysqli_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        ?>
                        <?php if (count($rows) === 0): ?>
                            <tr>
                                <td colspan="6" class="no-data">Belum ada pendaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <div class="student-name">
                                            <div class="student-avatar">
                                                <?= strtoupper(substr($row['nama_lengkap'], 0, 1) . substr(explode(' ', $row['nama_lengkap'])[1] ?? $row['nama_lengkap'], 0, 1)) ?>
                                            </div>
                                            <div class="student-info">
                                                <div class="name"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                                <div class="nisn">NISN: <?= htmlspecialchars($row['nisn']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-<?= $row['jenis_kelamin'] == 'laki-laki' ? 'male' : 'female' ?>">
                                            <i
                                                class="fas fa-<?= $row['jenis_kelamin'] == 'laki-laki' ? 'mars' : 'venus' ?>"></i>
                                            <?= ucwords($row['jenis_kelamin']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-agama">
                                            <i
                                                class="fas fa-<?= $row['agama'] == 'islam' ? 'mosque' : ($row['agama'] == 'kristen' ? 'cross' : ($row['agama'] == 'katolik' ? 'cross' : ($row['agama'] == 'hindu' ? 'om' : 'pray'))) ?>"></i>
                                            <?= ucwords($row['agama']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-jurusan"><?= ucwords($row['jurusan']) ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $row['status'] ?>">
                                            <i
                                                class="fas fa-<?= $row['status'] == 'diterima' ? 'check-circle' : 'clock' ?>"></i>
                                            <?= $row['status'] == 'diterima' ? 'Diterima' : 'Proses Seleksi' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <button><i class="fas fa-chevron-left"></i></button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>4</button>
                <button>5</button>
                <button>...</button>
                <button>25</button>
                <button><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

    </div>

    <?php include 'include/footer.php'; ?>

    <script>
        // Hapus script yang sudah ada di nav.php:
        // - Header scroll
        // - Nav highlighting

        // Hapus script yang sudah ada di footer.php:
        // - Footer animations
        // - Scroll to top

        // Sisakan hanya script spesifik PPDB
        window.addEventListener('load', function () {
            setTimeout(() => {
                document.querySelector('.loader-wrapper').classList.add('hidden');
            }, 300);
        });

        // Table scroll indicator
        const tableWrapper = document.getElementById('tableWrapper');
        if (tableWrapper) {
            tableWrapper.addEventListener('scroll', function () {
                if (this.scrollLeft > 10) {
                    this.classList.add('scrolled');
                }
            }, { once: true });
        }

        // Ambil data dari tabel
        const rows = Array.from(document.querySelectorAll('#ppdbTable tbody tr'));
        const searchInput = document.getElementById('searchNama');
        const jurusanSelect = document.getElementById('filterJurusan');

        function filterTable() {
            const search = searchInput.value.toLowerCase();
            const jurusan = jurusanSelect.value;
            let visible = 0;

            rows.forEach(row => {
                const nama = row.cells[1]?.textContent.toLowerCase() || '';
                const jurusanCell = row.cells[3]?.textContent.toLowerCase() || '';
                let show = true;

                if (search && !nama.includes(search)) show = false;
                if (jurusan && !jurusanCell.includes(jurusan)) show = false;

                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            // Tampilkan pesan jika tidak ada data yang cocok
            document.querySelectorAll('.no-data').forEach(el => el.style.display = visible === 0 ? '' : 'none');
        }

        searchInput.addEventListener('input', filterTable);
        jurusanSelect.addEventListener('change', filterTable);
    </script>
    <script>
        const tableBody = document.getElementById('ppdbTableBody');
        const searchInput = document.getElementById('searchNama');
        const jurusanSelect = document.getElementById('filterJurusan');
        let allRows = Array.from(tableBody.querySelectorAll('tr'));
        let currentPage = 1;
        const rowsPerPage = 10;

        function renderTable() {
            let filtered = allRows.filter(row => {
                const nama = row.querySelector('.name')?.textContent.toLowerCase() || '';
                const jurusan = row.querySelector('.badge-jurusan')?.textContent.toLowerCase() || '';
                let show = true;
                if (searchInput.value && !nama.includes(searchInput.value.toLowerCase())) show = false;
                if (jurusanSelect.value && !jurusan.includes(jurusanSelect.value.toLowerCase())) show = false;
                return show;
            });

            // Pagination
            const totalPages = Math.ceil(filtered.length / rowsPerPage);
            currentPage = Math.min(currentPage, totalPages) || 1;
            tableBody.innerHTML = '';
            filtered.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage).forEach(row => tableBody.appendChild(row));
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            const pag = document.querySelector('.pagination');
            if (!pag) return;
            pag.innerHTML = '';
            if (totalPages <= 1) return;
            pag.innerHTML += `<button ${currentPage == 1 ? 'disabled' : ''} onclick="gotoPage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
            for (let i = 1; i <= totalPages; i++) {
                pag.innerHTML += `<button class="${i == currentPage ? 'active' : ''}" onclick="gotoPage(${i})">${i}</button>`;
            }
            pag.innerHTML += `<button ${currentPage == totalPages ? 'disabled' : ''} onclick="gotoPage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
        }

        window.gotoPage = function (page) {
            currentPage = page;
            renderTable();
        }

        searchInput.addEventListener('input', renderTable);
        jurusanSelect.addEventListener('change', renderTable);

        renderTable();
    </script>
</body>

</html>