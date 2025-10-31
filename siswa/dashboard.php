<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    //header('Location: ../index.php');
    //exit();
}

// Get siswa data
$stmt = $db->prepare("SELECT s.*, k.nama as nama_kelas 
                      FROM siswa s 
                      JOIN kelas k ON s.kelas_id = k.kelas_id 
                      WHERE s.siswa_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$siswa = $stmt->get_result()->fetch_assoc();

// Statistik absensi dari absensi_detail
$hadir_query = "SELECT COUNT(*) as hadir FROM absensi_detail WHERE siswa_id = ? AND status = 'Hadir'";
$stmt = $db->prepare($hadir_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$hadir_count = $stmt->get_result()->fetch_assoc()['hadir'];

$izin_query = "SELECT COUNT(*) as izin FROM absensi_detail WHERE siswa_id = ? AND status = 'Izin'";
$stmt = $db->prepare($izin_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$izin_count = $stmt->get_result()->fetch_assoc()['izin'];

$sakit_query = "SELECT COUNT(*) as sakit FROM absensi_detail WHERE siswa_id = ? AND status = 'Sakit'";
$stmt = $db->prepare($sakit_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$sakit_count = $stmt->get_result()->fetch_assoc()['sakit'];

// Jika ingin menambah Alpha:
$alpha_query = "SELECT COUNT(*) as alpha FROM absensi_detail WHERE siswa_id = ? AND status = 'Alpha'";
$stmt = $db->prepare($alpha_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$alpha_count = $stmt->get_result()->fetch_assoc()['alpha'];

// Tampilkan hanya dari absensi_detail (tanpa UNION login_history)
$absensi_query = "
    SELECT tanggal, waktu_absen, status 
    FROM absensi_detail 
    WHERE siswa_id = ?
    ORDER BY tanggal DESC, waktu_absen DESC 
    LIMIT 10
";
$stmt = $db->prepare($absensi_query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$absensi = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="theme-color" content="#00499D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC">

    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SMK TI Garuda Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* ... [CSS Anda yang sangat panjang tetap di sini, tidak berubah] ... */
        :root {
            --primary-blue: #00499d;
            --primary-orange: #ff8303;
            --dark-blue: #003366;
            --light-blue: #e3f2fd;
            --success-green: #28a745;
            --warning-yellow: #ffc107;
            --danger-red: #dc3545;
            --info-blue: #17a2b8;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border-color: #e0e0e0;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        /* Sidebar Styles - LEFT for DESKTOP */
        .sidebar {
            position: fixed;
            top: 20px;
            left: 20px;
            width: var(--sidebar-width);
            height: calc(100vh - 40px);
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 30px 0;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 0 25px 25px;
            border-bottom: 2px solid var(--border-color);
            text-align: center;
        }

        .sidebar-header h4 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--text-muted);
            font-size: 13px;
            margin: 0;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .nav-link:hover {
            background: var(--light-blue);
            color: var(--primary-blue);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            box-shadow: 0 4px 15px rgba(0, 73, 157, 0.3);
        }

        .nav-link i {
            width: 24px;
            font-size: 18px;
            margin-right: 12px;
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
        }

        .btn-logout {
            width: 100%;
            background: linear-gradient(135deg, var(--danger-red), #c82333);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }

        /* Main Content - LEFT MARGIN for DESKTOP */
        .main-content {
            margin-left: calc(var(--sidebar-width) + 40px);
            padding: 0 20px 20px 0;
        }

        /* Header */
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .page-header h2 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .page-header .user-name {
            color: var(--primary-orange);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-muted);
            margin: 0;
            font-size: 14px;
        }

        .welcome-text {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-content {
            flex: 1;
        }

        .welcome-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .quick-actions h5 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .action-btn {
            background: var(--light-blue);
            border: 2px solid transparent;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-dark);
        }

        .action-btn:hover {
            background: white;
            border-color: var(--primary-blue);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            color: var(--primary-blue);
        }

        .action-btn i {
            font-size: 32px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            display: block;
        }

        .action-btn span {
            font-size: 13px;
            font-weight: 600;
            display: block;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 48px;
            color: white;
            box-shadow: 0 8px 25px rgba(0, 73, 157, 0.3);
        }

        .profile-name {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 5px;
        }

        .profile-class {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 20px;
        }

        .profile-info {
            text-align: left;
            padding-top: 20px;
            border-top: 2px solid var(--border-color);
        }

        .info-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: var(--light-blue);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-size: 18px;
            margin-right: 15px;
        }

        .info-text {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            word-break: break-word;
        }

        /* Absensi Card */
        .absensi-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-header-custom h5 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 20px;
            margin: 0;
        }

        .btn-scan {
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-scan:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 131, 3, 0.4);
            color: white;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .custom-table thead th {
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 15px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            white-space: nowrap;
        }

        .custom-table thead th:first-child {
            border-radius: 10px 0 0 10px;
        }

        .custom-table thead th:last-child {
            border-radius: 0 10px 10px 0;
        }

        .custom-table tbody tr {
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .custom-table tbody tr:hover {
            background: var(--light-blue);
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .custom-table tbody td {
            padding: 15px;
            border: none;
            font-size: 14px;
            color: var(--text-dark);
        }

        .custom-table tbody tr td:first-child {
            border-radius: 10px 0 0 10px;
        }

        .custom-table tbody tr td:last-child {
            border-radius: 0 10px 10px 0;
        }

        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .status-hadir {
            background: #d4edda;
            color: #155724;
        }

        .status-izin {
            background: #fff3cd;
            color: #856404;
        }

        .status-sakit {
            background: #f8d7da;
            color: #721c24;
        }

        .status-alpha {
            background: #e2e3e5;
            color: #383d41;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state h5 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* Statistics Cards - ENLARGED TEXT */
        .stats-section {
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card.hadir {
            background: linear-gradient(135deg, var(--success-green), #1e7e34);
            color: white;
        }

        .stat-card.izin {
            background: linear-gradient(135deg, var(--warning-yellow), #e0a800);
            color: white;
        }

        .stat-card.sakit {
            background: linear-gradient(135deg, var(--danger-red), #c82333);
            color: white;
        }

        .stat-card.total {
            background: linear-gradient(135deg, var(--info-blue), #117a8b);
            color: white;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .stat-info h3 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 8px;
            line-height: 1;
        }

        .stat-info p {
            font-size: 16px;
            opacity: 0.95;
            margin: 0;
            font-weight: 500;
        }

        /* Mobile Menu Toggle */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            cursor: pointer;
            color: var(--primary-blue);
            font-size: 20px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* MOBILE: Sidebar from RIGHT */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                right: -280px;
                left: auto;
                top: 0;
                height: 100%;
                border-radius: 20px 0 0 20px;
                transition: right 0.3s ease;
            }

            .sidebar.active {
                right: 0;
            }

            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .main-content {
                margin-left: 0;
                margin-right: 0;
                margin-top: 70px;
                padding: 0;
            }

            .page-header h2,
            .page-header .user-name {
                font-size: 20px;
            }

            .page-header p {
                font-size: 12px;
            }

            .welcome-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-icon {
                width: 55px;
                height: 55px;
                font-size: 28px;
                margin-bottom: 15px;
            }

            .stat-info h3 {
                font-size: 36px;
            }

            .stat-info p {
                font-size: 14px;
            }

            .action-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }

            .action-btn {
                padding: 15px 10px;
            }

            .action-btn i {
                font-size: 24px;
            }

            .action-btn span {
                font-size: 11px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .page-header {
                padding: 20px;
            }

            .quick-actions {
                padding: 20px;
            }

            .quick-actions h5 {
                font-size: 18px;
            }

            .absensi-card,
            .profile-card {
                padding: 20px;
            }

            .card-header-custom {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-header-custom h5 {
                font-size: 18px;
            }

            .btn-scan {
                width: 100%;
                justify-content: center;
            }

            .custom-table {
                font-size: 12px;
            }

            .custom-table thead th {
                padding: 10px;
                font-size: 11px;
            }

            .custom-table tbody td {
                padding: 10px;
                font-size: 12px;
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }

            .profile-name {
                font-size: 18px;
            }

            .info-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .info-label {
                font-size: 11px;
            }

            .info-value {
                font-size: 13px;
            }
        }

        @media (max-width: 576px) {
            .mobile-toggle {
                top: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .main-content {
                margin-top: 80px;
            }

            .page-header {
                padding: 15px;
                margin-bottom: 15px;
            }

            .page-header h2 {
                font-size: 16px;
                margin-bottom: 5px;
            }

            .page-header .user-name {
                font-size: 18px;
                margin-bottom: 8px;
            }

            .page-header p {
                font-size: 11px;
            }

            .welcome-text {
                gap: 10px;
            }

            .welcome-icon {
                width: 35px;
                height: 35px;
                font-size: 18px;
            }

            .quick-actions {
                padding: 15px;
                margin-bottom: 15px;
            }

            .quick-actions h5 {
                font-size: 16px;
                margin-bottom: 15px;
            }

            .action-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .action-btn {
                padding: 12px 8px;
            }

            .action-btn i {
                font-size: 22px;
                margin-bottom: 8px;
            }

            .action-btn span {
                font-size: 10px;
            }

            .content-grid {
                gap: 15px;
                margin-bottom: 15px;
            }

            .profile-card,
            .absensi-card {
                padding: 15px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
                margin-bottom: 12px;
            }

            .stat-info h3 {
                font-size: 32px;
            }

            .stat-info p {
                font-size: 12px;
            }

            .custom-table thead th {
                padding: 8px;
                font-size: 10px;
            }

            .custom-table tbody td {
                padding: 8px;
                font-size: 11px;
            }

            .status-badge {
                padding: 4px 10px;
                font-size: 10px;
            }

            .empty-state i {
                font-size: 48px;
            }

            .empty-state h5 {
                font-size: 16px;
            }

            .empty-state p {
                font-size: 12px;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .quick-actions,
        .stat-card,
        .profile-card,
        .absensi-card {
            animation: fadeInUp 0.6s ease;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .stat-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .stat-card:nth-child(4) {
            animation-delay: 0.4s;
        }

        /* Overlay untuk mobile sidebar */
        .sidebar-overlay {
            display: none;
            /* Default tersembunyi */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease, display 0.3s;
        }

        .sidebar-overlay.active {
            display: block;
            /* Hanya tampil ketika kelas active ditambahkan JS */
            opacity: 1;
        }

        /* --- PERBAIKAN BUG: HAPUS ATURAN MOBILE OVERLAY PAKSA --- */
        /* Hapus media query yang membuat overlay selalu display: block; di mobile */
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-graduation-cap"></i> Portal Siswa</h4>
            <p>SMK TI Garuda Nusantara</p>
        </div>

        <nav class="nav-menu">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="absensi.php" class="nav-link">
                    <i class="fas fa-qrcode"></i>
                    <span>Absensi QR</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="jadwal.php" class="nav-link">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Jadwal Pelajaran</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="nilai.php" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Nilai</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="pengaturan.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan Akun</span>
                </a>
            </div>
        </nav>

        <div class="logout-section">
            <a href="../config/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div class="welcome-text">
                <div class="welcome-icon">
                    <i class="fas fa-hand-paper"></i>
                </div>
                <div class="welcome-content">
                    <h2>Selamat Datang,</h2>
                    <div class="user-name"><?= htmlspecialchars($siswa['nama']) ?>!</div>
                    <p><i class="fas fa-calendar"></i> <?= date('l, d F Y') ?> | <i class="fas fa-clock"></i>
                        <?= date('H:i') ?> WIB</p>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h5><i class="fas fa-bolt"></i> Akses Cepat</h5>
            <div class="action-grid">
                <a href="absensi.php" class="action-btn">
                    <i class="fas fa-qrcode"></i>
                    <span>Scan Absensi</span>
                </a>
                <a href="jadwal.php" class="action-btn">
                    <i class="fas fa-calendar-week"></i>
                    <span>Lihat Jadwal</span>
                </a>
                <a href="nilai.php" class="action-btn">
                    <i class="fas fa-chart-bar"></i>
                    <span>Cek Nilai</span>
                </a>
            </div>
        </div>

        <div class="content-grid">
            <div class="profile-card">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-name"><?= htmlspecialchars($siswa['nama'] ?? '') ?></div>
                <div class="profile-class">
                    <i class="fas fa-users"></i> Kelas <?= htmlspecialchars($siswa['nama_kelas'] ?? '') ?>
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="info-text">
                            <div class="info-label">NIS</div>

                            <div class="info-value"><?= htmlspecialchars($siswa['nis'] ?? '') ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-text">
                            <div class="info-label">Email</div>
                            <div class="info-value"><?= htmlspecialchars($siswa['email'] ?? 'Belum ada') ?></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-text">
                            <div class="info-label">No. Telepon</div>
                            <div class="info-value"><?= htmlspecialchars($siswa['telepon'] ?? 'Belum ada') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absensi-card">
                <div class="card-header-custom">
                    <h5><i class="fas fa-history"></i> Riwayat Absensi Terbaru</h5>
                    <a href="absensi.php" class="btn-scan">
                        <i class="fas fa-qrcode"></i>
                        Scan QR Code
                    </a>
                </div>

                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($absensi->num_rows > 0):
                                while ($row = $absensi->fetch_assoc()):
                                    $status_class = strtolower($row['status']);
                                    $waktu = strtotime($row['tanggal'] . ' ' . $row['waktu_absen']);
                                    ?>
                                    <tr>
                                        <td>
                                            <i class="fas fa-calendar"></i>
                                            <?= date('d M Y', $waktu) ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-clock"></i>
                                            <?= date('H:i', $waktu) ?> WIB
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= $status_class ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td>-</td>
                                    </tr>
                                    <?php
                                endwhile;
                            else:
                                ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <h5>Belum Ada Riwayat Absensi</h5>
                                            <p>Mulai scan QR Code untuk mencatat kehadiran Anda</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card hadir">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $hadir_count ?></h3>
                        <p>Hari Hadir</p>
                    </div>
                </div>

                <div class="stat-card izin">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $izin_count ?></h3>
                        <p>Hari Izin</p>
                    </div>
                </div>

                <div class="stat-card sakit">
                    <div class="stat-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $sakit_count ?></h3>
                        <p>Hari Sakit</p>
                    </div>
                </div>

                <div class="stat-card total">
                    <div class="stat-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $hadir_count + $izin_count + $sakit_count ?></h3>
                        <p>Total Kehadiran</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        mobileToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });

        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 992) {
                // HANYA cek jika sidebar sedang aktif
                if (sidebar.classList.contains('active')) {
                    if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                        sidebarOverlay.classList.remove('active');
                    }
                }
            }
        });

        // Update Clock
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            const clockElements = document.querySelectorAll('.page-header p');
            if (clockElements.length > 0) {
                const currentText = clockElements[0].innerHTML;
                clockElements[0].innerHTML = currentText.replace(/\d{2}:\d{2}/, time);
            }
        }

        // Update clock every minute
        setInterval(updateClock, 60000);

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function () {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => console.log('SW Registered'))
                .catch(error => console.log('SW Registration failed:', error));
        }
    </script>
</body>

</html>