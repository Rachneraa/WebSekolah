<?php
use PhpOffice\PhpSpreadsheet\IOFactory;

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

// === PERUBAHAN: Menambahkan $search ===
function getAllGuru($db, $start = 0, $limit = 10, $search = '')
{
    $where = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        // Cari berdasarkan NIP atau Nama
        $where[] = "(g.nip LIKE ? OR g.nama LIKE ?)";
        
        // === PERBAIKAN: Diubah dari "%$search%" menjadi "$search%" agar berfungsi seperti startsWith ===
        $search_param = "$search%"; 
        
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }
    
    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $query = "SELECT g.*, m.nama as nama_mapel 
              FROM guru g 
              LEFT JOIN mapel m ON g.mapel_id = m.id 
              $where_sql
              ORDER BY g.nama ASC
              LIMIT ?, ?";
    
    $params[] = $start;
    $params[] = $limit;
    $types .= 'ii';

    $stmt = $db->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

// === PERUBAHAN: Menambahkan $search ===
function getTotalGuru($db, $search = '')
{
    $where = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        $where[] = "(nip LIKE ? OR nama LIKE ?)";

        // === PERBAIKAN: Diubah dari "%$search%" menjadi "$search%" agar berfungsi seperti startsWith ===
        $search_param = "$search%"; 

        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }
    
    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $query = "SELECT COUNT(*) as total FROM guru $where_sql";
    
    $stmt = $db->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return (int) $row['total'];
}

// Handle form submission (LOGIKA INI TIDAK BERUBAH)
if (isset($_POST['action'])) {
    switch ($_POST['action']) {

        case 'add':
            $nip = $_POST['nip'];
            $nama = $_POST['nama'];
            $jenis_kelamin = $_POST['jenis_kelamin'];
            $alamat = $_POST['alamat'];
            $no_telp = $_POST['no_telp'];
            $mapel_id = $_POST['mapel_id'];
            $password = $_POST['password'];
            
            if (empty($nip)) {
                $_SESSION['error'] = "NIP wajib diisi (akan digunakan sebagai username login).";
                break;
            }
            if (empty($password)) {
                $_SESSION['error'] = "Password wajib diisi untuk membuat akun login.";
                break; 
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $db->begin_transaction();
            $foto = '';
            $target_file = '';

            try {
                $stmt_check_user = $db->prepare("SELECT id FROM users WHERE username = ?");
                $stmt_check_user->bind_param("s", $nip);
                $stmt_check_user->execute();
                $result_check = $stmt_check_user->get_result();
                if ($result_check->num_rows > 0) {
                    throw new Exception("Username (NIP) '$nip' sudah terdaftar di tabel login. Gunakan NIP lain.");
                }
                $stmt_check_user->close();

                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $target_dir = __DIR__ . "/../../uploads/guru/"; 
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $foto_ext = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
                    if (empty($foto_ext)) $foto_ext = 'jpg';
                    $foto = uniqid() . "." . $foto_ext;
                    $target_file = $target_dir . $foto;
                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        throw new Exception("Gagal mengupload file foto.");
                    }
                }

                $stmt_guru = $db->prepare("INSERT INTO guru (nip, nama, jenis_kelamin, alamat, no_telp, foto, mapel_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_guru->bind_param(
                    "ssssssi", 
                    $nip, $nama, $jenis_kelamin, $alamat, $no_telp, $foto, $mapel_id
                );
                $stmt_guru->execute();
                $new_guru_id = $db->insert_id;
                $stmt_guru->close();

                $stmt_user = $db->prepare("INSERT INTO users (username, password, level, guru_id) VALUES (?, ?, 'guru', ?)");
                $stmt_user->bind_param(
                    "ssi", $nip, $password_hash, $new_guru_id
                );
                $stmt_user->execute();
                $stmt_user->close();

                $db->commit();
                $_SESSION['success'] = "Data guru DAN akun login berhasil ditambahkan. Username login adalah NIP.";

            } catch (Exception $e) {
                $db->rollback();
                if ($db->errno == 1062 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $_SESSION['error'] = "Error: NIP '$nip' sudah terdaftar. Gunakan NIP lain.";
                } else {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                if (!empty($foto) && file_exists($target_file)) {
                    unlink($target_file);
                }
            }
            break;

        case 'edit':
            $guru_id = $_POST['id'];
            $nip = $_POST['nip'];
            $password = $_POST['password'];

            $db->begin_transaction();
            $target_file = '';
            $target_dir = __DIR__ . "/../../uploads/guru/";

            try {
                $stmt_check = $db->prepare("SELECT id FROM users WHERE username = ? AND guru_id != ?");
                $stmt_check->bind_param("si", $nip, $guru_id);
                $stmt_check->execute();
                if ($stmt_check->get_result()->num_rows > 0) {
                    throw new Exception("Username (NIP) '$nip' sudah digunakan oleh guru lain.");
                }
                $stmt_check->close();

                $foto = $_POST['foto_lama'];
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    if ($foto && file_exists($target_dir . $foto)) {
                        unlink($target_dir . $foto);
                    }
                    $foto = uniqid() . "." . pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
                    $target_file = $target_dir . $foto;
                    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        throw new Exception("Gagal mengupload file foto baru.");
                    }
                }

                $stmt_guru = $db->prepare("UPDATE guru SET nip=?, nama=?, jenis_kelamin=?, alamat=?, no_telp=?, foto=?, mapel_id=? WHERE id=?");
                $stmt_guru->bind_param(
                    "ssssssii",
                    $nip, $_POST['nama'], $_POST['jenis_kelamin'], $_POST['alamat'],
                    $_POST['no_telp'], $foto, $_POST['mapel_id'], $guru_id
                );
                $stmt_guru->execute();
                $stmt_guru->close();
                
                if (!empty($password)) {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_user = $db->prepare("UPDATE users SET username = ?, password = ? WHERE guru_id = ?");
                    $stmt_user->bind_param("ssi", $nip, $password_hash, $guru_id);
                } else {
                    $stmt_user = $db->prepare("UPDATE users SET username = ? WHERE guru_id = ?");
                    $stmt_user->bind_param("si", $nip, $guru_id);
                }
                $stmt_user->execute();
                $stmt_user->close();

                $db->commit();
                $_SESSION['success'] = "Data guru dan akun login berhasil diperbarui";

            } catch (Exception $e) {
                $db->rollback();
                if ($db->errno == 1062 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                     $_SESSION['error'] = "Error: NIP '$nip' sudah terdaftar. Gunakan NIP lain.";
                } else {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                if ($target_file && file_exists($target_file)) {
                    unlink($target_file);
                }
            }
            break;

        case 'delete':
            $guru_id = $_POST['id'];
            $db->begin_transaction();
            $foto_path = '';

            try {
                $stmt_foto = $db->prepare("SELECT foto FROM guru WHERE id = ?");
                $stmt_foto->bind_param("i", $guru_id);
                $stmt_foto->execute();
                $result = $stmt_foto->get_result();
                $guru = $result->fetch_assoc();
                if ($guru && $guru['foto']) {
                    $foto_path = __DIR__ . "/../../uploads/guru/" . $guru['foto'];
                }
                $stmt_foto->close();

                $stmt_guru = $db->prepare("DELETE FROM guru WHERE id = ?");
                $stmt_guru->bind_param("i", $guru_id);
                $stmt_guru->execute();
                $stmt_guru->close();

                $stmt_user = $db->prepare("DELETE FROM users WHERE guru_id = ?");
                $stmt_user->bind_param("i", $guru_id);
                $stmt_user->execute();
                $stmt_user->close();

                $db->commit();
                $_SESSION['success'] = "Data guru dan akun login terkait berhasil dihapus";

                if ($foto_path && file_exists($foto_path)) {
                    unlink($foto_path);
                }

            } catch (Exception $e) {
                $db->rollback();
                $_SESSION['error'] = "Error: " . $e->getMessage();
            }
            break;
    }
    header("Location: ?page=guru");
    exit();
}

// LOGIKA IMPORT GURU (TIDAK BERUBAH)
if (isset($_POST['import_guru'])) {
    require_once BASE_PATH . '/vendor/autoload.php';

    if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
        $_SESSION['error'] = "Library PhpSpreadsheet tidak ditemukan.";
        header("Location: ?page=guru");
        exit();
    }
    if (!class_exists('ZipArchive')) {
        $_SESSION['error'] = "Ekstensi 'zip' PHP tidak aktif. Import file Excel (.xlsx) tidak akan berfungsi.";
    }
    if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] != 0) {
        $_SESSION['error'] = "Gagal mengupload file";
        header("Location: ?page=guru");
        exit();
    }

    $file_tmp = $_FILES['import_file']['tmp_name'];
    $file_name = $_FILES['import_file']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, ['xlsx','xls','ods', 'csv'])) {
        $_SESSION['error'] = "Hanya file Excel (.xlsx/.xls/.ods) atau .csv yang diperbolehkan";
    } else {
        
        $rows = [];
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_tmp);
            $sheet = $spreadsheet->getActiveSheet();
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); 
                $data = [];
                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }
                if (!empty(implode('', $data))) {
                    $rows[] = $data;
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal membaca file: " . $e->getMessage();
            header("Location: ?page=guru");
            exit();
        }

        $imported = 0;
        $skipped = 0;
        $updated = 0;
        $skipped_reasons = [];

        $db->begin_transaction();
        try {
            $mapel_map = [];
            $res_mapel = $db->query("SELECT id, nama FROM mapel");
            while ($m = $res_mapel->fetch_assoc()) {
                $mapel_map[strtolower(trim($m['nama']))] = $m['id'];
            }

            foreach ($rows as $i => $data) {
                if ($i == 0) continue; 
                if (count($data) < 6) {
                    $skipped++;
                    $skipped_reasons[] = "Baris " . ($i+1) . ": Data tidak lengkap.";
                    continue; 
                }

                $nip = trim($data[0]);
                $nama = trim($data[1]);
                $jenis_kelamin_raw = strtolower(trim($data[2]));
                $alamat = trim($data[3]);
                $no_telp = trim($data[4]);
                $nama_mapel = strtolower(trim($data[5]));

                if (empty($nip) || empty($nama)) {
                    $skipped++;
                    $skipped_reasons[] = "Baris " . ($i+1) . ": NIP atau Nama kosong.";
                    continue;
                }
                $jenis_kelamin = ($jenis_kelamin_raw == 'laki-laki' || $jenis_kelamin_raw == 'l') ? 'L' : 'P';
                $mapel_id = $mapel_map[$nama_mapel] ?? null;
                
                $password_hash = password_hash($nip, PASSWORD_DEFAULT); 

                $stmt_check = $db->prepare("SELECT u.id, g.id as guru_id FROM users u LEFT JOIN guru g ON u.guru_id = g.id WHERE u.username = ?");
                $stmt_check->bind_param("s", $nip);
                $stmt_check->execute();
                $res_check = $stmt_check->get_result();

                if ($res_check->num_rows > 0) {
                    $existing = $res_check->fetch_assoc();
                    $guru_id = $existing['guru_id'];
                    
                    if ($guru_id) {
                        $stmt_up_guru = $db->prepare("UPDATE guru SET nama=?, jenis_kelamin=?, alamat=?, no_telp=?, mapel_id=? WHERE id=?");
                        $stmt_up_guru->bind_param("ssssii", $nama, $jenis_kelamin, $alamat, $no_telp, $mapel_id, $guru_id);
                        $stmt_up_guru->execute();
                        $stmt_up_guru->close();
                    }
                    $updated++;
                } else {
                    $stmt_guru = $db->prepare("INSERT INTO guru (nip, nama, jenis_kelamin, alamat, no_telp, mapel_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt_guru->bind_param("sssssi", $nip, $nama, $jenis_kelamin, $alamat, $no_telp, $mapel_id);
                    $stmt_guru->execute();
                    $new_guru_id = $db->insert_id;
                    $stmt_guru->close();

                    $stmt_user = $db->prepare("INSERT INTO users (username, password, level, guru_id) VALUES (?, ?, 'guru', ?)");
                    $stmt_user->bind_param("ssi", $nip, $password_hash, $new_guru_id);
                    $stmt_user->execute();
                    $stmt_user->close();
                    
                    $imported++;
                }
                $stmt_check->close();
            }
            
            $db->commit();
            $_SESSION['success'] = "Import selesai. Ditambahkan: $imported, Diperbarui: $updated, Dilewati: $skipped.";
            if($skipped > 0) {
                 $_SESSION['import_skipped_reasons'] = $skipped_reasons;
            }

        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['error'] = "Import Gagal: " . $e->getMessage();
        }
    }
    
    header("Location: ?page=guru");
    exit();
}


// ===================================================================
// === LOGIKA PENGAMBILAN DATA UNTUK TAMPILAN ===
// ===================================================================

$page = isset($_GET['page_no']) ? (int) $_GET['page_no'] : 1;
$limit = 10;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';

$total = getTotalGuru($db, $search); // Kirim $search
$total_pages = $total > 0 ? ceil($total / $limit) : 1;

try {
    $guru_list = getAllGuru($db, $start, $limit, $search); // Kirim $search
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
    $guru_list = false;
}

try {
    $mapel_result_data = $db->query("SELECT id, nama FROM mapel ORDER BY nama");
    $mapel_options = $mapel_result_data->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = (isset($_SESSION['error']) ? $_SESSION['error'] . " | " : "") . "Gagal memuat data mapel: " . $e->getMessage();
    $mapel_options = [];
}

?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['import_skipped_reasons']) && is_array($_SESSION['import_skipped_reasons'])): ?>
    <div class="alert alert-warning">
        <h6>Beberapa baris impor dilewati:</h6>
        <ul style="max-height: 100px; overflow-y: auto;">
            <?php foreach ($_SESSION['import_skipped_reasons'] as $reason): ?>
                <li><?= htmlspecialchars($reason) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['import_skipped_reasons']); ?>
<?php endif; ?>


<div class="container-fluid">
    <div class="card">
        
        <div class="card-header d-md-flex justify-content-between align-items-center">
            <h3 class="card-title mb-2 mb-md-0">Data Guru</h3>
            
            <div class="d-flex flex-wrap gap-2">
                <form method="post" action="modules/export_guru.php" style="display:inline;">
                    <button type="submit" name="export_guru" class="btn btn-success">
                         <i class="fas fa-file-export"></i> 
                         <span class="d-none d-sm-inline">Export Data</span>
                    </button>
                </form>
                 <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import"></i> 
                    <span class="d-none d-sm-inline">Import Data</span> 
                </button>
                
                <a href="?page=guru-form" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 
                    <span class="d-none d-sm-inline">Tambah Guru</span>
                </a>
            </div>
        </div>

        <div class="card-body">
        
            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="page" value="guru">
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control" placeholder="Cari NIP atau Nama Guru (dari awal)..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="d-none d-lg-table-cell">Foto</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th class="d-none d-md-table-cell">Mata Pelajaran</th>
                            <th class="d-none d-lg-table-cell">Jenis Kelamin</th>
                            <th class="d-none d-lg-table-cell">No. Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($guru_list && $guru_list->num_rows > 0): ?>
                            <?php while ($row = $guru_list->fetch_assoc()): ?>
                                <tr>
                                    <td class="d-none d-lg-table-cell align-middle">
                                    <?php 
                                    // --- PERBAIKAN ---
                                    // Gunakan '??' agar jika foto null, dianggap string kosong ''
                                    $nama_foto = $row['foto'] ?? ''; 
                                
                                    $foto_path = "../uploads/guru/" . htmlspecialchars($nama_foto);
                                    $default_foto_path = "../assets/img/default-user.png";
                                    
                                    // Cek path fisik
                                    $check_path = __DIR__ . "/../../uploads/guru/" . $nama_foto;
                                    ?>
                                    
                                    <?php if (!empty($nama_foto) && file_exists($check_path)): ?>
                                        <img src="<?= $foto_path ?>"
                                             alt="Foto <?= htmlspecialchars($row['nama'] ?? '') ?>" class="img-thumbnail"
                                             style="max-width: 50px;">
                                    <?php else: ?>
                                        <img src="<?= $default_foto_path ?>" alt="Default" class="img-thumbnail"
                                             style="max-width: 50px;">
                                    <?php endif; ?>
                                </td>
                                    
                                    <td class="align-middle"><?= htmlspecialchars($row['nip'] ?? '') ?></td>
                                    <td class="align-middle"><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                                    
                                    <td class="d-none d-md-table-cell align-middle"><?= htmlspecialchars($row['nama_mapel'] ?? 'N/A') ?></td>
                                    
                                    <td class="d-none d-lg-table-cell align-middle"><?= ($row['jenis_kelamin'] ?? 'L') == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                    
                                    <td class="d-none d-lg-table-cell align-middle"><?= htmlspecialchars($row['no_telp'] ?? '') ?></td>
                                    
                                    <td class="align-middle">
                                        <a href="?page=guru-form&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteGuru(<?= $row['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data guru</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> 
            
            <?php if ($total > $limit): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center flex-wrap">
                        
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=guru&page_no=1&search=<?= urlencode($search) ?>">First</a>
                        </li>
                        
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? '?page=guru&page_no=' . ($page - 1) . '&search=' . urlencode($search) : '#' ?>">Prev</a>
                        </li>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        if ($start_page > 1) {
                            echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=guru&page_no=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        if ($end_page < $total_pages) {
                             echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                        }
                        ?>

                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $total_pages) ? '?page=guru&page_no=' . ($page + 1) . '&search=' . urlencode($search) : '#' ?>">Next</a>
                        </li>
                        
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=guru&page_no=<?= $total_pages ?>&search=<?= urlencode($search) ?>">Last</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div> 
    </div> 
</div> 

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="?page=guru" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Pilih File (Excel/CSV)</label>
                        <input type="file" name="import_file" id="import_file" class="form-control"
                            accept=".csv,.xlsx,.xls,.ods" required>
                        <div class="form-text mt-2">
                            Format Kolom (baris pertama adalah header, akan dilewati):
                            <ol style="padding-left: 20px; margin-bottom: 0;">
                                <li>NIP (Wajib, akan jadi username & pass default)</li>
                                <li>Nama (Wajib)</li>
                                <li>Jenis Kelamin (L/P atau Laki-laki/Perempuan)</li>
                                <li>Alamat</li>
                                <li>No. Telepon</li>
                                <li>Nama Mata Pelajaran (Harus sama persis dengan di data mapel)</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="import_guru" class="btn btn-success">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    // FUNGSI editGuru() SUDAH DIHAPUS DARI SINI

    function deleteGuru(id) {
        if (confirm('PERINGATAN: Ini akan menghapus data biodata DAN akun login guru. Yakin?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?page=guru'; 
            
            form.innerHTML = `
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="${id}">
                    `;
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>