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

$guru_id = $_SESSION['guru_id'];

// -------------------------
// Handle saving/updating alasan (robust: ensure absensi_id exists)
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_alasan') {
    $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
    $kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;
    $tanggal_post = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
    $status_post = isset($_POST['status']) ? $_POST['status'] : '';
    $alasan_post = isset($_POST['alasan']) ? trim($_POST['alasan']) : '';

    $allowed = ['Sakit', 'Izin', 'Alpha', '-'];
    if (!in_array($status_post, $allowed, true)) {
        $error_msg = 'Status tidak valid.';
    } else {
        // Cari record absensi_detail (ambil id dan status jika ada)
        $det_stmt = $db->prepare("SELECT id, status FROM absensi_detail WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?");
        $det_stmt->bind_param('iis', $siswa_id, $kelas_id, $tanggal_post);
        $det_stmt->execute();
        $det_res = $det_stmt->get_result();
        $absensi_detail_id = null;
        $existing_status = null;
        if ($row = $det_res->fetch_assoc()) {
            $absensi_detail_id = $row['id'];
            $existing_status = $row['status'];
        }
        $det_stmt->close();

        if ($existing_status === 'Hadir') {
            $error_msg = 'Tidak dapat mengubah alasan: siswa sudah tercatat Hadir.';
        } else {
            // Jika tidak ada absensi_detail, buat baru (pakai status yang dipilih)
            if (!$absensi_detail_id) {
                $nama_siswa = '';
                $get_nama = $db->prepare("SELECT nama FROM siswa WHERE siswa_id = ?");
                $get_nama->bind_param('i', $siswa_id);
                $get_nama->execute();
                $get_nama->bind_result($nama_siswa);
                $get_nama->fetch();
                $get_nama->close();

                $waktu_absen = date('H:i:s');
                $ins_det = $db->prepare("INSERT INTO absensi_detail (siswa_id, kelas_id, tanggal, status, nama_siswa, waktu_absen) VALUES (?, ?, ?, ?, ?, ?)");
                $ins_det->bind_param('iissss', $siswa_id, $kelas_id, $tanggal_post, $status_post, $nama_siswa, $waktu_absen);
                $ins_det->execute();
                $absensi_detail_id = $db->insert_id;
                $ins_det->close();
            } else {
                // Update status in absensi_detail to new status (if not '-')
                if ($status_post !== '-') {
                    $upd_det = $db->prepare("UPDATE absensi_detail SET status = ? WHERE id = ?");
                    $upd_det->bind_param('si', $status_post, $absensi_detail_id);
                    $upd_det->execute();
                    $upd_det->close();
                }
            }

            // Sekarang insert/update ke absensi_alasan menggunakan absensi_id = absensi_detail.id
            $check_alasan = $db->prepare("SELECT id FROM absensi_alasan WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?");
            $check_alasan->bind_param('iis', $siswa_id, $kelas_id, $tanggal_post);
            $check_alasan->execute();
            $check_alasan->store_result();
            if ($check_alasan->num_rows > 0) {
                $check_alasan->close();
                $update_alasan = $db->prepare("UPDATE absensi_alasan SET alasan = ?, status = ?, absensi_id = ?, dibuat_oleh = 'guru' WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?");
                $update_alasan->bind_param('ssiiis', $alasan_post, $status_post, $absensi_detail_id, $siswa_id, $kelas_id, $tanggal_post);
                $update_alasan->execute();
                $update_alasan->close();
            } else {
                $check_alasan->close();
                $insert_alasan = $db->prepare("INSERT INTO absensi_alasan (siswa_id, kelas_id, tanggal, alasan, status, dibuat_oleh, absensi_id) VALUES (?, ?, ?, ?, ?, 'guru', ?)");
                $insert_alasan->bind_param('iisssi', $siswa_id, $kelas_id, $tanggal_post, $alasan_post, $status_post, $absensi_detail_id);
                $insert_alasan->execute();
                $insert_alasan->close();
            }

            $success_msg = 'Alasan berhasil disimpan.';
        }
    }
}

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


// ===================================================================
// == PERUBAHAN LOGIKA FILTER ADA DI SINI ==
// ===================================================================
$tanggal = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Logika baru untuk $kelas_filter
$kelas_filter = 0; // Set default
if (isset($_GET['kelas'])) {
    // 1. Jika ada kelas di URL (user mengklik), pakai itu
    $kelas_filter = intval($_GET['kelas']);
} elseif (!empty($kelas_list)) {
    // 2. Jika TIDAK ada di URL (halaman baru dibuka) & daftar kelas tidak kosong,
    //    otomatis pilih kelas pertama dari daftar
    $kelas_filter = $kelas_list[0]['kelas_id'];
}
// Jika daftar kelas kosong, $kelas_filter akan tetap 0 (aman)
// ===================================================================
// == AKHIR PERUBAHAN LOGIKA ==
// ===================================================================


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


// =Lanjutan kode query (hadir_map, alasan_map)...
// ... (Saya persingkat, kode ini tidak berubah) ...

// 1. Ambil data Hadir (dari Scan QR di absensi_detail)
$hadir_map = [];
if ($kelas_filter) {
    $hadir_stmt = $db->prepare("SELECT siswa_id, status 
        FROM absensi_detail 
        WHERE kelas_id = ? AND tanggal = ? AND status = 'Hadir'");
    $hadir_stmt->bind_param("is", $kelas_filter, $tanggal);
    $hadir_stmt->execute();
    $hadir_result = $hadir_stmt->get_result();
    while ($row = $hadir_result->fetch_assoc()) {
        $hadir_map[$row['siswa_id']] = $row;
    }
    $hadir_stmt->close();
}

// 2. Ambil data Alasan (Izin, Sakit, Alpha dari Admin di absensi_alasan)
$alasan_map = [];
if ($kelas_filter) {
    $alasan_stmt = $db->prepare("SELECT siswa_id, status, alasan 
        FROM absensi_alasan 
        WHERE kelas_id = ? AND tanggal = ?");
    $alasan_stmt->bind_param("is", $kelas_filter, $tanggal);
    $alasan_stmt->execute();
    $alasan_result = $alasan_stmt->get_result();
    while ($row = $alasan_result->fetch_assoc()) {
        $alasan_map[$row['siswa_id']] = $row;
    }
    $alasan_stmt->close();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="container py-4">
    <h3>Absensi Siswa</h3>
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <form method="get" action="guru.php" class="row g-3 mb-3" id="filterAbsensiForm">
        <input type="hidden" name="page" value="absensi">
        <input type="hidden" name="kelas" value="<?= htmlspecialchars($kelas_filter) ?>">

        <div class="col-md-8">
            <label class="form-label">Pilih Kelas</label>
            <nav class="nav" style="display: flex; flex-wrap: wrap; gap: 5px;">

                <?php foreach ($kelas_list as $k): ?>
                    <?php
                    $is_active = ($k['kelas_id'] == $kelas_filter);
                    $url_params = [
                        'page' => 'absensi',
                        'kelas' => $k['kelas_id'],
                        'date' => $tanggal
                    ];
                    $url = 'guru.php?' . http_build_query($url_params);
                    $button_class = $is_active ? 'btn btn-primary' : 'btn btn-outline-primary';
                    ?>
                    <a class="btn <?= $button_class ?> btn-sm" href="<?= $url ?>">
                        <?= htmlspecialchars($k['nama']) ?>
                    </a>
                <?php endforeach ?>

            </nav>
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
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Alasan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($siswa_list as $i => $siswa):
                                $siswa_id = $siswa['siswa_id'];
                                $status = '-';
                                $alasan = '-';
                                $status_class = '';

                                if (isset($alasan_map[$siswa_id])) {
                                    $status = $alasan_map[$siswa_id]['status'];
                                    $alasan = $alasan_map[$siswa_id]['alasan'];

                                    if ($status == 'Sakit')
                                        $status_class = 'bg-warning text-dark';
                                    elseif ($status == 'Izin')
                                        $status_class = 'bg-info text-dark';
                                    elseif ($status == 'Alpha')
                                        $status_class = 'bg-danger text-white';

                                } elseif (isset($hadir_map[$siswa_id])) {
                                    $status = $hadir_map[$siswa_id]['status'];
                                    $alasan = '-';
                                    $status_class = 'bg-success text-white';
                                }
                                ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($siswa['nama']) ?></td>
                                    <td>
                                        <?php if ($status != '-'): ?>
                                            <span class="badge <?= $status_class ?> p-2">
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        <?php else: ?>
                                            <?= $status ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($alasan) ?></td>
                                    <td>
                                        <?php if ($status === 'Hadir'): ?>
                                            -
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-secondary edit-alasan-btn"
                                                data-bs-toggle="modal" data-bs-target="#editAlasanModal"
                                                data-siswa-id="<?= $siswa_id ?>"
                                                data-nama="<?= htmlspecialchars($siswa['nama'], ENT_QUOTES) ?>"
                                                data-status="<?= htmlspecialchars($status, ENT_QUOTES) ?>"
                                                data-alasan="<?= htmlspecialchars($alasan, ENT_QUOTES) ?>">
                                                Edit
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php elseif ($kelas_filter): ?>
        <div class="alert alert-warning mt-3">Tidak ada siswa di kelas ini.</div>
    <?php elseif (!$kelas_list): ?>
        <div class="alert alert-info mt-3">Anda saat ini tidak terdaftar mengajar di kelas manapun.</div>
    <?php endif ?>

    <!-- Edit Alasan Modal -->
    <div class="modal fade" id="editAlasanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" class="modal-content" id="editAlasanForm">
                <input type="hidden" name="action" value="save_alasan">
                <input type="hidden" name="siswa_id" id="modal_siswa_id" value="">
                <input type="hidden" name="kelas_id" value="<?= htmlspecialchars($kelas_filter) ?>">
                <input type="hidden" name="tanggal" id="modal_tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Alasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong id="modal_nama"></strong></div>
                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" id="modal_status" class="form-select">
                            <option value="-">-</option>
                            <option value="Sakit">Sakit</option>
                            <option value="Izin">Izin</option>
                            <option value="Alpha">Alpha</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Alasan</label>
                        <textarea name="alasan" id="modal_alasan" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('editAlasanModal');
            if (!modalEl) return;
            modalEl.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var siswaId = button.getAttribute('data-siswa-id') || '';
                var nama = button.getAttribute('data-nama') || '';
                var status = button.getAttribute('data-status') || '-';
                var alasan = button.getAttribute('data-alasan') || '';
                document.getElementById('modal_siswa_id').value = siswaId;
                document.getElementById('modal_nama').textContent = nama;
                document.getElementById('modal_status').value = status;
                document.getElementById('modal_alasan').value = (alasan === '-') ? '' : alasan;
                document.getElementById('modal_tanggal').value = '<?= htmlspecialchars($tanggal) ?>';
            });
        });
    </script>

</div>