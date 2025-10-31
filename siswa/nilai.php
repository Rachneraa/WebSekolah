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
    while ($r = $res->fetch_assoc()) $mapel_list[] = $r;
}

// Deteksi kolom di tabel `nilai`
$nilai_columns = [];
$colRes = $db->query("SHOW COLUMNS FROM nilai");
if ($colRes) {
    while ($c = $colRes->fetch_assoc()) $nilai_columns[] = $c['Field'];
}

// Tentukan kolom relasi mapel (bisa mapel_id, mata_pelajaran_id, atau mapel (nama string))
$mapel_col = null;
if (in_array('mapel_id', $nilai_columns)) {
    $mapel_col = 'mapel_id';
} elseif (in_array('mata_pelajaran_id', $nilai_columns)) {
    $mapel_col = 'mata_pelajaran_id';
} elseif (in_array('mapel', $nilai_columns)) {
    $mapel_col = 'mapel'; // menyimpan nama mapel (string)
}

// Ambil filter mapel dari GET
$filter_mapel = isset($_GET['mapel']) && $_GET['mapel'] !== '' ? $_GET['mapel'] : '';

// Siapkan query berdasarkan struktur
$nilai_rows = [];
if ($mapel_col === 'mapel' || $mapel_col === null) {
    // kolom mapel adalah string atau tidak ditemukan kolom FK -> ambil langsung tanpa JOIN
    if ($filter_mapel !== '') {
        $stmt = $db->prepare("SELECT n.tugas, n.uts, n.uas, n.created_at, n.mapel AS mapel FROM nilai n WHERE n.siswa_id = ? AND n.mapel = ? ORDER BY n.created_at DESC");
        $stmt->bind_param("is", $siswa_id, $filter_mapel);
    } else {
        $stmt = $db->prepare("SELECT n.tugas, n.uts, n.uas, n.created_at, n.mapel AS mapel FROM nilai n WHERE n.siswa_id = ? ORDER BY n.created_at DESC");
        $stmt->bind_param("i", $siswa_id);
    }
} else {
    // kolom mapel numeric -> JOIN ke tabel mapel
    if ($filter_mapel !== '') {
        // jika filter berupa id numeric, gunakan id; jika bukan, cari id dari nama
        if (ctype_digit((string)$filter_mapel)) {
            $fid = (int)$filter_mapel;
            $stmt = $db->prepare("SELECT n.tugas, n.uts, n.uas, n.created_at, m.nama AS mapel FROM nilai n LEFT JOIN mapel m ON n.$mapel_col = m.id WHERE n.siswa_id = ? AND n.$mapel_col = ? ORDER BY n.created_at DESC");
            $stmt->bind_param("ii", $siswa_id, $fid);
        } else {
            // cari id mapel dari nama
            $mstmt = $db->prepare("SELECT id FROM mapel WHERE nama = ? LIMIT 1");
            $mstmt->bind_param("s", $filter_mapel);
            $mstmt->execute();
            $mr = $mstmt->get_result()->fetch_assoc();
            $mid = $mr['id'] ?? 0;
            $mstmt->close();
            $stmt = $db->prepare("SELECT n.tugas, n.uts, n.uas, n.created_at, m.nama AS mapel FROM nilai n LEFT JOIN mapel m ON n.$mapel_col = m.id WHERE n.siswa_id = ? AND n.$mapel_col = ? ORDER BY n.created_at DESC");
            $stmt->bind_param("ii", $siswa_id, $mid);
        }
    } else {
        $stmt = $db->prepare("SELECT n.tugas, n.uts, n.uas, n.created_at, m.nama AS mapel FROM nilai n LEFT JOIN mapel m ON n.$mapel_col = m.id WHERE n.siswa_id = ? ORDER BY n.created_at DESC");
        $stmt->bind_param("i", $siswa_id);
    }
}

// Eksekusi dan ambil hasil
if (isset($stmt) && $stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $nilai_rows[] = $r;
    $stmt->close();
}

// Helper untuk menghitung rata-rata (jika kolom numeric ada)
function avg_score($t, $u, $a) {
    $t = is_numeric($t) ? (float)$t : null;
    $u = is_numeric($u) ? (float)$u : null;
    $a = is_numeric($a) ? (float)$a : null;
    $values = array_filter([$t,$u,$a], function($v){ return $v !== null; });
    if (count($values) === 0) return null;
    return round(array_sum($values)/count($values),2);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Nilai - Portal Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* SALINAN STYLE DARI dashboard.php (tidak diubah) */
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
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;padding:20px}
        .sidebar{position:fixed;top:20px;left:20px;width:var(--sidebar-width);height:calc(100vh - 40px);background:white;border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.1);padding:30px 0;overflow-y:auto;z-index:1000}
        .sidebar-header{padding:0 25px 25px;border-bottom:2px solid var(--border-color);text-align:center}
        .sidebar-header h4{color:var(--primary-blue);font-weight:700;font-size:18px;margin-bottom:5px}
        .sidebar-header p{color:var(--text-muted);font-size:13px;margin:0}
        .nav-menu{padding:20px 0}
        .nav-item{margin:5px 15px}
        .nav-link{display:flex;align-items:center;padding:15px 20px;color:var(--text-dark);text-decoration:none;border-radius:12px;transition:all .3s ease;font-weight:500;font-size:14px}
        .nav-link:hover{background:var(--light-blue);color:var(--primary-blue);transform:translateX(5px)}
        .nav-link.active{background:linear-gradient(135deg,var(--primary-blue),var(--dark-blue));color:white;box-shadow:0 4px 15px rgba(0,73,157,0.3)}
        .nav-link i{width:24px;font-size:18px;margin-right:12px}
        .logout-section{position:absolute;bottom:20px;left:15px;right:15px}
        .btn-logout{width:100%;background:linear-gradient(135deg,var(--danger-red),#c82333);color:white;border:none;padding:12px;border-radius:12px;font-weight:600;font-size:14px;display:flex;align-items:center;justify-content:center;gap:10px;text-decoration:none}
        .btn-logout:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(220,53,69,0.4);color:white}
        .main-content{margin-left:calc(var(--sidebar-width) + 40px);padding:0 20px 20px 0}
        .page-header{background:white;border-radius:20px;padding:30px;margin-bottom:30px;box-shadow:0 5px 20px rgba(0,0,0,0.08)}
        .page-header h2{color:var(--primary-blue);font-weight:700;font-size:28px;margin-bottom:10px}
        .page-header .user-name{color:var(--primary-orange);font-weight:700;font-size:28px;margin-bottom:10px}
        .page-header p{color:var(--text-muted);margin:0;font-size:14px}
        .quick-actions{background:white;border-radius:20px;padding:30px;box-shadow:0 5px 20px rgba(0,0,0,0.08);margin-bottom:30px}
        .content-grid{display:grid;grid-template-columns:1fr 2fr;gap:30px;margin-bottom:30px}
        .card{background:white;border-radius:20px;padding:20px;box-shadow:0 5px 20px rgba(0,0,0,0.08)}
        .custom-table{width:100%;border-collapse:separate;border-spacing:0 10px}
        .custom-table thead th{background:var(--light-blue);color:var(--primary-blue);padding:15px;font-weight:600;font-size:13px;text-transform:uppercase}
        .custom-table tbody tr{background:#f8f9fa;transition:all .3s ease}
        .custom-table tbody td{padding:15px;border:none;font-size:14px;color:var(--text-dark)}
        @media (max-width: 992px) {.main-content{margin-left:0;margin-top:70px;padding:0}}
        @media (max-width: 576px){body{padding:10px}.page-header{padding:15px;margin-bottom:15px}}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-graduation-cap"></i> Portal Siswa</h4>
            <p>SMK TI Garuda Nusantara</p>
        </div>
        <nav class="nav-menu">
            <div class="nav-item"><a href="dashboard.php" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a></div>
            <div class="nav-item"><a href="absensi.php" class="nav-link"><i class="fas fa-qrcode"></i><span>Absensi QR</span></a></div>
            <div class="nav-item"><a href="jadwal.php" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Jadwal Pelajaran</span></a></div>
            <div class="nav-item"><a href="nilai.php" class="nav-link active"><i class="fas fa-chart-line"></i><span>Nilai</span></a></div>
        </nav>
        <div class="logout-section">
            <a href="../config/logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <main class="main-content">
        <div class="page-header">
            <div class="welcome-text" style="display:flex;align-items:center;gap:15px">
                <div class="welcome-icon" style="width:50px;height:50px;background:linear-gradient(135deg,var(--primary-orange),#ff6b00);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:24px">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="welcome-content">
                    <h2>Form Nilai Siswa</h2>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['nama'] ?? ($_SESSION['username'] ?? '')) ?></div>
                    <p><i class="fas fa-calendar"></i> <?= date('l, d F Y') ?> | <i class="fas fa-clock"></i> <?= date('H:i') ?> WIB</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:12px">
                <h5 style="margin:0;color:var(--primary-blue)"><i class="fas fa-table"></i> Daftar Nilai</h5>

                <form method="get" id="filterForm" style="display:flex;gap:10px;align-items:center">
                    <label for="mapel" style="margin:0;font-weight:600;color:var(--text-dark)">Filter Mata Pelajaran</label>
                    <select name="mapel" id="mapel" onchange="document.getElementById('filterForm').submit()" style="padding:8px;border-radius:8px;border:1px solid #e6e9ef">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        <?php foreach ($mapel_list as $m): 
                            $val = $m['id'];
                            $label = $m['nama'];
                            // jika mapel_col adalah teks dan filter sebenarnya menyimpan nama, biarkan value = nama
                            if ($mapel_col === 'mapel' || $mapel_col === null) $val = $label;
                        ?>
                            <option value="<?= htmlspecialchars($val) ?>" <?= ($filter_mapel !== '' && (string)$filter_mapel === (string)$val) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="table-container">
                <table class="custom-table" aria-describedby="daftar-nilai">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Mata Pelajaran</th>
                            <th>Nilai Tugas</th>
                            <th>Nilai UTS</th>
                            <th>Nilai UAS</th>
                            <th>Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($nilai_rows) === 0): ?>
                            <tr><td colspan="6">
                                <div class="empty-state" style="padding:30px;text-align:center;color:var(--text-muted)">
                                    <i class="fas fa-file-alt" style="font-size:42px;opacity:0.3"></i>
                                    <h5 style="margin-top:10px">Belum ada data nilai</h5>
                                    <p style="margin:0">Nilai Anda belum tersedia.</p>
                                </div>
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($nilai_rows as $row): 
                                $tugas = $row['tugas'] ?? $row['tugas'];
                                $uts = $row['uts'] ?? $row['uts'];
                                $uas = $row['uas'] ?? $row['uas'];
                                $mapel_name = $row['mapel'] ?? '—';
                                $created = $row['created_at'] ?? null;
                                $avg = avg_score($tugas, $uts, $uas);
                            ?>
                                <tr>
                                    <td><?= $created ? htmlspecialchars(date('d M Y', strtotime($created))) : '—' ?></td>
                                    <td><?= htmlspecialchars($mapel_name) ?></td>
                                    <td><?= is_numeric($tugas) ? htmlspecialchars($tugas) : '—' ?></td>
                                    <td><?= is_numeric($uts) ? htmlspecialchars($uts) : '—' ?></td>
                                    <td><?= is_numeric($uas) ? htmlspecialchars($uas) : '—' ?></td>
                                    <td><?= $avg !== null ? htmlspecialchars($avg) : '—' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>