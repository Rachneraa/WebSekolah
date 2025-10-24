<?php
// session_start(); // Sudah dipanggil di admin.php
require_once __DIR__ . '/../../config/koneksi.php';

// Hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Ambil data pendaftar
$query = "SELECT id, nisn, nama_lengkap, jenis_kelamin, jurusan, status FROM ppdb_pendaftar ORDER BY id DESC";
$result = mysqli_query($db, $query);

$pendaftar = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pendaftar[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPDB Admin - SMK TI Garuda Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .filter-bar {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .filter-bar>* {
            min-width: 180px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2>Manajemen Pendaftar PPDB</h2>
        <div class="filter-bar mb-3">
            <select id="filterJurusan" class="form-select">
                <option value="">Semua Jurusan</option>
                <option value="rpl">RPL</option>
                <option value="tkj">TKJ</option>
                <option value="animasi">Animasi</option>
                <option value="dkv">DKV</option>
                <option value="mp">MP</option>
                <option value="tjat">TJAT</option>
            </select>
            <select id="filterStatus" class="form-select">
                <option value="">Semua Status</option>
                <option value="proses">Proses</option>
                <option value="diterima">Diterima</option>
                <option value="ditolak">Ditolak</option>
            </select>
            <input type="text" id="searchNama" class="form-control" placeholder="Cari Nama...">
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="ppdbAdminTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pendaftar) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada pendaftar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendaftar as $i => $row): ?>
                            <tr data-jurusan="<?= strtolower($row['jurusan']) ?>"
                                data-status="<?= strtolower($row['status']) ?>"
                                data-nama="<?= strtolower($row['nama_lengkap']) ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['nisn']) ?></td>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= ucwords($row['jenis_kelamin']) ?></td>
                                <td><?= strtoupper($row['jurusan']) ?></td>
                                <td>
                                    <select class="form-select status-select" data-id="<?= $row['id'] ?>">
                                        <option value="proses" <?= $row['status'] == 'proses' ? 'selected' : '' ?>>Proses
                                        </option>
                                        <option value="diterima" <?= $row['status'] == 'diterima' ? 'selected' : '' ?>>Diterima
                                        </option>
                                        <option value="ditolak" <?= $row['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm hapus-btn" data-id="<?= $row['id'] ?>"><i
                                            class="fas fa-trash"></i> Hapus</button>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
        <div class="pagination"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Status update (AJAX + SweetAlert2)
        document.querySelectorAll('.status-select').forEach(function (select) {
            select.addEventListener('change', function () {
                const id = this.dataset.id;
                const statusBaru = this.value;
                Swal.fire({
                    title: 'Konfirmasi Ubah Status',
                    text: 'Yakin ingin mengubah status pendaftar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('modules/ubah_status_ppdb.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'id=' + id + '&status=' + statusBaru
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Berhasil!', 'Status berhasil diubah.', 'success');
                                    // Update data-status pada row
                                    this.closest('tr').setAttribute('data-status', statusBaru);
                                } else {
                                    Swal.fire('Gagal!', 'Status gagal diubah.', 'error');
                                }
                            });
                    } else {
                        this.value = this.getAttribute('data-old');
                    }
                });
            });
            select.setAttribute('data-old', select.value);
        });

        // Filter & Search
        const filterJurusan = document.getElementById('filterJurusan');
        const filterStatus = document.getElementById('filterStatus');
        const searchNama = document.getElementById('searchNama');
        const tableRows = document.querySelectorAll('#ppdbAdminTable tbody tr');

        function filterTable() {
            const jurusan = filterJurusan.value.toLowerCase();
            const status = filterStatus.value.toLowerCase();
            const nama = searchNama.value.toLowerCase();

            let no = 1;
            tableRows.forEach(row => {
                const rowJurusan = row.getAttribute('data-jurusan');
                const rowStatus = row.getAttribute('data-status');
                const rowNama = row.getAttribute('data-nama');
                let show = true;
                if (jurusan && rowJurusan !== jurusan) show = false;
                if (status && rowStatus !== status) show = false;
                if (nama && !rowNama.includes(nama)) show = false;
                row.style.display = show ? '' : 'none';
                if (show) row.querySelector('td').textContent = no++;
            });
        }

        filterJurusan.addEventListener('change', filterTable);
        filterStatus.addEventListener('change', filterTable);
        searchNama.addEventListener('input', filterTable);
    </script>
</body>

</html>