<?php
// session_start(); // Sudah dipanggil di admin.php
require_once __DIR__ . '/../../config/koneksi.php';

// Hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Ambil daftar kelas untuk dropdown
$kelas_list = [];
$kelas_result = $db->query("SELECT kelas_id, nama FROM kelas ORDER BY nama");
if ($kelas_result) {
    while ($row = $kelas_result->fetch_assoc()) {
        $kelas_list[] = $row;
    }
}

// --- FUNGSI UNTUK AMBIL DATA (SERVER-SIDE) ---
function getPendaftar($db, $where_sql, $params, $types, $start, $limit) {
    // Query ini sudah benar mengambil 'alamat_email'
    $query = "SELECT id, nisn, nama_lengkap, jenis_kelamin, jurusan, status, no_hp, alamat_email, status_akun 
              FROM ppdb_pendaftar 
              $where_sql 
              ORDER BY id DESC 
              LIMIT ?, ?";
    
    $params[] = $start;
    $params[] = $limit;
    $types .= 'ii';
    
    $stmt = $db->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

function getTotalPendaftar($db, $where_sql, $params, $types) {
    $query = "SELECT COUNT(*) as total FROM ppdb_pendaftar $where_sql";
    $stmt = $db->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

// --- LOGIKA FILTER DAN PAGINASI ---
$jurusan_filter = $_GET['jurusan'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(nisn LIKE ? OR nama_lengkap LIKE ?)";
    $search_param = "$search%"; 
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}
if (!empty($jurusan_filter)) {
    $where[] = "jurusan = ?";
    $params[] = $jurusan_filter;
    $types .= 's';
}
if (!empty($status_filter)) {
    $where[] = "status = ?";
    $params[] = $status_filter;
    $types .= 's';
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';


// 3. Siapkan Paginasi
$page = isset($_GET['page_no']) ? (int) $_GET['page_no'] : 1;
// === INI PERUBAHAN ANDA ===
$limit = 10; // Diubah dari 15 menjadi 10
// === AKHIR PERUBAHAN ===
$start = ($page - 1) * $limit;

// 4. Ambil Total Data dan Data Pendaftar
$total = getTotalPendaftar($db, $where_sql, $params, $types);
$total_pages = $total > 0 ? ceil($total / $limit) : 1;
$pendaftar_result = getPendaftar($db, $where_sql, $params, $types, $start, $limit);

$jurusan_list = ['rpl', 'tkj', 'animasi', 'dkv', 'mp', 'tjat'];
$status_list = ['proses', 'diterima', 'ditolak'];

// === INI PERBAIKAN PAGINASI (Baris Baru) ===
// Kueri string untuk filter, agar tetap ada saat ganti halaman
$query_params = http_build_query([
    'page' => 'ppdb_admin',
    'jurusan' => $jurusan_filter,
    'status' => $status_filter,
    'search' => $search
]);
// === AKHIR PERBAIKAN ===
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPDB Admin - SMK TI Garuda Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0 text-white">Manajemen Pendaftar PPDB</h3>
            </div>
            <div class="card-body">
                
                <form method="get" class="row g-2 mb-3">
                    <input type="hidden" name="page" value="ppdb_admin">
                    <input type="hidden" name="jurusan" value="<?= htmlspecialchars($jurusan_filter) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
                    
                    <div class="col-md-9">
                        <input type="text" name="search" class="form-control" placeholder="Cari Nama atau NISN (dari awal)..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>
                
                <div class="mb-2">
                    <label class="form-label" style="font-weight: 500;">Filter Jurusan:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?page=ppdb_admin&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&jurusan=" 
                           class="btn btn-sm <?= empty($jurusan_filter) ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Semua Jurusan
                        </a>
                        <?php foreach ($jurusan_list as $jur) :
                            $is_active = ($jurusan_filter == $jur);
                        ?>
                        <a href="?page=ppdb_admin&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>&jurusan=<?= urlencode($jur) ?>" 
                           class="btn btn-sm <?= $is_active ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <?= strtoupper($jur) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Filter Status:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="?page=ppdb_admin&jurusan=<?= urlencode($jurusan_filter) ?>&search=<?= urlencode($search) ?>&status=" 
                           class="btn btn-sm <?= empty($status_filter) ? 'btn-primary' : 'btn-outline-primary' ?>">
                            Semua Status
                        </a>
                        <?php foreach ($status_list as $stat) :
                            $is_active = ($status_filter == $stat);
                        ?>
                        <a href="?page=ppdb_admin&jurusan=<?= urlencode($jurusan_filter) ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($stat) ?>" 
                           class="btn btn-sm <?= $is_active ? 'btn-primary' : 'btn-outline-primary' ?>">
                            <?= ucfirst($stat) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-3 text-end">
                    <button id="btn-clear-data" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Bersihkan Data Selesai
                    </button>
                    <small class="text-muted d-block mt-1">
                        Akan menghapus pendaftar yang statusnya 'Ditolak' atau 'Diterima' (dan sudah punya akun).
                    </small>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="ppdbAdminTable">
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
                            <?php if ($total === 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada pendaftar yang cocok dengan filter.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = $start + 1; ?>
                                <?php while ($row = $pendaftar_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
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
                                            <?php
                                            if ($row['status_akun'] == 1) {
                                                echo '<span class="badge bg-success">Akun Dibuat</span>';
                                            
                                            } else if ($row['status_akun'] == 0 && $row['status'] == 'diterima') {
                                            ?>
                                                <button class="btn btn-primary btn-sm btn-buat-akun" 
                                                        data-id="<?= $row['id'] ?>" 
                                                        data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>" 
                                                        data-wa="<?= htmlspecialchars($row['no_hp']) ?>"
                                                        data-email="<?= htmlspecialchars($row['alamat_email'] ?? '') ?>">
                                                    <i class="fas fa-key"></i> Buat Akun
                                                </button>
                                            <?php
                                            } else {
                                                echo '<span>-</span>';
                                            }
                                            ?>
                                        </td>
                                        
                                    </tr>
                                <?php endwhile ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center flex-wrap">
                        
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= $query_params ?>&page_no=<?= max(1, $page - 1) ?>">Previous</a>
                        </li>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= $query_params ?>&page_no=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?<?= $query_params ?>&page_no=<?= min($total_pages, $page + 1) ?>">Next</a>
                        </li>

                    </ul>
                    </nav>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // FUNGSI UNTUK UBAH STATUS (TETAP ADA)
        function postWithRetry(url, body, tries = 3, delayMs = 800) {
            // ... (kode tidak berubah)
            return new Promise((resolve, reject) => {
                const attempt = (n) => {
                    fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body
                    })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json();
                        })
                        .then(resolve)
                        .catch(err => {
                            if (n > 1) {
                                setTimeout(() => attempt(n - 1), delayMs);
                                delayMs *= 2;
                            } else {
                                reject(err);
                            }
                        });
                };
                attempt(tries);
            });
        }

        // EVENT LISTENER UNTUK UBAH STATUS (TETAP ADA)
        document.querySelectorAll('.status-select').forEach(function (select) {
            // ... (kode tidak berubah)
            select.addEventListener('change', function () {
                const selectEl = this;
                const id = selectEl.dataset.id;
                const statusBaru = selectEl.value;
                Swal.fire({
                    title: 'Konfirmasi Ubah Status',
                    text: 'Yakin ingin mengubah status pendaftar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        postWithRetry('modules/ubah_status_ppdb.php', 'id=' + encodeURIComponent(id) + '&status=' + encodeURIComponent(statusBaru))
                            .then(data => {
                                if (data.success) {
                                    let msg = 'Status diubah.';
                                    if (statusBaru === 'diterima' || statusBaru === 'ditolak') {
                                        if (data.wa_sent) {
                                            msg += ' Pesan WhatsApp berhasil dikirim.';
                                        } else {
                                            msg += ' WhatsApp gagal dikirim: ' + (data.wa_error || data.wa_response || 'Tidak diketahui');
                                        }
                                    }
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: msg,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload(); 
                                    });
                                } else {
                                    Swal.fire('Gagal!', data.error || 'Status gagal diubah.', 'error');
                                    selectEl.value = selectEl.getAttribute('data-old');
                                }
                            })
                            .catch(err => {
                                console.error('Network/error:', err);
                                Swal.fire('Kesalahan Jaringan', 'Tidak dapat terhubung ke server. Silakan coba lagi atau hubungi admin.', 'error');
                                selectEl.value = selectEl.getAttribute('data-old');
                            });
                    } else {
                        selectEl.value = selectEl.getAttribute('data-old');
                    }
                });
            });
            select.setAttribute('data-old', select.value);
        });


        // JAVASCRIPT UNTUK TOMBOL 'BUAT AKUN' (TETAP ADA)
        const daftarKelas = <?= json_encode($kelas_list) ?>;
        document.querySelectorAll('.btn-buat-akun').forEach(button => {
            // ... (kode tidak berubah)
            button.addEventListener('click', function() {
                const idSiswa = this.dataset.id;
                const namaSiswa = this.dataset.nama;
                const noHp = this.dataset.wa; 
                const emailSiswa = this.dataset.email; 

                let kelasOptions = '<option value="">Pilih Kelas...</option>';
                daftarKelas.forEach(kelas => {
                    kelasOptions += `<option value="${kelas.kelas_id}">${kelas.nama}</option>`;
                });

                const formHtml = `
                    <form id="swal-form" style="text-align: left;">
                        <div class="mb-3">
                            <label for="swal-kelas" class="form-label"><b>Kelas*</b></label>
                            <select id="swal-kelas" class="form-select" required>
                                ${kelasOptions}
                            </select>
                            <small>Siswa akan dimasukkan ke kelas ini.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><b>Email</b></label>
                            <input type="email" class="form-control" value="${emailSiswa || ''}" disabled>
                            <small>Email diambil otomatis dari pendaftaran.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><b>No. Telepon</b></label>
                            <input type="text" class="form-control" value="${noHp}" disabled>
                            <small>Nomor telepon diambil otomatis dari pendaftaran.</small>
                        </div>
                    </form>
                `;

                Swal.fire({
                    title: `Buat Akun: ${namaSiswa}`,
                    html: formHtml,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Buat Akun & Kirim WA',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        const kelasId = document.getElementById('swal-kelas').value;
                        if (!kelasId) {
                            Swal.showValidationMessage('Tolong pilih kelas untuk siswa ini');
                            return false;
                        }
                        return { kelas_id: kelasId };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = result.value; 
                        const buttonEl = this;
                        buttonEl.disabled = true;
                        buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                        fetch('api/buat_akun_ppdb.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id_pendaftar: idSiswa,
                                kelas_id: formData.kelas_id
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: `Akun untuk ${namaSiswa} telah dibuat (Username: ${data.username}). Notifikasi WA sedang dikirim.`,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload(); 
                                });
                            } else {
                                Swal.fire('Gagal!', data.message || 'Gagal membuat akun.', 'error');
                                buttonEl.disabled = false;
                                buttonEl.innerHTML = '<i class="fas fa-key"></i> Buat Akun';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                            buttonEl.disabled = false;
                            buttonEl.innerHTML = '<i class="fas fa-key"></i> Buat Akun';
                        });
                    }
                });
            });
        });

        // JAVASCRIPT BARU UNTUK TOMBOL "BERSIHKAN DATA" (TETAP ADA)
        document.getElementById('btn-clear-data').addEventListener('click', function() {
            // ... (kode ini tidak berubah)
            const button = this;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghitung data...';

            fetch('api/hapus_data_ppdb.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'check' })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }

                if (data.count === 0) {
                    Swal.fire('Info', 'Tidak ada data yang bisa dibersihkan saat ini.', 'info');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-trash"></i> Bersihkan Data Selesai';
                    return;
                }

                Swal.fire({
                    title: 'Anda Yakin?',
                    text: `Ini akan menghapus ${data.count} pendaftar (yang ditolak / sudah punya akun). Data tidak bisa dikembalikan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus Permanen',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus data...';

                        fetch('api/hapus_data_ppdb.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ action: 'delete' })
                        })
                        .then(res => res.json())
                        .then(deleteData => {
                            if (deleteData.success) {
                                Swal.fire({
                                    title: 'Berhasil Dihapus!',
                                    text: `${deleteData.deleted_rows} data pendaftar telah dihapus.`,
                                    icon: 'success'
                                }).then(() => location.reload());
                            } else {
                                throw new Error(deleteData.message);
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error!', err.message, 'error');
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-trash"></i> Bersihkan Data Selesai';
                        });
                    } else {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-trash"></i> Bersihkan Data Selesai';
                    }
                });

            })
            .catch(err => {
                Swal.fire('Error!', err.message, 'error');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-trash"></i> Bersihkan Data Selesai';
            });
        });
    </script>
</body>

</html>