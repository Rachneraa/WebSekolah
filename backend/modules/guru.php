<?php
// Hapus session_start() karena sudah ada di admin.php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Database connection
require_once '../config/koneksi.php';

// Fungsi CRUD
function getAllGuru($db, $start = 0, $limit = 10)
{
    $query = "SELECT g.*, m.nama as nama_mapel 
              FROM guru g 
              LEFT JOIN mapel m ON g.mapel_id = m.id 
              LIMIT ?, ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $start, $limit);
    $stmt->execute();
    return $stmt->get_result();
}

function getTotalGuru($db)
{
    $query = "SELECT COUNT(*) as total FROM guru";
    $result = $db->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $foto = '';
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    // Pastikan direktori upload ada
                    $target_dir = __DIR__ . "/../../uploads/guru/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    // Generate nama file unik
                    $foto = uniqid() . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
                    $target_file = $target_dir . $foto;

                    // Upload file
                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $_SESSION['error'] = "Gagal mengupload file";
                        break;
                    }
                }

                // Hash password
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

                try {
                    $stmt = $db->prepare("INSERT INTO guru (nip, nama, jenis_kelamin, alamat, no_telp, foto, mapel_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param(
                        "ssssssss",
                        $_POST['nip'],
                        $_POST['nama'],
                        $_POST['jenis_kelamin'],
                        $_POST['alamat'],
                        $_POST['no_telp'],
                        $foto,
                        $_POST['mapel_id'],
                        $password_hash
                    );
                    $stmt->execute();
                    $_SESSION['success'] = "Data guru berhasil ditambahkan";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'edit':
                $foto = $_POST['foto_lama'];
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $target_dir = __DIR__ . "/../../uploads/guru/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    // Hapus foto lama jika ada
                    if ($_POST['foto_lama'] && file_exists($target_dir . $_POST['foto_lama'])) {
                        unlink($target_dir . $_POST['foto_lama']);
                    }

                    $foto = uniqid() . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
                    $target_file = $target_dir . $foto;

                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $_SESSION['error'] = "Gagal mengupload file";
                        break;
                    }
                }

                // Hash password
                $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

                try {
                    $stmt = $db->prepare("UPDATE guru SET nip=?, nama=?, jenis_kelamin=?, alamat=?, no_telp=?, foto=?, mapel_id=?, password=? WHERE id=?");
                    $stmt->bind_param(
                        "ssssssssi",
                        $_POST['nip'],
                        $_POST['nama'],
                        $_POST['jenis_kelamin'],
                        $_POST['alamat'],
                        $_POST['no_telp'],
                        $foto,
                        $_POST['mapel_id'],
                        $password_hash,
                        $_POST['id']
                    );
                    $stmt->execute();
                    $_SESSION['success'] = "Data guru berhasil diperbarui";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    // Hapus foto
                    $stmt = $db->prepare("SELECT foto FROM guru WHERE id = ?");
                    $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $guru = $result->fetch_assoc();

                    if ($guru['foto']) {
                        $target_dir = __DIR__ . "/../../uploads/guru/";
                        if (file_exists($target_dir . $guru['foto'])) {
                            unlink($target_dir . $guru['foto']);
                        }
                    }

                    // Hapus data
                    $stmt = $db->prepare("DELETE FROM guru WHERE id = ?");
                    $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $_SESSION['success'] = "Data guru berhasil dihapus";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get current page data
$page = isset($_GET['page_no']) ? (int) $_GET['page_no'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;
$total = getTotalGuru($db);
$total_pages = ceil($total / $limit);

try {
    $guru_list = getAllGuru($db, $start, $limit);
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    $guru_list = false;
}

// Display table
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title float-start">Data Guru</h3>
            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Tambah Guru
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Mata Pelajaran</th>
                            <th>Jenis Kelamin</th>
                            <th>No. Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($guru_list): ?>
                            <?php while ($row = $guru_list->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if ($row['foto']): ?>
                                            <img src="../uploads/guru/<?= htmlspecialchars($row['foto']) ?>"
                                                alt="Foto <?= htmlspecialchars($row['nama']) ?>" class="img-thumbnail"
                                                style="max-width: 50px;">
                                        <?php else: ?>
                                            <img src="../assets/img/default-user.png" alt="Default" class="img-thumbnail"
                                                style="max-width: 50px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['nip']) ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_mapel']) ?></td>
                                    <td><?= $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                    <td><?= htmlspecialchars($row['no_telp']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editGuru(<?= $row['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteGuru(<?= $row['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Guru -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            <?php
                            $mapel_query = "SELECT id, nama FROM mapel ORDER BY nama";
                            $mapel_result = $db->query($mapel_query);
                            while ($mapel = $mapel_result->fetch_assoc()) {
                                echo "<option value='" . $mapel['id'] . "'>" . htmlspecialchars($mapel['nama']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="no_telp" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
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

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="foto_lama" id="edit_foto_lama">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" id="edit_nip" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" id="edit_nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" id="edit_jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select" id="edit_mapel_id" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            <?php
                            $mapel_query = "SELECT id, nama FROM mapel ORDER BY nama";
                            $mapel_result = $db->query($mapel_query);
                            while ($mapel = $mapel_result->fetch_assoc()) {
                                echo "<option value='" . $mapel['id'] . "'>" . htmlspecialchars($mapel['nama']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" id="edit_alamat" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="no_telp" class="form-control" id="edit_no_telp">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="edit_password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" id="edit_foto">
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
    // Tambahkan script untuk menangani form submission
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#addModal form');
        form.addEventListener('submit', function (e) {
            // Validasi form di sisi client
            const nip = this.querySelector('[name="nip"]').value.trim();
            const nama = this.querySelector('[name="nama"]').value.trim();

            if (!nip || !nama) {
                e.preventDefault();
                alert('NIP dan Nama harus diisi!');
                return false;
            }
        });
    });

    function editGuru(id) {
        fetch(`get_guru.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_foto_lama').value = data.foto;
                // Set nilai form lainnya
                document.querySelector('#editModal [name="nip"]').value = data.nip;
                document.querySelector('#editModal [name="nama"]').value = data.nama;
                document.querySelector('#editModal [name="jenis_kelamin"]').value = data.jenis_kelamin;
                document.querySelector('#editModal [name="alamat"]').value = data.alamat;
                document.querySelector('#editModal [name="no_telp"]').value = data.no_telp;
                document.querySelector('#editModal [name="mapel_id"]').value = data.mapel_id;

                // Tampilkan modal
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
    }

    function deleteGuru(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data guru ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>