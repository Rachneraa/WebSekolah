<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan login sebagai siswa
if (!isset($_SESSION['user_id']) || ($_SESSION['level'] ?? '') !== 'siswa') {
    header('Location: ../index.php');
    exit();
}

$siswa_id = (int) $_SESSION['user_id'];

// Ambil daftar mata pelajaran
$mapel_list = [];
$res = $db->query("SELECT id, nama FROM mapel ORDER BY nama");
if ($res) {
    while ($r = $res->fetch_assoc())
        $mapel_list[] = $r;
}

// Deteksi kolom di tabel `nilai` (kita asumsikan 'mapel' adalah string nama)
$mapel_col = 'mapel';

// Ambil filter mapel dari GET
// Di sini, filter_mapel adalah NAMA mapel (cth: "Matematika")
$filter_mapel = isset($_GET['mapel']) && $_GET['mapel'] !== '' ? $_GET['mapel'] : '';

// Siapkan variabel untuk data
$nilai_rows = [];
$tugas_list = []; // Untuk header tabel (Tugas 1, Tugas 2, ...)
$nilai_tugas_map = []; // Untuk nilai [1 => 10, 2 => 10]
$nilai_uts_uas = []; // Untuk nilai UTS & UAS

// Helper untuk menghitung rata-rata
function avg_score($tugas_map, $uts, $uas)
{
    $values = [];
    if (!empty($tugas_map)) {
        foreach ($tugas_map as $v) {
            if (is_numeric($v))
                $values[] = (float) $v;
        }
    }
    if (is_numeric($uts))
        $values[] = (float) $uts;
    if (is_numeric($uas))
        $values[] = (float) $uas;

    if (count($values) === 0)
        return null;
    return round(array_sum($values) / count($values), 2);
}


if ($filter_mapel !== '') {
    // === MODE RINCIAN (SATU MAPEL DIPILIH) ===

    // 1. Cari mapel_id (INT) dari nama mapel (STRING)
    $mapel_id_filter = 0;
    $mstmt = $db->prepare("SELECT id FROM mapel WHERE nama = ? LIMIT 1");
    $mstmt->bind_param("s", $filter_mapel);
    $mstmt->execute();
    $mr = $mstmt->get_result()->fetch_assoc();
    if ($mr) {
        $mapel_id_filter = (int) $mr['id'];
    }
    $mstmt->close();

    if ($mapel_id_filter > 0) {
        // 2. Ambil daftar tugas (Tugas 1, 2, 3...)
        $tstmt = $db->prepare("SELECT DISTINCT tugas_ke FROM nilai_tugas WHERE siswa_id = ? AND mapel_id = ? ORDER BY tugas_ke");
        $tstmt->bind_param("ii", $siswa_id, $mapel_id_filter);
        $tstmt->execute();
        $tres = $tstmt->get_result();
        while ($tr = $tres->fetch_assoc()) {
            $tugas_list[] = $tr['tugas_ke']; // Hasil: [1, 2]
        }
        $tstmt->close();

        // 3. Ambil nilai tugas
        $nstmt = $db->prepare("SELECT tugas_ke, nilai FROM nilai_tugas WHERE siswa_id = ? AND mapel_id = ?");
        $nstmt->bind_param("ii", $siswa_id, $mapel_id_filter);
        $nstmt->execute();
        $nres = $nstmt->get_result();
        while ($nr = $nres->fetch_assoc()) {
            $nilai_tugas_map[$nr['tugas_ke']] = $nr['nilai']; // Hasil: [1 => 10, 2 => 10]
        }
        $nstmt->close();
    }

    // 4. Ambil nilai UTS & UAS dari tabel 'nilai'
    $stmt = $db->prepare("SELECT uts, uas, created_at, mapel FROM nilai WHERE siswa_id = ? AND mapel = ?");
    $stmt->bind_param("is", $siswa_id, $filter_mapel);
    $stmt->execute();
    $res = $stmt->get_result();
    $nilai_uts_uas = $res->fetch_assoc(); // Hanya 1 baris
    $stmt->close();

} else {
    // === MODE RINGKASAN (SEMUA MAPEL) ===
    // Gunakan LEFT JOIN untuk memastikan baris dari `nilai` tetap muncul,
    // lalu ambil rata-rata dari `nilai_tugas` berdasarkan mapel_id yang di-join
    $sql = "SELECT n.id, n.created_at, n.mapel AS mapel, n.uts, n.uas, 
                   AVG(nt.nilai) AS rata_rata_tugas
            FROM nilai n
            LEFT JOIN mapel m ON m.nama = n.mapel
            LEFT JOIN nilai_tugas nt ON nt.siswa_id = n.siswa_id AND nt.mapel_id = m.id
            WHERE n.siswa_id = ?
            GROUP BY n.id, n.created_at, n.mapel, n.uts, n.uas
            ORDER BY n.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc())
        $nilai_rows[] = $r;
    $stmt->close();
}
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nilai - Portal Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
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
            box-sizing: border-box
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px
        }

        /* Mobile First Approach */
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
            transition: all 0.3s ease
        }

        .sidebar-header {
            padding: 0 25px 25px;
            border-bottom: 2px solid var(--border-color);
            text-align: center
        }

        .sidebar-header h4 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 5px
        }

        .sidebar-header p {
            color: var(--text-muted);
            font-size: 13px;
            margin: 0
        }

        .nav-menu {
            padding: 20px 0
        }

        .nav-item {
            margin: 5px 15px
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 12px;
            transition: all .3s ease;
            font-weight: 500;
            font-size: 14px
        }

        .nav-link:hover {
            background: var(--light-blue);
            color: var(--primary-blue);
            transform: translateX(5px)
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            box-shadow: 0 4px 15px rgba(0, 73, 157, 0.3)
        }

        .nav-link i {
            width: 24px;
            font-size: 18px;
            margin-right: 12px
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px
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
            text-decoration: none
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white
        }

        .main-content {
            margin-left: calc(var(--sidebar-width) + 40px);
            padding: 0 20px 20px 0;
            transition: all 0.3s ease
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08)
        }

        .page-header h2 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px
        }

        .page-header .user-name {
            color: var(--primary-orange);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px
        }

        .page-header p {
            color: var(--text-muted);
            margin: 0;
            font-size: 14px
        }

        .quick-actions {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08)
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px
        }

        .custom-table thead th {
            background: var(--light-blue);
            color: var(--primary-blue);
            padding: 15px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase
        }

        .custom-table tbody tr {
            background: #f8f9fa;
            transition: all .3s ease
        }

        .custom-table tbody td {
            padding: 15px;
            border: none;
            font-size: 14px;
            color: var(--text-dark)
        }

        /* --- AWAL CSS BARU UNTUK MOBILE (DARI jadwal.php) --- */

        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                right: -280px;
                /* Diubah dari 'transform' ke 'right' */
                left: auto;
                top: 0;
                height: 100%;
                border-radius: 20px 0 0 20px;
                /* Style dari jadwal.php */
                transition: right 0.3s ease;
                /* Transisi 'right' */
            }

            .sidebar.active {
                /* Menggunakan '.active' bukan '.mobile-open' */
                right: 0;
            }

            .main-content {
                margin-left: 0;
                margin-right: 0;
                margin-top: 70px;
                /* Menyesuaikan dengan 'jadwal.php' */
                padding: 0;
            }

            /* CSS yang ada di file nilai.php tapi tidak ada di jadwal.php (di dalam 992px) */
            .page-header {
                margin-top: 80px;
                padding: 20px;
                margin-bottom: 20px;
            }

            .page-header h2 {
                font-size: 24px;
            }

            .page-header .user-name {
                font-size: 22px;
            }
        }

        .mobile-toggle {
            /* Style untuk tombol baru */
            display: none;
            position: fixed;
            top: 30px;
            right: 30px;
            /* Posisi di kanan atas */
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

        @media (max-width: 992px) {
            .mobile-toggle {
                /* Tampilkan tombol di mobile */
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .sidebar-overlay {
            /* Style untuk overlay */
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* --- AKHIR CSS BARU UNTUK MOBILE --- */

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .page-header {
                padding: 15px;
                margin-bottom: 15px;
            }

            .card {
                padding: 15px;
                border-radius: 15px;
            }

            .custom-table {
                font-size: 0.8rem;
            }

            .custom-table thead th {
                padding: 10px 8px;
                font-size: 0.75rem;
            }

            .custom-table tbody td {
                padding: 10px 8px;
                font-size: 0.8rem;
            }

            /* Filter form mobile */
            #filterForm {
                flex-direction: column;
                align-items: stretch !important;
                gap: 8px !important;
                width: 100%;
            }

            #filterForm label {
                text-align: center;
                margin-bottom: 0;
            }

            #mapel {
                width: 100% !important;
                padding: 10px !important;
            }
        }

        @media (max-width: 576px) {
            .page-header {
                margin-top: 70px;
            }

            .page-header h2 {
                font-size: 20px;
            }

            .page-header .user-name {
                font-size: 18px;
            }

            .welcome-text {
                flex-direction: column;
                text-align: center;
                gap: 10px !important;
            }

            .welcome-icon {
                width: 40px !important;
                height: 40px !important;
                font-size: 18px !important;
            }

            /* Table responsive for mobile */
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .custom-table {
                min-width: 600px;
                /* Ensure horizontal scroll on small screens */
            }

            /* Card header mobile */
            .card h5 {
                font-size: 1rem;
                text-align: center;
                width: 100%;
            }
        }

        /* Score badges for better mobile display */
        .score-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
            min-width: 40px;
            text-align: center;
        }

        .score-excellent {
            background: #d4edda;
            color: #155724;
        }

        .score-good {
            background: #d1ecf1;
            color: #0c5460;
        }

        .score-average {
            background: #fff3cd;
            color: #856404;
        }

        .score-poor {
            background: #f8d7da;
            color: #721c24;
        }

        /* Empty state mobile */
        .empty-state {
            padding: 20px !important;
        }

        .empty-state i {
            font-size: 32px !important;
        }

        /* Touch improvements */
        .btn,
        .nav-link,
        select,
        .mobile-toggle {
            /* diubah dari .mobile-menu-btn */
            min-height: 44px;
        }

        .nav-link {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-graduation-cap"></i> Portal Siswa</h4>
            <p>SMK TI Garuda Nusantara</p>
        </div>
        <nav class="nav-menu">
            <div class="nav-item"><a href="dashboard.php" class="nav-link"><i
                        class="fas fa-home"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="absensi.php" class="nav-link"><i class="fas fa-qrcode"></i><span>Absensi
                        QR</span></a></div>
            <div class="nav-item"><a href="jadwal.php" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Jadwal
                        Pelajaran</span></a></div>
            <div class="nav-item"><a href="nilai.php" class="nav-link active"><i
                        class="fas fa-chart-line"></i><span>Nilai</span></a></div>
            <div class="nav-item">
                <a href="pengaturan.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan Akun</span>
                </a>
            </div>
        </nav>
        </nav>
        <div class="logout-section">
            <a href="../config/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div class="welcome-text" style="display:flex;align-items:center;gap:15px">
                <div class="welcome-icon"
                    style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary-orange),#ff6b00);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:24px">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="welcome-content">
                    <h2>Form Nilai Siswa</h2>
                    <div class="user-name">
                        <?= htmlspecialchars($_SESSION['nama_siswa'] ?? ($_SESSION['username'] ?? '')) ?>
                    </div>
                    <p><i class="fas fa-calendar"></i> <?= date('l, d F Y') ?> | <i class="fas fa-clock"></i>
                        <?= date('H:i') ?> WIB</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div
                style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:12px">
                <h5 style="margin:0;color:var(--primary-blue)"><i class="fas fa-table"></i> Daftar Nilai</h5>

                <form method="get" id="filterForm" style="display:flex;gap:10px;align-items:center">
                    <label for="mapel" style="margin:0;font-weight:600;color:var(--text-dark)">Filter Mata
                        Pelajaran</label>
                    <select name="mapel" id="mapel" onchange="document.getElementById('filterForm').submit()"
                        style="padding:8px;border-radius:8px;border:1px solid #e6e9ef">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        <?php foreach ($mapel_list as $m):
                            // Filter harus menggunakan NAMA mapel
                            $val = $m['nama'];
                            $label = $m['nama'];
                            ?>
                            <option value="<?= htmlspecialchars($val) ?>" <?= ($filter_mapel === $val) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="table-container">
                <table class="custom-table" aria-describedby="daftar-nilai">

                    <?php if ($filter_mapel !== ''): ?>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mapel</th>
                                <?php
                                if (!empty($tugas_list)) {
                                    foreach ($tugas_list as $tugas_ke) {
                                        echo "<th>T" . htmlspecialchars($tugas_ke) . "</th>";
                                    }
                                } else {
                                    echo "<th>Tugas</th>"; // Fallback
                                }
                                ?>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Rata²</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Cek jika ada data UTS/UAS ATAU data tugas
                            if ($nilai_uts_uas || !empty($nilai_tugas_map)):
                                $uts = $nilai_uts_uas['uts'] ?? null;
                                $uas = $nilai_uts_uas['uas'] ?? null;
                                $mapel_name = $nilai_uts_uas['mapel'] ?? $filter_mapel;
                                $created = $nilai_uts_uas['created_at'] ?? null;
                                $avg = avg_score($nilai_tugas_map, $uts, $uas);

                                // Determine score badge class
                                $avg_class = '';
                                if ($avg !== null) {
                                    if ($avg >= 85)
                                        $avg_class = 'score-excellent';
                                    elseif ($avg >= 70)
                                        $avg_class = 'score-good';
                                    elseif ($avg >= 60)
                                        $avg_class = 'score-average';
                                    else
                                        $avg_class = 'score-poor';
                                }
                                ?>
                                <tr>
                                    <td><?= $created ? htmlspecialchars(date('d M Y', strtotime($created))) : '—' ?></td>
                                    <td><strong><?= htmlspecialchars($mapel_name) ?></strong></td>

                                    <?php
                                    if (!empty($tugas_list)) {
                                        foreach ($tugas_list as $tugas_ke) {
                                            $nilai = $nilai_tugas_map[$tugas_ke] ?? '—';
                                            $score_class = '';
                                            if (is_numeric($nilai)) {
                                                if ($nilai >= 85)
                                                    $score_class = 'score-excellent';
                                                elseif ($nilai >= 70)
                                                    $score_class = 'score-good';
                                                elseif ($nilai >= 60)
                                                    $score_class = 'score-average';
                                                else
                                                    $score_class = 'score-poor';
                                            }

                                            if (is_numeric($nilai)) {
                                                echo "<td><span class='score-badge $score_class'>" . htmlspecialchars($nilai) . "</span></td>";
                                            } else {
                                                echo "<td>—</td>";
                                            }
                                        }
                                    } else {
                                        echo "<td>—</td>"; // Fallback
                                    }
                                    ?>

                                    <td>
                                        <?php if (is_numeric($uts)):
                                            $uts_class = '';
                                            if ($uts >= 85)
                                                $uts_class = 'score-excellent';
                                            elseif ($uts >= 70)
                                                $uts_class = 'score-good';
                                            elseif ($uts >= 60)
                                                $uts_class = 'score-average';
                                            else
                                                $uts_class = 'score-poor';
                                            ?>
                                            <span class="score-badge <?= $uts_class ?>"><?= htmlspecialchars($uts) ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (is_numeric($uas)):
                                            $uas_class = '';
                                            if ($uas >= 85)
                                                $uas_class = 'score-excellent';
                                            elseif ($uas >= 70)
                                                $uas_class = 'score-good';
                                            elseif ($uas >= 60)
                                                $uas_class = 'score-average';
                                            else
                                                $uas_class = 'score-poor';
                                            ?>
                                            <span class="score-badge <?= $uas_class ?>"><?= htmlspecialchars($uas) ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($avg !== null): ?>
                                            <span class="score-badge <?= $avg_class ?>"><?= htmlspecialchars($avg) ?></span>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="<?= 5 + (empty($tugas_list) ? 1 : count($tugas_list)) ?>">
                                        <div class="empty-state" style="padding:30px;text-align:center;color:var(--text-muted)">
                                            <i class="fas fa-file-alt" style="font-size:42px;opacity:0.3"></i>
                                            <h5 style="margin-top:10px">Belum ada data nilai</h5>
                                            <p class="mt-2">Pilih mata pelajaran lain atau coba lagi nanti</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    <?php else: ?>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Mapel</th>
                                <th>Tugas</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Rata²</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($nilai_rows) === 0): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="padding:30px;text-align:center;color:var(--text-muted)">
                                            <i class="fas fa-file-alt" style="font-size:42px;opacity:0.3"></i>
                                            <h5 style="margin-top:10px">Belum ada data nilai</h5>
                                            <p class="mt-2">Nilai akan muncul setelah guru menginput data</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($nilai_rows as $row):
                                    $tugas_avg = $row['rata_rata_tugas'] ?? null;
                                    $uts = $row['uts'] ?? null;
                                    $uas = $row['uas'] ?? null;
                                    $mapel_name = $row['mapel'] ?? '—';
                                    $created = $row['created_at'] ?? null;
                                    // Hitung rata-rata akhir (tugas avg, uts, uas)
                                    $avg = avg_score([$tugas_avg], $uts, $uas);

                                    // Determine score badge classes
                                    $tugas_class = $tugas_avg ? ($tugas_avg >= 85 ? 'score-excellent' : ($tugas_avg >= 70 ? 'score-good' : ($tugas_avg >= 60 ? 'score-average' : 'score-poor'))) : '';
                                    $uts_class = $uts ? ($uts >= 85 ? 'score-excellent' : ($uts >= 70 ? 'score-good' : ($uts >= 60 ? 'score-average' : 'score-poor'))) : '';
                                    $uas_class = $uas ? ($uas >= 85 ? 'score-excellent' : ($uas >= 70 ? 'score-good' : ($uas >= 60 ? 'score-average' : 'score-poor'))) : '';
                                    $avg_class = $avg ? ($avg >= 85 ? 'score-excellent' : ($avg >= 70 ? 'score-good' : ($avg >= 60 ? 'score-average' : 'score-poor'))) : '';
                                    ?>
                                    <tr>
                                        <td><?= $created ? htmlspecialchars(date('d M Y', strtotime($created))) : '—' ?></td>
                                        <td><strong><?= htmlspecialchars($mapel_name) ?></strong></td>
                                        <td>
                                            <?php if (is_numeric($tugas_avg)): ?>
                                                <span
                                                    class="score-badge <?= $tugas_class ?>"><?= htmlspecialchars(round($tugas_avg, 1)) ?></span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (is_numeric($uts)): ?>
                                                <span class="score-badge <?= $uts_class ?>"><?= htmlspecialchars($uts) ?></span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (is_numeric($uas)): ?>
                                                <span class="score-badge <?= $uas_class ?>"><?= htmlspecialchars($uas) ?></span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($avg !== null): ?>
                                                <span class="score-badge <?= $avg_class ?>"><?= htmlspecialchars($avg) ?></span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    <?php endif; ?>

                </table>
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
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            }
        });
        // SERVICE WORKER REGISTRATION
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => console.log('SW Registered'))
                .catch(error => console.log('SW Registration failed:', error));
        }
    </script>
</body>

</html>