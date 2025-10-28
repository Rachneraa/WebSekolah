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

// Ambil nilai tugas per siswa
$nilai_map = [];
if ($kelas_id && $mapel_id) {
    $stmt = $db->prepare("SELECT * FROM nilai_tugas WHERE kelas_id=? AND mapel_id=?");
    $stmt->bind_param("ii", $kelas_id, $mapel_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $nilai_map[$row['siswa_id']][$row['tugas_ke']] = $row['nilai'];
    }
    $stmt->close();
}

// Ambil nilai UTS, UAS, rata-rata per siswa
$nilai_uts = $nilai_uas = $nilai_rata = [];
if ($kelas_id && $mapel_id) {
    $stmt = $db->prepare("SELECT * FROM nilai WHERE kelas_id=? AND mapel=?");
    $mapel_nama = ''; // default
    // Ambil nama mapel dari tabel mapel
    $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id=?");
    $stmt_mapel->bind_param("i", $mapel_id);
    $stmt_mapel->execute();
    $stmt_mapel->bind_result($mapel_nama);
    $stmt_mapel->fetch();
    $stmt_mapel->close();

    $stmt->bind_param("is", $kelas_id, $mapel_nama);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $nilai_uts[$row['siswa_id']] = $row['uts'];
        $nilai_uas[$row['siswa_id']] = $row['uas'];
        $nilai_rata[$row['siswa_id']] = $row['rata_rata'];
    }
    $stmt->close();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .nilai-table th,
    .nilai-table td {
        text-align: center;
        vertical-align: middle;
    }

    .nilai-table input[type=number] {
        width: 60px;
        text-align: center;
    }

    .add-tugas-btn {
        margin-left: 10px;
    }
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
    </form>

    <?php if ($kelas_id && $mapel_id && $siswa_list): ?>
        <div class="table-responsive">
            <table class="table table-bordered nilai-table align-middle">
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Nama Siswa</th>
                        <th colspan="<?= $max_tugas ?>">
                            Nilai Tugas
                            <button class="btn btn-sm btn-success add-tugas-btn" id="addTugasBtn" title="Tambah Tugas">
                                <i class="fas fa-plus"></i>
                            </button>
                        </th>
                        <th rowspan="2">UTS</th>
                        <th rowspan="2">UAS</th>
                        <th rowspan="2">Rata-rata</th>
                    </tr>
                    <tr>
                        <?php for ($t = 1; $t <= $max_tugas; $t++): ?>
                            <th>
                                Tugas <?= $t ?>
                                <button class="btn btn-sm btn-danger ms-1 hapus-tugas-btn" data-tugas="<?= $t ?>"
                                    title="Hapus Tugas">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </th>
                        <?php endfor ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswa_list as $i => $siswa): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($siswa['nama']) ?></td>
                            <?php
                            $total = 0;
                            $isi = 0;
                            for ($t = 1; $t <= $max_tugas; $t++):
                                $nilai = isset($nilai_map[$siswa['siswa_id']][$t]) ? $nilai_map[$siswa['siswa_id']][$t] : '';
                                if ($nilai !== '' && $nilai !== null) {
                                    $total += $nilai;
                                    $isi++;
                                }
                                ?>
                                <td>
                                    <input type="number" min="0" max="100" value="<?= $nilai ?>"
                                        data-siswa="<?= $siswa['siswa_id'] ?>" data-tugas="<?= $t ?>"
                                        class="form-control form-control-sm nilai-input" style="width:70px;display:inline-block;">
                                </td>
                            <?php endfor ?>
                            <td>
                                <input type="number" min="0" max="100" value="<?= $nilai_uts[$siswa['siswa_id']] ?? '' ?>"
                                    data-siswa="<?= $siswa['siswa_id'] ?>" data-tipe="uts"
                                    class="form-control form-control-sm nilai-uts" style="width:70px;display:inline-block;">
                            </td>
                            <td>
                                <input type="number" min="0" max="100" value="<?= $nilai_uas[$siswa['siswa_id']] ?? '' ?>"
                                    data-siswa="<?= $siswa['siswa_id'] ?>" data-tipe="uas"
                                    class="form-control form-control-sm nilai-uas" style="width:70px;display:inline-block;">
                            </td>
                            <td>
                                <input type="number" readonly value="<?= $isi ? round($total / $isi, 2) : '' ?>"
                                    class="form-control form-control-sm rata-rata" data-siswa="<?= $siswa['siswa_id'] ?>"
                                    style="width:70px;display:inline-block;">
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($kelas_id && !$siswa_list): ?>
        <div class="alert alert-warning mt-3">Tidak ada siswa di kelas ini.</div>
    <?php endif ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function hitungRataRata($row) {
        let total = 0, isi = 0;
        $row.find('.nilai-input').each(function () {
            let v = $(this).val();
            if (v !== '' && !isNaN(v)) { total += parseFloat(v); isi++; }
        });
        // Jika ingin rata-rata termasuk UTS dan UAS, tambahkan baris berikut:
        // $row.find('.nilai-uts, .nilai-uas').each(function () {
        //     let v = $(this).val();
        //     if (v !== '' && !isNaN(v)) { total += parseFloat(v); isi++; }
        // });
        let rata = isi ? (total / isi).toFixed(2) : '';
        $row.find('.rata-rata').val(rata);
    }

    $(function () {
        // Tambah kolom tugas (AJAX)
        $('#addTugasBtn').on('click', function (e) {
            e.preventDefault();
            let kelas = $('#kelas').val();
            let mapel = <?= (int) $mapel_id ?>;
            $.post('ajax_tambah_tugas.php', { kelas: kelas, mapel: mapel }, function (res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert('Gagal menambah tugas!');
                }
            }, 'json');
        });

        // Hapus kolom tugas (AJAX)
        $('.hapus-tugas-btn').on('click', function (e) {
            e.preventDefault();
            if (!confirm('Hapus kolom tugas ini?')) return;
            let tugas = $(this).data('tugas');
            let kelas = $('#kelas').val();
            let mapel = <?= (int) $mapel_id ?>;
            $.post('ajax_hapus_tugas.php', { kelas: kelas, mapel: mapel, tugas: tugas }, function (res) {
                if (res.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus tugas!');
                }
            }, 'json');
        });

        // Simpan nilai tugas (AJAX) dan update rata-rata
        $('.nilai-input').on('change', function () {
            let nilai = $(this).val();
            let siswa = $(this).data('siswa');
            let tugas = $(this).data('tugas');
            let kelas = $('#kelas').val();
            let mapel = <?= (int) $mapel_id ?>;
            if (nilai < 0 || nilai > 100) {
                alert('Nilai harus 0-100');
                $(this).val('');
                return;
            }
            $.post('ajax_simpan_nilai.php', {
                siswa: siswa, tugas: tugas, nilai: nilai, kelas: kelas, mapel: mapel
            }, function (res) {
                if (!res.success) {
                    alert('Gagal menyimpan nilai!');
                }
            }, 'json');
            // Hitung rata-rata otomatis
            let $row = $(this).closest('tr');
            hitungRataRata($row);
        });

        // Jika ingin rata-rata update juga saat UTS/UAS diubah:
        $('.nilai-uts, .nilai-uas').on('change', function () {
            let $row = $(this).closest('tr');
            hitungRataRata($row);
        });

        // Inisialisasi rata-rata saat halaman pertama kali load
        $('tr').each(function () {
            hitungRataRata($(this));
        });
    });
</script>