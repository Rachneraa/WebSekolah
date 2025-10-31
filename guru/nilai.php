<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

// Cek login guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

$guru_id = $_SESSION['guru_id'];

// --- (Proses Hapus & Simpan Nilai tidak ada perubahan) ---
// Proses hapus tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_tugas'])) {
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $mapel_id = intval($_POST['mapel_id'] ?? 0);
    $tugas_ke = intval($_POST['tugas_ke'] ?? 0);
    if ($kelas_id && $mapel_id && $tugas_ke) {
        $stmt = $db->prepare("DELETE FROM nilai_tugas WHERE kelas_id=? AND mapel_id=? AND tugas_ke=?");
        $stmt->bind_param("iii", $kelas_id, $mapel_id, $tugas_ke);
        $stmt->execute();
        $stmt->close();
        $pesan_sukses = "Tugas ke-$tugas_ke berhasil dihapus.";
        // Refresh halaman dengan parameter yang sama
        header("Location: guru.php?page=nilai&kelas=" . $kelas_id . "&mapel=" . $mapel_id);
        exit();
    }
}

// Proses simpan semua nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_semua'])) {
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $mapel_id = intval($_POST['mapel_id'] ?? 0);
    $tugas_list = $_POST['tugas'] ?? [];
    $uts_list = $_POST['uts'] ?? [];
    $uas_list = $_POST['uas'] ?? [];
    $siswa_ids = $_POST['siswa_id'] ?? [];

    foreach ($siswa_ids as $siswa_id) {
        // Simpan nilai tugas
        if (!empty($tugas_list[$siswa_id])) {
            foreach ($tugas_list[$siswa_id] as $tugas_ke => $nilai) {
                $nilai = ($nilai === '' ? null : intval($nilai));
                $stmt = $db->prepare("SELECT id FROM nilai_tugas WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=?");
                $stmt->bind_param("iiii", $siswa_id, $kelas_id, $mapel_id, $tugas_ke);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $stmt->close();
                    $stmt2 = $db->prepare("UPDATE nilai_tugas SET nilai=? WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=?");
                    $stmt2->bind_param("iiiii", $nilai, $siswa_id, $kelas_id, $mapel_id, $tugas_ke);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $stmt->close();
                    $stmt2 = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai) VALUES (?, ?, ?, ?, ?)");
                    $stmt2->bind_param("iiiii", $siswa_id, $kelas_id, $mapel_id, $tugas_ke, $nilai);
                    $stmt2->execute();
                    $stmt2->close();
                }
            }
        }

        // Simpan UTS & UAS
        $uts = (!empty($uts_list[$siswa_id]) && $uts_list[$siswa_id] !== '') ? intval($uts_list[$siswa_id]) : null;
        $uas = (!empty($uas_list[$siswa_id]) && $uas_list[$siswa_id] !== '') ? intval($uas_list[$siswa_id]) : null;

        // Ambil nama mapel
        $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id = ?");
        $stmt_mapel->bind_param("i", $mapel_id);
        $stmt_mapel->execute();
        $stmt_mapel->bind_result($mapel_nama);
        $stmt_mapel->fetch();
        $stmt_mapel->close();

        // Cek & simpan ke tabel nilai
        $stmt = $db->prepare("SELECT id FROM nilai WHERE siswa_id = ? AND kelas_id = ? AND mapel = ?");
        $stmt->bind_param("iis", $siswa_id, $kelas_id, $mapel_nama);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $stmt2 = $db->prepare("UPDATE nilai SET uts = ?, uas = ? WHERE siswa_id = ? AND kelas_id = ? AND mapel = ?");
            $stmt2->bind_param("iiiis", $uts, $uas, $siswa_id, $kelas_id, $mapel_nama);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt->close();
            $stmt2 = $db->prepare("INSERT INTO nilai (siswa_id, kelas_id, mapel, uts, uas) VALUES (?, ?, ?, ?, ?)");
            $stmt2->bind_param("iisii", $siswa_id, $kelas_id, $mapel_nama, $uts, $uas);
            $stmt2->execute();
            $stmt2->close();
        }
    }
    $pesan_sukses = "Semua nilai berhasil disimpan.";
}
// --- (Akhir Proses Simpan) ---


// ===================================================================
// == 1. AWAL PERBAIKAN LOGIKA FILTER OTOMATIS
// ===================================================================

// 🔑 AMBIL KELAS YANG DIAJAR GURU
$kelas_list = [];
$stmt = $db->prepare("
    SELECT DISTINCT k.kelas_id, k.nama 
    FROM jadwal j 
    JOIN kelas k ON j.kelas_id = k.kelas_id
    WHERE j.guru_id = ?
");
if ($stmt) {
    $stmt->bind_param("i", $guru_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $kelas_list[] = $row;
    }
    $stmt->close();
}

// 1. Tentukan $kelas_id (dengan auto-select)
$kelas_id = 0;
if (isset($_GET['kelas'])) {
    $kelas_id = intval($_GET['kelas']);
} elseif (!empty($kelas_list)) {
    $kelas_id = $kelas_list[0]['kelas_id']; // Otomatis pilih kelas pertama
}

// 2. Ambil $mapel_list (BERDASARKAN $kelas_id yang sudah pasti)
$mapel_list = [];
if ($kelas_id) {
    $stmt = $db->prepare("
        SELECT DISTINCT m.id, m.nama 
        FROM jadwal j 
        JOIN mapel m ON j.mapel_id = m.id
        WHERE j.kelas_id = ? AND j.guru_id = ?
    ");
    $stmt->bind_param("ii", $kelas_id, $guru_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $mapel_list[] = $r;
    }
    $stmt->close();
}

// 3. Tentukan $mapel_id (dengan auto-select)
$mapel_id = 0;
if (isset($_GET['mapel'])) {
    $mapel_id = intval($_GET['mapel']);
} elseif (!empty($mapel_list)) {
    $mapel_id = $mapel_list[0]['id']; // Otomatis pilih mapel pertama
}

// 4. Ambil $siswa_list (BERDASARKAN $kelas_id)
$siswa_list = [];
if ($kelas_id) {
    $stmt = $db->prepare("SELECT siswa_id, nama FROM siswa WHERE kelas_id = ?");
    $stmt->bind_param("i", $kelas_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $siswa_list[] = $row;
    }
    $stmt->close();
}
// ===================================================================
// == AKHIR PERBAIKAN LOGIKA FILTER
// ===================================================================


// --- (Sisa Logika PHP tidak berubah) ---

// Hitung jumlah tugas
$max_tugas = 1;
if ($kelas_id && $mapel_id) {
    $stmt = $db->prepare("SELECT MAX(tugas_ke) AS max_tugas FROM nilai_tugas WHERE kelas_id = ? AND mapel_id = ?");
    $stmt->bind_param("ii", $kelas_id, $mapel_id);
    $stmt->execute();
    $stmt->bind_result($max_tugas);
    $stmt->fetch();
    $stmt->close();
    $max_tugas = max(1, (int)$max_tugas);
}

// Buat daftar tugas
$tugas_list = [];
for ($i = 1; $i <= $max_tugas; $i++) {
    $tugas_list[] = ['tugas_ke' => $i];
}

// Ambil nilai tugas
$nilai_tugas_map = [];
if ($kelas_id && $mapel_id) {
    $stmt = $db->prepare("SELECT siswa_id, tugas_ke, nilai FROM nilai_tugas WHERE kelas_id = ? AND mapel_id = ?");
    $stmt->bind_param("ii", $kelas_id, $mapel_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $nilai_tugas_map[$row['siswa_id']][$row['tugas_ke']] = $row['nilai'];
    }
    $stmt->close();
}

// Ambil nilai UTS/UAS
$nilai_map = [];
if ($kelas_id && $mapel_id) {
    $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id = ?");
    $stmt_mapel->bind_param("i", $mapel_id);
    $stmt_mapel->execute();
    $stmt_mapel->bind_result($mapel_nama);
    $stmt_mapel->fetch();
    $stmt_mapel->close();

    $stmt = $db->prepare("SELECT siswa_id, uts, uas, rata_rata FROM nilai WHERE kelas_id = ? AND mapel = ?");
    $stmt->bind_param("is", $kelas_id, $mapel_nama);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $nilai_map[$row['siswa_id']] = [
            'uts' => $row['uts'],
            'uas' => $row['uas'],
            'rata_rata' => $row['rata_rata']
        ];
    }
    $stmt->close();
}

// Helper hitung rata-rata
function calculate_row_avg($siswa_id, $nilai_tugas_map, $nilai_map) {
    $values = [];
    if (!empty($nilai_tugas_map[$siswa_id])) {
        foreach ($nilai_tugas_map[$siswa_id] as $v) {
            if (is_numeric($v)) $values[] = (float)$v;
        }
    }
    if (!empty($nilai_map[$siswa_id])) {
        if (is_numeric($nilai_map[$siswa_id]['uts'])) $values[] = (float)$nilai_map[$siswa_id]['uts'];
        if (is_numeric($nilai_map[$siswa_id]['uas'])) $values[] = (float)$nilai_map[$siswa_id]['uas'];
    }
    return count($values) ? number_format(array_sum($values) / count($values), 2) : '—';
}

// Pastikan variabel tersedia
if (!isset($nilai_tugas_map)) $nilai_tugas_map = [];
if (!isset($nilai_map)) $nilai_map = [];
if (!isset($tugas_list)) $tugas_list = [['tugas_ke' => 1]];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .nilai-table th, .nilai-table td { text-align: center; vertical-align: middle; }
    .nilai-table input[type=number] { width: 60px; text-align: center; }
    .btn-hapus-tugas { font-size: 0.7rem; padding: 2px 5px; margin-top: 3px; }
</style>

<div class="container py-4">
    <h4>Input Nilai Tugas Siswa</h4>
    
    <div class="row g-3 mb-3">
        <div class="col-md-12">
            <label class="form-label">Pilih Kelas</label>
            <nav class="nav" style="display: flex; flex-wrap: wrap; gap: 5px;">
                <?php foreach ($kelas_list as $k): ?>
                    <?php
                        $is_active = ($k['kelas_id'] == $kelas_id);
                        // Link untuk kelas, HANYA berisi parameter kelas
                        // Ini agar saat ganti kelas, filter mapel ter-reset
                        $url = 'guru.php?page=nilai&kelas=' . $k['kelas_id'];
                        $btn_class = $is_active ? 'btn btn-primary' : 'btn btn-outline-primary';
                    ?>
                    <a class="btn <?= $btn_class ?> btn-sm" href="<?= $url ?>">
                        <?= htmlspecialchars($k['nama']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <?php if ($kelas_id && !empty($mapel_list)): ?>
            <div class="col-md-8">
                <label class="form-label">Pilih Mata Pelajaran</label>
                <nav class="nav" style="display: flex; flex-wrap: wrap; gap: 5px;">
                    <?php foreach ($mapel_list as $m): ?>
                        <?php
                            $is_active = ($m['id'] == $mapel_id);
                            // Link untuk mapel, HARUS membawa parameter kelas & mapel
                            $url = 'guru.php?page=nilai&kelas=' . $kelas_id . '&mapel=' . $m['id'];
                            $btn_class = $is_active ? 'btn btn-secondary' : 'btn btn-outline-secondary';
                        ?>
                        <a class="btn <?= $btn_class ?> btn-sm" href="<?= $url ?>">
                            <?= htmlspecialchars($m['nama']) ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        <?php endif; ?>

        <div class="col-md-4 d-flex align-items-end justify-content-end">
            <?php if ($kelas_id && $mapel_id): ?>
                <button id="addTugasBtn" class="btn btn-success" type="button">
                    <i class="fas fa-plus"></i> Tambah Tugas
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($pesan_sukses)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($pesan_sukses) ?></div>
    <?php endif; ?>

    <?php if ($kelas_id && $mapel_id && !empty($siswa_list)): ?>
        <form method="post" action="" id="formHapusTugas" style="display:none;">
            <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
            <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
            <input type="hidden" name="tugas_ke" id="hapusTugasKe">
            <input type="hidden" name="hapus_tugas" value="1">
        </form>

        <form method="post" action="">
            <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
            <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
            <div class="table-responsive">
                <table class="table table-bordered nilai-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <?php foreach ($tugas_list as $t): ?>
                                <th>
                                    Tugas <?= $t['tugas_ke'] ?>
                                    <?php if (count($tugas_list) > 1): ?>
                                        <br>
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus-tugas"
                                            onclick="hapusTugas(<?= $t['tugas_ke'] ?>)">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    <?php endif; ?>
                                </th>
                            <?php endforeach; ?>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa_list as $i => $s): 
                            $sid = $s['siswa_id'];
                            $uts_val = $nilai_map[$sid]['uts'] ?? '';
                            $uas_val = $nilai_map[$sid]['uas'] ?? '';
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="text-start"><?= htmlspecialchars($s['nama']) ?></td>
                            <input type="hidden" name="siswa_id[]" value="<?= $sid ?>">
                            <?php foreach ($tugas_list as $t):
                                $tk = $t['tugas_ke'];
                                $tval = $nilai_tugas_map[$sid][$tk] ?? '';
                            ?>
                                <td>
                                    <input type="number" min="0" max="100" class="form-control"
                                           name="tugas[<?= $sid ?>][<?= $tk ?>]"
                                           value="<?= htmlspecialchars($tval) ?>">
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <input type="number" min="0" max="100" class="form-control"
                                       name="uts[<?= $sid ?>]" value="<?= htmlspecialchars($uts_val) ?>">
                            </td>
                            <td>
                                <input type="number" min="0" max="100" class="form-control"
                                       name="uas[<?= $sid ?>]" value="<?= htmlspecialchars($uas_val) ?>">
                            </td>
                            <td>
                                <?= calculate_row_avg($sid, $nilai_tugas_map, $nilai_map) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="simpan_semua" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Simpan Semua Nilai
                </button>
            </div>
        </form>

    <?php elseif ($kelas_id && $mapel_id && empty($siswa_list)): ?>
        <div class="alert alert-warning">Tidak ada siswa di kelas ini.</div>
    <?php elseif ($kelas_id && !$mapel_id && !empty($mapel_list)): ?>
        <div class="alert alert-info">Pilih mata pelajaran untuk melihat nilai.</div>
    <?php elseif ($kelas_id && empty($mapel_list)): ?>
        <div class="alert alert-warning">Tidak ada mata pelajaran yang Anda ajar di kelas ini.</div>
    <?php else: ?>
        <div class="alert alert-info">Pilih kelas terlebih dahulu.</div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// --- (Javascript tidak ada perubahan) ---
function hapusTugas(tugasKe) {
    if (!confirm('Anda yakin ingin menghapus Tugas ' + tugasKe + '? Semua nilai di kolom ini akan hilang.')) return;
    document.getElementById('hapusTugasKe').value = tugasKe;
    document.getElementById('formHapusTugas').submit();
}

$(document).ready(function() {
    $('#addTugasBtn').on('click', function() {
        var table = $('.nilai-table');
        var tugasKeBaru = table.find('thead th:contains("Tugas")').length + 1;

        // === PERBAIKAN: Buat HTML header yang lengkap ===
        // Ini akan menambahkan tombol hapus yang berfungsi & memiliki styling
        var thBaruHTML = `
            <th>
                Tugas ${tugasKeBaru}
                <br>
                <button type="button" class="btn btn-danger btn-sm btn-hapus-tugas"
                        onclick="hapusTugas(${tugasKeBaru})">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </th>
        `;
        table.find('thead th:contains("UTS")').before(thBaruHTML);
        // === BATAS AKHIR PERBAIKAN HEADER ===

        // 4. Tambahkan sel input baru di setiap baris siswa
        table.find('tbody tr').each(function() {
            var row = $(this);
            // Ambil siswa_id dari input tersembunyi di baris ini
            var siswaId = row.find('input[name^="siswa_id"]').val();
            
            if (siswaId) {
                // Buat <td> baru
                var tdBaru = `
                    <td>
                        <input type="number" min="0" max="100" class="form-control"
                               name="tugas[${siswaId}][${tugasKeBaru}]"
                               value="">
                    </td>
                `;
                // Tambahkan sebelum sel UTS
                row.find('td:has(input[name^="uts"])').before(tdBaru);
            }
        });
    });
});
</script>