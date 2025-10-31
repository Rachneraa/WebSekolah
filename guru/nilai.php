<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

// Cek login guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

$guru_id = $_SESSION['user_id'];

// Proses hapus tugas (non-AJAX)
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
    }
}

// Proses simpan semua nilai (non-AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_semua'])) {
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $mapel_id = intval($_POST['mapel_id'] ?? 0);
    $tugas_list = $_POST['tugas'] ?? [];
    $uts_list = $_POST['uts'] ?? [];
    $uas_list = $_POST['uas'] ?? [];
    $siswa_ids = $_POST['siswa_id'] ?? [];

    // Simpan nilai tugas
    foreach ($siswa_ids as $siswa_id) {
        if (!empty($tugas_list[$siswa_id])) {
            foreach ($tugas_list[$siswa_id] as $tugas_ke => $nilai) {
                $nilai = ($nilai === '' ? null : intval($nilai));
                // Cek apakah sudah ada
                $stmt = $db->prepare("SELECT id FROM nilai_tugas WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=?");
                $stmt->bind_param("iiii", $siswa_id, $kelas_id, $mapel_id, $tugas_ke);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    // update
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
        // Simpan UTS/UAS
        $uts = isset($uts_list[$siswa_id]) && $uts_list[$siswa_id] !== '' ? intval($uts_list[$siswa_id]) : null;
        $uas = isset($uas_list[$siswa_id]) && $uas_list[$siswa_id] !== '' ? intval($uas_list[$siswa_id]) : null;

        // Ambil nama mapel
        $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id=?");
        $stmt_mapel->bind_param("i", $mapel_id);
        $stmt_mapel->execute();
        $stmt_mapel->bind_result($mapel_nama);
        $stmt_mapel->fetch();
        $stmt_mapel->close();

        // Cek apakah sudah ada
        $stmt = $db->prepare("SELECT id FROM nilai WHERE siswa_id=? AND kelas_id=? AND mapel=?");
        $stmt->bind_param("iis", $siswa_id, $kelas_id, $mapel_nama);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $stmt2 = $db->prepare("UPDATE nilai SET uts=?, uas=? WHERE siswa_id=? AND kelas_id=? AND mapel=?");
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

// Ambil kelas yang diajar guru
$kelas_list = [];
$stmt = $db->prepare("SELECT DISTINCT k.kelas_id, k.nama 
    FROM jadwal j JOIN kelas k ON j.kelas_id = k.kelas_id
    WHERE j.guru_id = ?");
$stmt->bind_param("i", $guru_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc())
    $kelas_list[] = $row;
$stmt->close();

// Filter kelas
$kelas_id = isset($_GET['kelas']) ? intval($_GET['kelas']) : 0;

// Ambil mapel_id yang diajar guru di kelas terpilih
$mapel_id = 0;
if ($kelas_id) {
    $stmt = $db->prepare("SELECT DISTINCT m.id AS mapel_id
        FROM jadwal j JOIN mapel m ON j.mapel_id = m.id
        WHERE j.guru_id = ? AND j.kelas_id = ?");
    $stmt->bind_param("ii", $guru_id, $kelas_id);
    $stmt->execute();
    $stmt->bind_result($mapel_id);
    $stmt->fetch();
    $stmt->close();
}

// Ambil siswa di kelas
$siswa_list = [];
if ($kelas_id) {
    $stmt = $db->prepare("SELECT siswa_id, nama FROM siswa WHERE kelas_id = ?");
    $stmt->bind_param("i", $kelas_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc())
        $siswa_list[] = $row;
    $stmt->close();
}

// Ambil jumlah tugas (tugas_ke terbesar) untuk kelas & mapel ini
$max_tugas = 0;
if ($kelas_id && $mapel_id) {
    $stmt = $db->prepare("SELECT MAX(tugas_ke) as max_tugas FROM nilai_tugas WHERE kelas_id=? AND mapel_id=?");
    $stmt->bind_param("ii", $kelas_id, $mapel_id);
    $stmt->execute();
    $stmt->bind_result($max_tugas);
    $stmt->fetch();
    $stmt->close();
}
if ($max_tugas < 1)
    $max_tugas = 1;

// buat daftar tugas (digunakan untuk rendering kolom)
$tugas_list = [];
for ($i = 1; $i <= $max_tugas; $i++) {
    $tugas_list[] = ['tugas_ke' => $i];
}

// Ambil nilai tugas per siswa
$nilai_tugas_map = []; // [siswa_id][tugas_ke] = nilai
if ($kelas_id && $mapel_id) {
    $stmt = $db->prepare("SELECT * FROM nilai_tugas WHERE kelas_id=? AND mapel_id=?");
    $stmt->bind_param("ii", $kelas_id, $mapel_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $nilai_tugas_map[$row['siswa_id']][$row['tugas_ke']] = $row['nilai'];
    }
    $stmt->close();
}

// Ambil nilai UTS, UAS, rata-rata per siswa
$nilai_map = []; // [siswa_id] = ['uts'=>..,'uas'=>..,'rata_rata'=>..]
if ($kelas_id && $mapel_id) {
    // Ambil nama mapel dari tabel mapel
    $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id=?");
    $stmt_mapel->bind_param("i", $mapel_id);
    $stmt_mapel->execute();
    $stmt_mapel->bind_result($mapel_nama);
    $stmt_mapel->fetch();
    $stmt_mapel->close();

    // Ambil nilai (sesuaikan kolom sesuai struktur tabel 'nilai')
    // Jika kolom 'mapel' di tabel nilai menyimpan nama, gunakan mapel_nama
    $stmt = $db->prepare("SELECT siswa_id, uts, uas, rata_rata FROM nilai WHERE kelas_id=? AND mapel=?");
    $stmt->bind_param("is", $kelas_id, $mapel_nama);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $s_id = $row['siswa_id'];
        $nilai_map[$s_id] = [
            'uts' => $row['uts'],
            'uas' => $row['uas'],
            'rata_rata' => $row['rata_rata']
        ];
    }
    $stmt->close();
}

// setelah ambil siswa_list, sebelum render form/tabel — ambil daftar mapel untuk kelas terpilih
if ($kelas_id) {
    $mapel_list = [];
    $stmt = $db->prepare("SELECT DISTINCT m.id, m.nama 
                          FROM jadwal j 
                          JOIN mapel m ON j.mapel_id = m.id
                          WHERE j.kelas_id = ? AND j.guru_id = ?");
    $stmt->bind_param("ii", $kelas_id, $guru_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $mapel_list[] = $r;
    $stmt->close();
}

// helper: hitung rata-rata baris (tugas + uts + uas jika ada)
function calculate_row_avg($siswa_id, $nilai_tugas_map, $nilai_map) {
    $values = [];
    if (!empty($nilai_tugas_map[$siswa_id]) && is_array($nilai_tugas_map[$siswa_id])) {
        foreach ($nilai_tugas_map[$siswa_id] as $v) {
            if (is_numeric($v)) $values[] = (float)$v;
        }
    }
    if (!empty($nilai_map[$siswa_id])) {
        if (isset($nilai_map[$siswa_id]['uts']) && is_numeric($nilai_map[$siswa_id]['uts'])) $values[] = (float)$nilai_map[$siswa_id]['uts'];
        if (isset($nilai_map[$siswa_id]['uas']) && is_numeric($nilai_map[$siswa_id]['uas'])) $values[] = (float)$nilai_map[$siswa_id]['uas'];
    }
    if (count($values) === 0) return '—';
    return number_format(array_sum($values) / count($values), 2);
}

// Pastikan variabel ada untuk template (hindari undefined)
if (!isset($nilai_tugas_map)) $nilai_tugas_map = [];
if (!isset($nilai_map)) $nilai_map = [];
if (!isset($tugas_list)) $tugas_list = [['tugas_ke' => 1]];
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .nilai-table th,
    .nilai-table td { text-align: center; vertical-align: middle; }
    .nilai-table input[type=number] { width: 60px; text-align: center; }
</style>

<div class="container py-4">
    <h4>Input Nilai Tugas Siswa</h4>
    <form method="get" action="guru.php" class="row g-3 mb-3">
        <input type="hidden" name="page" value="nilai">
        <div class="col-md-4">
            <label for="kelas" class="form-label">Pilih Kelas</label>
            <select name="kelas" id="kelas" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['kelas_id'] ?>" <?= $kelas_id == $k['kelas_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="col-md-4">
            <label for="mapel" class="form-label">Pilih Mata Pelajaran</label>
            <select name="mapel" id="mapel" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih Mata Pelajaran --</option>
                <?php if (!empty($mapel_list)): foreach ($mapel_list as $m): ?>
                    <option value="<?= (int)$m['id'] ?>" <?= ($mapel_id == $m['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['nama']) ?>
                    </option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button id="addTugasBtn" class="btn btn-success me-2" type="button"><i class="fas fa-plus"></i> Tambah Tugas</button>
            <!-- Tombol Simpan Semua akan di bawah tabel -->
        </div>
    </form>

    <?php if ($kelas_id && $mapel_id && $siswa_list): ?>
        <?php if (!empty($pesan_sukses)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($pesan_sukses) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="hidden" name="kelas_id" value="<?= $kelas_id ?>">
            <input type="hidden" name="mapel_id" value="<?= $mapel_id ?>">
            <div class="table-responsive">
                <table class="table table-bordered nilai-table" id="nilaiTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <?php foreach ($tugas_list as $t): ?>
                                <th>
                                    Tugas <?= $t['tugas_ke'] ?>
                                    <?php if (count($tugas_list) > 1): ?>
                                        <button type="submit"
                                            name="hapus_tugas"
                                            value="<?= $t['tugas_ke'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Hapus tugas ke-<?= $t['tugas_ke'] ?>?')">
                                            Hapus Tugas
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
                        $siswa_id = $s['siswa_id'];
                    ?>
                        <tr>
                            <td><?= ($i+1) ?></td>
                            <td class="text-start"><?= htmlspecialchars($s['nama']) ?></td>
                            <input type="hidden" name="siswa_id[]" value="<?= $siswa_id ?>">
                            <?php foreach ($tugas_list as $t):
                                $tke = $t['tugas_ke'];
                                $val = $nilai_tugas_map[$siswa_id][$tke] ?? '';
                            ?>
                                <td>
                                    <input type="number" min="0" max="100" class="form-control"
                                           name="tugas[<?= $siswa_id ?>][<?= $tke ?>]"
                                           value="<?= htmlspecialchars($val) ?>">
                                </td>
                            <?php endforeach; ?>
                            <?php
                                $uts_val = $nilai_map[$siswa_id]['uts'] ?? '';
                                $uas_val = $nilai_map[$siswa_id]['uas'] ?? '';
                            ?>
                            <td>
                                <input type="number" min="0" max="100" class="form-control"
                                       name="uts[<?= $siswa_id ?>]" value="<?= htmlspecialchars($uts_val) ?>">
                            </td>
                            <td>
                                <input type="number" min="0" max="100" class="form-control"
                                       name="uas[<?= $siswa_id ?>]" value="<?= htmlspecialchars($uas_val) ?>">
                            </td>
                            <td>
                                <?= is_numeric($uts_val) || is_numeric($uas_val) || !empty($nilai_tugas_map[$siswa_id]) ? calculate_row_avg($siswa_id,$nilai_tugas_map,$nilai_map) : '—' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="simpan_semua" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua Nilai</button>
            </div>
        </form>
    <?php elseif ($kelas_id && !$siswa_list): ?>
        <div class="alert alert-warning mt-3">Tidak ada siswa di kelas ini.</div>
    <?php endif ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // hitung rata-rata per baris (mengumpulkan tugas + uts + uas)
    function hitungRataRata($row) {
        let total = 0, count = 0;
        $row.find('.tugas-input').each(function () {
            let v = $(this).val();
            if (v !== '' && !isNaN(v)) { total += parseFloat(v); count++; }
        });
        $row.find('.uts-input, .uas-input').each(function () {
            let v = $(this).val();
            if (v !== '' && !isNaN(v)) { total += parseFloat(v); count++; }
        });
        let rata = count ? (total / count).toFixed(2) : '—';
        $row.find('.rata-rata-cell').text(rata);
    }

    $(function () {
        // tombol tambah tugas
        $('#addTugasBtn').on('click', function (e) {
            e.preventDefault();
            let kelas = $('#kelas').val();
            let mapel = $('#mapel').val();
            if (!kelas || !mapel) { alert('Pilih kelas dan mata pelajaran terlebih dahulu'); return; }
            $.post('ajax_tambah_tugas.php', { kelas: kelas, mapel: mapel }, function (res) {
                if (res && res.success) location.reload();
                else alert('Gagal menambah tugas!');
            }, 'json').fail(function(){ alert('Request gagal'); });
        });

        // hapus tugas (delegated)
        $(document).on('click', '.hapus-tugas-btn', function (e) {
            e.preventDefault();
            if (!confirm('Hapus kolom tugas ini?')) return;
            let tugas = $(this).data('tugas');
            let kelas = $('#kelas').val();
            let mapel = $('#mapel').val();
            $.post('ajax_hapus_tugas.php', { kelas: kelas, mapel: mapel, tugas: tugas }, function (res) {
                if (res && res.success) location.reload();
                else alert('Gagal menghapus tugas!');
            }, 'json').fail(function(){ alert('Request gagal'); });
        });

        // ketika salah satu input berubah — simpan individual via AJAX dan update rata-rata baris
        $(document).on('change', '.tugas-input, .uts-input, .uas-input', function () {
            let $input = $(this);
            let nilai = $input.val();
            let siswa = $input.data('siswa');
            let tugas = $input.data('tugas') || 0; // tugas ke untuk tugas-input, undefined untuk uts/uas -> 0
            let kelas = $('#kelas').val();
            let mapel = $('#mapel').val();

            if (nilai !== '' && (isNaN(nilai) || nilai < 0 || nilai > 100)) {
                alert('Nilai harus angka 0-100');
                $input.val('');
                return;
            }

            $.post('ajax_simpan_nilai.php', {
                siswa: siswa, tugas: tugas, nilai: nilai, kelas: kelas, mapel: mapel,
                field: $input.hasClass('uts-input') ? 'uts' : ($input.hasClass('uas-input') ? 'uas' : 'tugas')
            }, function (res) {
                if (!res || !res.success) {
                    alert('Gagal menyimpan nilai!');
                }
            }, 'json').fail(function(){ alert('Request gagal'); });

            // update rata-rata baris
            let $row = $input.closest('tr');
            hitungRataRata($row);
        });

        // inisialisasi rata-rata di load
        $('#nilaiTable tbody tr').each(function(){ hitungRataRata($(this)); });

        // Simpan semua (global) — kumpulkan dan kirim paralel
        $('#saveAllBtn').on('click', function (e) {
            e.preventDefault();
            const btn = $(this);
            const status = $('#saveStatus');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            status.text('');

            const kelas = $('#kelas').val();
            const mapel = $('#mapel').val();
            if (!kelas || !mapel) { status.text('Pilih kelas dan mata pelajaran terlebih dahulu.'); btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Semua Nilai'); return; }

            const requests = [];
            $('#nilaiTable tbody tr').each(function () {
                const $row = $(this);
                const siswa = $row.data('siswa');

                // tugas
                $row.find('.tugas-input').each(function () {
                    const tugas = $(this).data('tugas');
                    const nilai = $(this).val();
                    const f = new FormData();
                    f.append('siswa', siswa);
                    f.append('kelas', kelas);
                    f.append('mapel', mapel);
                    f.append('tugas', tugas);
                    f.append('nilai', nilai);
                    f.append('field', 'tugas');
                    requests.push(fetch('ajax_simpan_nilai.php', { method: 'POST', body: f }).then(r=>r.json()));
                });

                // uts
                let uts = $row.find('.uts-input').val();
                let f2 = new FormData();
                f2.append('siswa', siswa); f2.append('kelas', kelas); f2.append('mapel', mapel);
                f2.append('tugas', 0); f2.append('nilai', uts); f2.append('field', 'uts');
                requests.push(fetch('ajax_simpan_nilai.php', { method: 'POST', body: f2 }).then(r=>r.json()));

                // uas
                let uas = $row.find('.uas-input').val();
                let f3 = new FormData();
                f3.append('siswa', siswa); f3.append('kelas', kelas); f3.append('mapel', mapel);
                f3.append('tugas', 0); f3.append('nilai', uas); f3.append('field', 'uas');
                requests.push(fetch('ajax_simpan_nilai.php', { method: 'POST', body: f3 }).then(r=>r.json()));
            });

            Promise.all(requests).then(results=>{
                let ok = results.every(r => r && r.success);
                if (ok) {
                    status.html('<span class="text-success">Semua nilai berhasil disimpan.</span>');
                    setTimeout(()=> location.reload(), 700);
                } else {
                    status.html('<span class="text-danger">Beberapa nilai gagal disimpan.</span>');
                }
            }).catch(err=>{
                console.error(err);
                status.html('<span class="text-danger">Terjadi kesalahan jaringan.</span>');
            }).finally(()=> {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Semua Nilai');
            });
        });
    });
</script>

<?php
// filepath: c:\laragon\www\smk-ti-main\smk-ti-main\guru\nilai.php
// Proses hapus tugas (non-AJAX)
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
    }
}

// Proses simpan semua nilai (non-AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_semua'])) {
    $kelas_id = intval($_POST['kelas_id'] ?? 0);
    $mapel_id = intval($_POST['mapel_id'] ?? 0);
    $tugas_list = $_POST['tugas'] ?? [];
    $uts_list = $_POST['uts'] ?? [];
    $uas_list = $_POST['uas'] ?? [];
    $siswa_ids = $_POST['siswa_id'] ?? [];

    // Simpan nilai tugas
    foreach ($siswa_ids as $siswa_id) {
        if (!empty($tugas_list[$siswa_id])) {
            foreach ($tugas_list[$siswa_id] as $tugas_ke => $nilai) {
                $nilai = ($nilai === '' ? null : intval($nilai));
                // Cek apakah sudah ada
                $stmt = $db->prepare("SELECT id FROM nilai_tugas WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=?");
                $stmt->bind_param("iiii", $siswa_id, $kelas_id, $mapel_id, $tugas_ke);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    // update
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
        // Simpan UTS/UAS
        $uts = isset($uts_list[$siswa_id]) && $uts_list[$siswa_id] !== '' ? intval($uts_list[$siswa_id]) : null;
        $uas = isset($uas_list[$siswa_id]) && $uas_list[$siswa_id] !== '' ? intval($uas_list[$siswa_id]) : null;

        // Ambil nama mapel
        $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id=?");
        $stmt_mapel->bind_param("i", $mapel_id);
        $stmt_mapel->execute();
        $stmt_mapel->bind_result($mapel_nama);
        $stmt_mapel->fetch();
        $stmt_mapel->close();

        // Cek apakah sudah ada
        $stmt = $db->prepare("SELECT id FROM nilai WHERE siswa_id=? AND kelas_id=? AND mapel=?");
        $stmt->bind_param("iis", $siswa_id, $kelas_id, $mapel_nama);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            $stmt2 = $db->prepare("UPDATE nilai SET uts=?, uas=? WHERE siswa_id=? AND kelas_id=? AND mapel=?");
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