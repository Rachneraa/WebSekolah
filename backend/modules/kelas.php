<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../config/koneksi.php';
require_once '../phpqrcode/qrlib.php';

// Fungsi generate QR Code
function generateQRCode($kelas_id, $nama_kelas)
{
    try {
        // Cek ekstensi GD
        if (!extension_loaded('gd')) {
            throw new Exception('Ekstensi GD PHP tidak terinstall');
        }

        $qr_path = "../uploads/qrcodes/";
        if (!file_exists($qr_path)) {
            if (!mkdir($qr_path, 0777, true)) {
                throw new Exception('Gagal membuat direktori uploads');
            }
        }

        if (!is_writable($qr_path)) {
            throw new Exception('Direktori uploads tidak writable');
        }

        $file_name = $qr_path . "kelas_" . $kelas_id . ".png";
        $qr_content = "http://" . $_SERVER['HTTP_HOST'] . "/Web-Sekolah/absensi.php?kelas=" . $kelas_id;

        QRcode::png($qr_content, $file_name, QR_ECLEVEL_L, 10);
        return "kelas_" . $kelas_id . ".png";
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

// Fungsi CRUD
function getAllKelas($db, $start = 0, $limit = 10)
{
    $query = "SELECT k.*, COUNT(s.id) as jumlah_siswa 
              FROM kelas k 
              LEFT JOIN siswa s ON k.id = s.kelas_id 
              GROUP BY k.id 
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
                    $stmt = $db->prepare("INSERT INTO kelas (nama) VALUES (?)");
                    $stmt->bind_param("s", $_POST['nama']);
                    $stmt->execute();
                    $kelas_id = $db->insert_id;

                    // Generate QR Code
                    $qr_file = generateQRCode($kelas_id, $_POST['nama']);

                    // Update QR Code filename in database
                    $stmt = $db->prepare("UPDATE kelas SET qr_code = ? WHERE id = ?");
                    $stmt->bind_param("si", $qr_file, $kelas_id);
                    $stmt->execute();

                    $_SESSION['success'] = "Kelas berhasil ditambahkan";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            // ... handle edit & delete cases
        }
    }
}

// Get data for table display
$page = isset($_GET['page_no']) ? (int) $_GET['page_no'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;
$kelas_list = getAllKelas($db, $start, $limit);
?>

<div class="container-fluid">
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
                        if ($kelas_list):
                            $no = $start + 1;
                            while ($row = $kelas_list->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= $row['jumlah_siswa'] ?></td>
                                    <td>
                                        <?php if ($row['qr_code']): ?>
                                            <img src="../uploads/qrcodes/<?= $row['qr_code'] ?>"
                                                alt="QR Code <?= htmlspecialchars($row['nama']) ?>" style="width: 50px;">
                                            <div class="btn-group btn-group-sm">
                                                <a href="../uploads/qrcodes/<?= $row['qr_code'] ?>" class="btn btn-info"
                                                    download="QR_<?= htmlspecialchars($row['nama']) ?>.png">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button class="btn btn-success"
                                                    onclick="printQR('<?= $row['qr_code'] ?>', '<?= htmlspecialchars($row['nama']) ?>')">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editKelas(<?= $row['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteKelas(<?= $row['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            endwhile;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal dan Edit Modal sama seperti modul lain -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post">
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
            <form action="" method="post">
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

<script>
    function editKelas(id) {
        fetch(`get_kelas.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_nama').value = data.nama;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
    }

    function deleteKelas(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data kelas akan dihapus permanen!",
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
                    <img src="../uploads/qrcodes/${qrFile}" alt="QR Code">
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
</script>