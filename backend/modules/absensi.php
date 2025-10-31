<?php
// Modul: Absensi (Admin view) — edit alasan dinonaktifkan untuk admin

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// koneksi.php sudah di-require oleh admin.php (file induk)

// Ambil filter kelas dari GET
$kelas_filter = isset($_GET['kelas']) ? intval($_GET['kelas']) : 0;

if (!$kelas_filter) {
    echo "<div class='alert alert-warning m-4'>Silakan pilih kelas terlebih dahulu dari halaman <a href='?page=kelas' class='alert-link'>Data Kelas</a>.</div>";
    return; // hentikan eksekusi modul
}

// Ambil nama kelas
$kelas_nama = '';
$kelas_stmt = $db->prepare("SELECT nama FROM kelas WHERE kelas_id = ?");
if ($kelas_stmt) {
    $kelas_stmt->bind_param("i", $kelas_filter);
    $kelas_stmt->execute();
    $kelas_stmt->bind_result($kelas_nama);
    $kelas_stmt->fetch();
    $kelas_stmt->close();
}

// Ambil daftar siswa di kelas
$siswa_stmt = $db->prepare("SELECT siswa_id, nama FROM siswa WHERE kelas_id = ? ORDER BY nama ASC");
if ($siswa_stmt) {
    $siswa_stmt->bind_param("i", $kelas_filter);
    $siswa_stmt->execute();
    $siswa_result = $siswa_stmt->get_result();
} else {
    echo "<div class='alert alert-danger m-4'>Gagal mengambil data siswa: " . htmlspecialchars($db->error) . "</div>";
    return;
}

$tanggal = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Absensi Kelas <?= htmlspecialchars($kelas_nama) ?></h4>
                    <small><?= date('d M Y', strtotime($tanggal)) ?></small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form method="get" action="admin.php" class="row g-2 align-items-center">
                            <input type="hidden" name="page" value="absensi">
                            <input type="hidden" name="kelas" value="<?= $kelas_filter ?>">
                            <div class="col-auto">
                                <label for="filterTanggal" class="form-label mb-0">Tanggal Absensi:</label>
                            </div>
                            <div class="col-auto">
                                <input type="date" id="filterTanggal" name="date" class="form-control"
                                    value="<?= htmlspecialchars($tanggal) ?>">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Tampilkan
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Waktu Absen</th>
                                    <th>Alasan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                // 1. Ambil semua data 'Hadir' (QR Scan)
                                $hadir_map = [];
                                $absen_stmt = $db->prepare("SELECT siswa_id, waktu_absen, status FROM absensi_detail WHERE kelas_id = ? AND tanggal = ? AND status = 'Hadir'");
                                $absen_stmt->bind_param("is", $kelas_filter, $tanggal);
                                $absen_stmt->execute();
                                $absen_result = $absen_stmt->get_result();
                                while ($absen = $absen_result->fetch_assoc()) {
                                    $hadir_map[$absen['siswa_id']] = $absen;
                                }
                                $absen_stmt->close();

                                // 2. Ambil semua data 'Alasan' (Admin Input)
                                $alasan_map = [];
                                $alasan_stmt = $db->prepare("SELECT siswa_id, alasan, status, waktu_alasan FROM absensi_alasan WHERE kelas_id = ? AND tanggal = ?");
                                $alasan_stmt->bind_param("is", $kelas_filter, $tanggal);
                                $alasan_stmt->execute();
                                $alasan_result = $alasan_stmt->get_result();
                                while ($alasan_row = $alasan_result->fetch_assoc()) {
                                    $alasan_map[$alasan_row['siswa_id']] = $alasan_row;
                                }
                                $alasan_stmt->close();

                                // 3. Loop daftar siswa
                                while ($row = $siswa_result->fetch_assoc()) {
                                    $siswa_id = $row['siswa_id'];

                                    // Tentukan status, prioritas 1 adalah data dari Admin
                                    if (isset($alasan_map[$siswa_id]) && !empty($alasan_map[$siswa_id]['status'])) {
                                        $status_text = $alasan_map[$siswa_id]['status'];
                                        $waktu = $alasan_map[$siswa_id]['waktu_alasan'] ? date('H:i', strtotime($alasan_map[$siswa_id]['waktu_alasan'])) : '-';
                                        $alasan = htmlspecialchars($alasan_map[$siswa_id]['alasan']);
                                    }
                                    // Prioritas 2 adalah data dari QR
                                    elseif (isset($hadir_map[$siswa_id])) {
                                        $status_text = $hadir_map[$siswa_id]['status'];
                                        $waktu = $hadir_map[$siswa_id]['waktu_absen'] ? date('H:i', strtotime($hadir_map[$siswa_id]['waktu_absen'])) : '-';
                                        $alasan = '-';
                                    }
                                    // Default
                                    else {
                                        $status_text = 'Tidak Hadir';
                                        $waktu = '-';
                                        $alasan = '-';
                                    }

                                    // Tombol edit alasan dinonaktifkan untuk admin (admin tidak boleh mengedit alasan siswa)
                                    $alasan_btn = '';

                                    // Tentukan warna badge
                                    $status_class = 'bg-secondary'; // Default
                                    if ($status_text == 'Hadir')
                                        $status_class = 'bg-success';
                                    if ($status_text == 'Sakit')
                                        $status_class = 'bg-warning text-dark';
                                    if ($status_text == 'Izin')
                                        $status_class = 'bg-info text-dark';
                                    if ($status_text == 'Alpha' || $status_text == 'Tidak Hadir')
                                        $status_class = 'bg-danger';

                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>" . htmlspecialchars($row['nama']) . "</td>
                                        <td><span class='badge " . $status_class . "'>$status_text</span></td>
                                        <td>$waktu</td>
                                        <td>$alasan</td>
                                        <td>" . $alasan_btn . "</td>
                                    </tr>";
                                    $no++;
                                }
                                $siswa_stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="?page=kelas" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali ke
                        Data Kelas</a>
                </div>
            </div>
        </div>
    </div>
</div>