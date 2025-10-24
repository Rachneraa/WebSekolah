<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../config/koneksi.php';

// Fungsi CRUD
function getAllSiswa($db, $start = 0, $limit = 10, $search = '', $kelas_filter = '')
{
    $where = [];
    $params = [];
    $types = '';

    if ($search) {
        $where[] = "(s.nama LIKE ? OR s.username LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
    }
    if ($kelas_filter) {
        $where[] = "s.kelas_id = ?";
        $params[] = $kelas_filter;
        $types .= 'i';
    }

    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $query = "SELECT s.*, k.nama as nama_kelas 
              FROM siswa s 
              LEFT JOIN kelas k ON s.kelas_id = k.id 
              $where_sql
              ORDER BY k.nama, s.nama 
              LIMIT ?, ?";
    $params[] = $start;
    $params[] = $limit;
    $types .= 'ii';

    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function getTotalSiswa($db, $search = '', $kelas_filter = '')
{
    $query = "SELECT COUNT(*) as total FROM siswa s 
              LEFT JOIN kelas k ON s.kelas_id = k.id 
              WHERE (s.nama LIKE ? OR s.username LIKE ?) AND (k.id = ? OR ? = '')";
    $stmt = $db->prepare($query);
    $search_param = "%$search%";
    $stmt->bind_param("ssii", $search_param, $search_param, $kelas_filter, $kelas_filter);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    // Hash password
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    $stmt = $db->prepare("INSERT INTO siswa (nama, username, password, kelas_id, nama_kelas) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param(
                        "sssis",
                        $_POST['nama'],
                        $_POST['username'],
                        $password,
                        $_POST['kelas_id'],
                        $_POST['nama_kelas']
                    );
                    $stmt->execute();
                    $_SESSION['success'] = "Data siswa berhasil ditambahkan";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    if (!empty($_POST['password'])) {
                        // Update dengan password baru
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $update_query = "UPDATE siswa SET nama=?, username=?, password=?, kelas_id=?, nama_kelas=? WHERE siswa_id=?";
                        $stmt = $db->prepare($update_query);
                        $stmt->bind_param(
                            "sssisi",
                            $_POST['nama'],
                            $_POST['username'],
                            $password,
                            $_POST['kelas_id'],
                            $_POST['nama_kelas'],
                            $_POST['id']
                        );
                    } else {
                        // Update tanpa password
                        $update_query = "UPDATE siswa SET nama=?, username=?, kelas_id=?, nama_kelas=? WHERE siswa_id=?";
                        $stmt = $db->prepare($update_query);
                        $stmt->bind_param(
                            "ssisi",
                            $_POST['nama'],
                            $_POST['username'],
                            $_POST['kelas_id'],
                            $_POST['nama_kelas'],
                            $_POST['id']
                        );
                    }
                    $stmt->execute();
                    $_SESSION['success'] = "Data siswa berhasil diperbarui";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    $delete_query = "DELETE FROM siswa WHERE siswa_id = ?";
                    $stmt = $db->prepare($delete_query);
                    $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $_SESSION['success'] = "Data siswa berhasil dihapus";
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
$search = isset($_GET['search']) ? $_GET['search'] : '';
$kelas_filter = isset($_GET['kelas_filter']) ? $_GET['kelas_filter'] : '';
$total = getTotalSiswa($db, $search, $kelas_filter);
$total_pages = ceil($total / $limit);

$siswa_list = getAllSiswa($db, $start, $limit, $search, $kelas_filter);

// Get kelas list for dropdown
$kelas_query = "SELECT * FROM kelas ORDER BY nama";
$kelas_result = $db->query($kelas_query);
?>

<!-- Alert Messages -->
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

<!-- Main Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title float-start">Data Siswa</h3>
            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Tambah Siswa
            </button>
        </div>
        <div class="card-body">
            <!-- Search & Filter -->
            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="page" value="siswa">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau username..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>
                <div class="col-md-4">
                    <select name="kelas_filter" class="form-select">
                        <option value="">Semua Kelas</option>
                        <?php
                        $kelas_result->data_seek(0);
                        while ($kelas = $kelas_result->fetch_assoc()):
                            ?>
                            <option value="<?= $kelas['id'] ?>" <?= (isset($_GET['kelas_filter']) && $_GET['kelas_filter'] == $kelas['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kelas['nama']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($siswa_list):
                            $no = $start + 1;
                            while ($row = $siswa_list->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editSiswa(<?= $row['siswa_id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSiswa(<?= $row['siswa_id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=siswa&page_no=1&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>"
                            tabindex="-1">First</a>
                    </li>
                    <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=siswa&page_no=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=siswa&page_no=<?= $i ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=siswa&page_no=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>">Next</a>
                    </li>
                    <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=siswa&page_no=<?= $total_pages ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>">Last</a>
                    </li>
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
                <h5 class="modal-title">Tambah Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required onchange="setNamaKelas(this)">
                            <option value="">Pilih Kelas</option>
                            <?php
                            $kelas_result->data_seek(0);
                            while ($kelas = $kelas_result->fetch_assoc()):
                                ?>
                                <option value="<?= $kelas['id'] ?>" data-nama="<?= htmlspecialchars($kelas['nama']) ?>">
                                    <?= htmlspecialchars($kelas['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="hidden" name="nama_kelas" id="nama_kelas">
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
                <h5 class="modal-title">Edit Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" id="edit_kelas_id" class="form-select" required
                            onchange="setNamaKelas(this)">
                            <option value="">Pilih Kelas</option>
                            <?php
                            $kelas_result->data_seek(0);
                            while ($kelas = $kelas_result->fetch_assoc()):
                                ?>
                                <option value="<?= $kelas['id'] ?>" data-nama="<?= htmlspecialchars($kelas['nama']) ?>">
                                    <?= htmlspecialchars($kelas['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="hidden" name="nama_kelas" id="edit_nama_kelas">
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
    function setNamaKelas(select) {
        const selectedOption = select.options[select.selectedIndex];
        const namaKelasInput = select.form.querySelector('[name="nama_kelas"]');
        namaKelasInput.value = selectedOption.dataset.nama || '';
    }

    function editSiswa(siswa_id) {
        fetch(`get_siswa.php?id=${siswa_id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.siswa_id;
                document.getElementById('edit_nama').value = data.nama;
                document.getElementById('edit_username').value = data.username;
                document.getElementById('edit_kelas_id').value = data.kelas_id;
                document.getElementById('edit_nama_kelas').value = data.nama_kelas;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            });
    }

    function deleteSiswa(id) {
        if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
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
<!-- Jangan ada include modul di sini! -->