<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan BASE_PATH didefinisikan
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Cek session
if (!isset($_SESSION['user_id'])) {
    // Jangan redirect jika file ini di-include, admin.php sudah menanganinya
    // header('Location: ../index.php');
    // exit();
}

require_once BASE_PATH . '/config/koneksi.php'; // Gunakan BASE_PATH
require_once BASE_PATH . '/phpqrcode/qrlib.php'; // Gunakan BASE_PATH

// Fungsi generate QR Code
function generateQRCode($kelas_id, $nama_kelas)
{
    try {
        // Cek ekstensi GD
        if (!extension_loaded('gd')) {
            throw new Exception('Ekstensi GD PHP tidak terinstall');
        }

        // Path ke folder uploads dari root BASE_PATH
        $qr_path = BASE_PATH . "/uploads/qrcodes/";
        if (!file_exists($qr_path)) {
            if (!mkdir($qr_path, 0777, true)) {
                throw new Exception('Gagal membuat direktori uploads');
            }
        }

        if (!is_writable($qr_path)) {
            throw new Exception('Direktori uploads tidak writable');
        }

        $file_name = $qr_path . "kelas_" . $kelas_id . ".png";
        $web_file_name = "kelas_" . $kelas_id . ".png"; // Nama file untuk disimpan di DB

        // Bangun URL absensi yang valid
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $scheme . '://' . $_SERVER['HTTP_HOST'];
        
        // Cari base path web. Asumsi: 'backend' adalah bagian dari URL
        // /smk-ti-main/backend/admin.php -> /smk-ti-main
        $webBasePath = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\'); 

        // ======================================================================
        // PERBAIKAN LINK QR CODE (Baris 49)
        // QR Code harusnya mengarah ke halaman scan SISWA, bukan modul admin.
        // Asumsi: halaman scan siswa bernama 'scan.php' dan ada di root folder web
        // Sesuaikan 'scan.php' jika nama filenya berbeda.
        // ======================================================================
        $qr_content = $host . $webBasePath . '/scan.php?kelas=' . $kelas_id;

        QRcode::png($qr_content, $file_name, QR_ECLEVEL_L, 10);
        return $web_file_name; // Kembalikan hanya nama file
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fungsi CRUD
function getAllKelas($db, $start = 0, $limit = 10)
{
    $query = "SELECT k.kelas_id, k.nama, k.qr_code, COUNT(s.siswa_id) AS jumlah_siswa
              FROM kelas k
              LEFT JOIN siswa s ON k.kelas_id = s.kelas_id
              GROUP BY k.kelas_id, k.nama, k.qr_code
              ORDER BY k.nama
              LIMIT ?, ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $start, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $db->begin_transaction(); // Mulai transaksi
                    
                    $stmt = $db->prepare("INSERT INTO kelas (nama) VALUES (?)");
                    $stmt->bind_param("s", $_POST['nama']);
                    $stmt->execute();
                    $kelas_id = $db->insert_id;

                    if ($kelas_id == 0) {
                         throw new Exception('Gagal mendapatkan ID kelas baru.');
                    }

                    // Generate QR Code
                    $qr_file = generateQRCode($kelas_id, $_POST['nama']);
                    if ($qr_file === false) {
                        throw new Exception('Gagal membuat QR code.');
                    }

                    // Update QR Code filename in database
                    $stmt_update = $db->prepare("UPDATE kelas SET qr_code = ? WHERE kelas_id = ?");
                    $stmt_update->bind_param("si", $qr_file, $kelas_id);
                    $stmt_update->execute();
                    
                    $db->commit(); // Selesaikan transaksi
                    $_SESSION['success'] = "Kelas berhasil ditambahkan";

                } catch (Exception $e) {
                    $db->rollback(); // Batalkan transaksi jika ada error
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    $stmt = $db->prepare("UPDATE kelas SET nama = ? WHERE kelas_id = ?");
                    $stmt->bind_param("si", $_POST['nama'], $_POST['id']);
                    $stmt->execute();
                    $_SESSION['success'] = "Kelas berhasil diperbarui";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $db->begin_transaction();
                    $kelas_id_to_delete = $_POST['id'];

                    // Hapus data terkait terlebih dahulu
                    $tables_to_delete_from = ['absensi_alasan', 'absensi_detail', 'absensi', 'jadwal', 'siswa'];
                    foreach ($tables_to_delete_from as $table) {
                         $stmt = $db->prepare("DELETE FROM $table WHERE kelas_id = ?");
                         $stmt->bind_param("i", $kelas_id_to_delete);
                         $stmt->execute();
                         $stmt->close();
                    }

                    // Baru hapus kelas
                    $stmt = $db->prepare("DELETE FROM kelas WHERE kelas_id = ?");
                    $stmt->bind_param("i", $kelas_id_to_delete);
                    $stmt->execute();
                    $stmt->close();
                    
                    $db->commit();
                    $_SESSION['success'] = "Kelas dan semua data terkait berhasil dihapus";

                } catch (Exception $e) {
                    $db->rollback();
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                // Redirect untuk menghindari re-post form
                header("Location: ?page=kelas");
                exit();
        }
        // Redirect setelah POST (add/edit) untuk menghindari re-post
        header("Location: ?page=kelas");
        exit();
    }
}

// Get data for table display
$page_no = isset($_GET['page_no']) ? (int) $_GET['page_no'] : 1;
$limit = 10;
$start = ($page_no - 1) * $limit;
$kelas_list = getAllKelas($db, $start, $limit);

// Ambil total data untuk pagination
$total_rows = $db->query("SELECT COUNT(kelas_id) as total FROM kelas")->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
?>

<div class="container-fluid">
    <!-- Tampilkan notifikasi -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title float-start">Data Kelas</h3>
            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Jumlah Siswa</th>
                            <th>QR Code</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($kelas_list && $kelas_list->num_rows > 0):
                            $no = $start + 1;
                            while ($row = $kelas_list->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= $row['jumlah_siswa'] ?></td>
                                    <td>
                                        <?php if ($row['qr_code']): 
                                            // Tentukan path web ke QR code
                                            // Asumsi: folder 'uploads' ada di luar folder 'backend'
                                            $qr_web_path = "../uploads/qrcodes/" . $row['qr_code'];
                                        ?>
                                            <img src="<?= $qr_web_path ?>"
                                                alt="QR Code <?= htmlspecialchars($row['nama']) ?>" style="width: 50px;">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= $qr_web_path ?>" class="btn btn-info"
                                                    download="QR_<?= htmlspecialchars($row['nama']) ?>.png">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button class="btn btn-success"
                                                    onclick="printQR('<?= $qr_web_path ?>', '<?= htmlspecialchars(addslashes($row['nama'])) ?>')">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <small class="text-muted">Belum di-generate</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                            onclick="editKelas(<?= $row['kelas_id'] ?>, '<?= htmlspecialchars(addslashes($row['nama'])) ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteKelas(<?= $row['kelas_id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <br>
                                        
                                        <!-- ====================================================== -->
                                        <!-- INI PERBAIKANNYA (Baris 267)                         -->
                                        <!-- Link ini memanggil admin.php (halaman utama)           -->
                                        <!-- dan meminta 'page=absensi'                           -->
                                        <!-- ====================================================== -->
                                        <a href="?page=absensi&kelas=<?= $row['kelas_id'] ?>"
                                            class="btn btn-sm btn-info mt-2">
                                            <i class="fas fa-clipboard-list"></i> Absensi
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data kelas.</td>
                            </tr>
                        <?php
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if($page_no > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=kelas&page_no=<?=($page_no - 1) ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page_no) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=kelas&page_no=<?=$i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if($page_no < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=kelas&page_no=<?=($page_no + 1) ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Form action dikosongkan agar submit ke halaman ini sendiri -->
            <form action="?page=kelas" method="post">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Form action dikosongkan agar submit ke halaman ini sendiri -->
            <form action="?page=kelas" method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kelas</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Absensi (Dihapus karena kita pindah halaman) -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function editKelas(id, nama) {
        // Tidak perlu fetch, data sudah ada
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    function deleteKelas(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Semua data terkait (siswa, jadwal, absensi) akan dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                // Arahkan ke halaman ?page=kelas
                form.action = '?page=kelas'; 
                form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="${id}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function printQR(qrFile, className) {
        const printWindow = window.open('', '', 'width=800,height=600');
        printWindow.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>QR Code Kelas ${className}</title>
                <style>
                    @page { size: A4; margin: 0; }
                    body { 
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        margin: 0;
                    }
                    .qr-container {
                        text-align: center;
                        padding: 20px;
                    }
                    img {
                        width: 400px;
                        height: 400px;
                    }
                    h2 { 
                        margin-top: 20px;
                        font-family: Arial, sans-serif;
                    }
                </style>
            </head>
            <body>
                <div class="qr-container">
                    <img src="${qrFile}" alt="QR Code">
                    <h2>Kelas ${className}</h2>
                    <p>Scan untuk Absensi</p>
                </div>
            </body>
        </html>
    `);
        printWindow.document.close();
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 250);
    }

    // Fungsi showAbsensiModal (sudah tidak relevan)
    // function showAbsensiModal(kelasId, kelasNama) { ... }
</script>

