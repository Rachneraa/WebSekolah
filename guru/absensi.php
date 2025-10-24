<?php
$guru_id = $_SESSION['user_id'];
require_once '../config/koneksi.php';

// Ambil filter kelas dari GET
$kelas_filter = isset($_GET['kelas']) ? intval($_GET['kelas']) : 0;
$tanggal = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Ambil kelas yang diajar guru
$q_kelas = mysqli_query($db, "SELECT DISTINCT kelas.id, kelas.nama FROM jadwal 
    JOIN kelas ON jadwal.kelas_id=kelas.id 
    WHERE jadwal.guru_id='$guru_id'");
$kelas_list = [];
while ($row = mysqli_fetch_assoc($q_kelas))
    $kelas_list[] = $row;

// Ambil nama kelas
$kelas_nama = '';
if ($kelas_filter) {
    $kelas_stmt = $db->prepare("SELECT nama FROM kelas WHERE id = ?");
    $kelas_stmt->bind_param("i", $kelas_filter);
    $kelas_stmt->execute();
    $kelas_stmt->bind_result($kelas_nama);
    $kelas_stmt->fetch();
    $kelas_stmt->close();
}

// Ambil detail absensi siswa
$absensi_detail = [];
if ($kelas_filter) {
    $stmt = $db->prepare("SELECT ad.id, ad.siswa_id, s.nama AS nama_siswa, ad.status, ad.waktu_absen, aa.alasan 
        FROM absensi_detail ad
        JOIN siswa s ON ad.siswa_id = s.siswa_id
        LEFT JOIN absensi_alasan aa ON ad.siswa_id = aa.siswa_id AND ad.tanggal = aa.tanggal
        WHERE ad.kelas_id = ? AND ad.tanggal = ?
        ORDER BY ad.waktu_absen ASC");
    $stmt->bind_param("is", $kelas_filter, $tanggal);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $absensi_detail[] = $row;
    }
    $stmt->close();
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<h1>Absensi Kelas</h1>
<div class="card mb-4 p-3">
    <form method="get" class="row g-2">
        <div class="col-md-4">
            <select name="kelas" class="form-select" required>
                <option value="">Pilih Kelas</option>
                <?php foreach ($kelas_list as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelas_filter == $k['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($tanggal) ?>" required>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
</div>

<?php if ($kelas_filter): ?>
    <div class="card shadow">
        <div class="card-header">
            <strong>Detail Absensi Kelas <?= htmlspecialchars($kelas_nama) ?> (<?= htmlspecialchars($tanggal) ?>)</strong>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                        <th>Waktu Absen</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($absensi_detail)):
                        foreach ($absensi_detail as $ad): ?>
                            <tr>
                                <td><?= htmlspecialchars($ad['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($ad['status']) ?></td>
                                <td><?= htmlspecialchars($ad['waktu_absen']) ?></td>
                                <td><?= htmlspecialchars($ad['alasan'] ?? '-') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info"
                                        onclick="showAlasanModal('<?= $ad['siswa_id'] ?>', '<?= htmlspecialchars($ad['nama_siswa']) ?>', '<?= htmlspecialchars($ad['status']) ?>', '<?= htmlspecialchars($ad['alasan']) ?>')">
                                        Edit Alasan
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data absensi siswa</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Alasan -->
<div class="modal fade" id="alasanModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="formAlasan" method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Alasan Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="siswa_id" id="modalSiswaId">
                <input type="hidden" name="kelas_id" value="<?= $kelas_filter ?>">
                <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                <div class="mb-2">
                    <label>Nama Siswa</label>
                    <input type="text" id="modalNama" class="form-control" readonly>
                </div>
                <div class="mb-2">
                    <label>Status</label>
                    <input type="text" id="modalStatus" class="form-control" readonly>
                </div>
                <div class="mb-2">
                    <label>Alasan</label>
                    <input type="text" name="alasan" id="modalAlasan" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showAlasanModal(siswa_id, nama, status, alasan) {
        document.getElementById('modalSiswaId').value = siswa_id;
        document.getElementById('modalNama').value = nama;
        document.getElementById('modalStatus').value = status;
        document.getElementById('modalAlasan').value = alasan || '';
        var alasanModal = new bootstrap.Modal(document.getElementById('alasanModal'));
        alasanModal.show();
    }

    document.getElementById('formAlasan').onsubmit = function (e) {
        e.preventDefault();
        var form = this;
        var data = new FormData(form);
        fetch('simpan_alasan.php', {
            method: 'POST',
            body: data
        }).then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert('Alasan berhasil disimpan');
                    location.reload();
                } else {
                    alert('Gagal menyimpan alasan');
                }
            });
    };
</script>