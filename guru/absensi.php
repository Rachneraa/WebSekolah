<?php
ob_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

require_once dirname(__DIR__) . '/config/koneksi.php';

// Pastikan user adalah guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

$guru_id = $_SESSION['user_id'];

// Ambil daftar kelas yang diajar guru ini
$kelas_list = [];
$stmt = $db->prepare("SELECT DISTINCT k.kelas_id, k.nama 
    FROM jadwal j
    JOIN kelas k ON j.kelas_id = k.kelas_id
    WHERE j.guru_id = ?");
$stmt->bind_param("i", $guru_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc())
    $kelas_list[] = $row;
$stmt->close();

$kelas_filter = isset($_GET['kelas']) ? intval($_GET['kelas']) : 0;
$tanggal = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Ambil nama kelas
$kelas_nama = '';
if ($kelas_filter) {
    $kelas_stmt = $db->prepare("SELECT nama FROM kelas WHERE kelas_id = ?");
    $kelas_stmt->bind_param("i", $kelas_filter);
    $kelas_stmt->execute();
    $kelas_stmt->bind_result($kelas_nama);
    $kelas_stmt->fetch();
    $kelas_stmt->close();
}

// Ambil daftar siswa di kelas
$siswa_list = [];
if ($kelas_filter) {
    $siswa_stmt = $db->prepare("SELECT siswa_id, nama FROM siswa WHERE kelas_id = ?");
    $siswa_stmt->bind_param("i", $kelas_filter);
    $siswa_stmt->execute();
    $siswa_result = $siswa_stmt->get_result();
    while ($row = $siswa_result->fetch_assoc()) {
        $siswa_list[] = $row;
    }
    $siswa_stmt->close();
}

// Ambil absensi detail & alasan per siswa
$absensi_map = [];
if ($kelas_filter) {
    $absensi_stmt = $db->prepare("SELECT a.siswa_id, a.status, al.alasan 
        FROM absensi_detail a 
        LEFT JOIN absensi_alasan al ON a.absensi_id = al.absensi_id AND a.siswa_id = al.siswa_id
        WHERE a.kelas_id = ? AND a.tanggal = ?");
    $absensi_stmt->bind_param("is", $kelas_filter, $tanggal);
    $absensi_stmt->execute();
    $absensi_result = $absensi_stmt->get_result();
    while ($row = $absensi_result->fetch_assoc()) {
        $absensi_map[$row['siswa_id']] = $row;
    }
    $absensi_stmt->close();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container py-4">
    <h3>Absensi Siswa</h3>
    <form method="get" action="guru.php" class="row g-3 mb-3">
        <input type="hidden" name="page" value="absensi">
        <div class="col-md-4">
            <label for="kelas" class="form-label">Pilih Kelas</label>
            <select name="kelas" id="kelas" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['kelas_id'] ?>" <?= $kelas_filter == $k['kelas_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tanggal</label>
            <input type="date" name="date" id="date" class="form-control" value="<?= htmlspecialchars($tanggal) ?>"
                onchange="this.form.submit()">
        </div>
    </form>

    <?php if ($kelas_filter && $siswa_list): ?>
        <div class="card shadow">
            <div class="card-header">
                <b>Kelas:</b> <?= htmlspecialchars($kelas_nama) ?> | <b>Tanggal:</b> <?= htmlspecialchars($tanggal) ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Status</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa_list as $i => $siswa):
                            $abs = $absensi_map[$siswa['siswa_id']] ?? null;
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($siswa['nama']) ?></td>
                                <td><?= $abs['status'] ?? '-' ?></td>
                                <td><?= $abs['alasan'] ?? '-' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($kelas_filter): ?>
        <div class="alert alert-warning mt-3">Tidak ada siswa di kelas ini.</div>
    <?php endif ?>
</div>