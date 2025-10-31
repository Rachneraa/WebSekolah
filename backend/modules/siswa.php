<?php
// ob_start() SUDAH DIHAPUS

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../config/koneksi.php';

// Autoload composer (PhpSpreadsheet)
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

// error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fungsi CRUD
function getAllSiswa($db, $start = 0, $limit = 10, $search = '', $kelas_filter = '')
{
    $where = [];
    $params = [];
    $types = '';

    if ($search) {
        // === PERBAIKAN: s.nisn diubah menjadi s.nis ===
        $where[] = "(s.nama LIKE ? OR s.username LIKE ? OR s.nis LIKE ? OR s.email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ssss';
    }
    if ($kelas_filter !== '' && $kelas_filter !== null) {
        $where[] = "s.kelas_id = ?";
        $params[] = $kelas_filter;
        $types .= 'i';
    }

    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $query = "SELECT s.*, k.nama as nama_kelas 
              FROM siswa s 
              LEFT JOIN kelas k ON s.kelas_id = k.kelas_id 
              $where_sql
              ORDER BY k.nama, s.nama 
              LIMIT ?, ?";
    $params[] = $start;
    $params[] = $limit;
    $types .= 'ii';

    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . (string)$db->error);
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function getTotalSiswa($db, $search = '', $kelas_filter = '')
{
    $where = [];
    $params = [];
    $types = '';

    if ($search) {
        // === PERBAIKAN: s.nisn diubah menjadi s.nis ===
        $where[] = "(s.nama LIKE ? OR s.username LIKE ? OR s.nis LIKE ? OR s.email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ssss';
    }
    if ($kelas_filter !== '' && $kelas_filter !== null) {
        $where[] = "s.kelas_id = ?";
        $params[] = $kelas_filter;
        $types .= 'i';
    }

    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $query = "SELECT COUNT(*) as total FROM siswa s 
              LEFT JOIN kelas k ON s.kelas_id = k.kelas_id 
              $where_sql";

    $stmt = $db->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . (string)$db->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return (int) $row['total'];
}

// Handle form submissions (add/edit/delete) + import/export
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                try {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $res_next = $db->query("SELECT COALESCE(MAX(siswa_id),0)+1 AS next_id FROM siswa");
                    $row_next = $res_next ? $res_next->fetch_assoc() : null;
                    $next_id = $row_next ? (int) $row_next['next_id'] : 1;
                    
                    // === PERBAIKAN: 'nisn' diubah menjadi 'nis' ===
                    $stmt = $db->prepare("INSERT INTO siswa (siswa_id, nama, nis, email, telepon, username, password, kelas_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . (string)$db->error);
                    }
                    
                    // === PERBAIKAN: $_POST['nisn'] diubah menjadi $_POST['nis'] ===
                    $stmt->bind_param(
                        "issssssi", 
                        $next_id,
                        $_POST['nama'],
                        $_POST['nis'],      // diubah dari nisn
                        $_POST['email'],
                        $_POST['telepon'],
                        $_POST['username'],
                        $password,
                        $_POST['kelas_id']
                    );
                    $stmt->execute();
                    $_SESSION['success'] = "Data siswa berhasil ditambahkan";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                header("Location: ?page=siswa");
                exit();
                break;

            case 'edit':
                try {
                    // === PERBAIKAN: 'nisn' diubah menjadi 'nis' ===
                    $nama = $_POST['nama'];
                    $nis = $_POST['nis']; // diubah dari nisn
                    $email = $_POST['email'];
                    $telepon = $_POST['telepon'];
                    $username = $_POST['username'];
                    $kelas_id = $_POST['kelas_id'];
                    $id = $_POST['id'];

                    if (!empty($_POST['password'])) {
                        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        
                        $update_query = "UPDATE siswa SET nama=?, nis=?, email=?, telepon=?, username=?, password=?, kelas_id=? WHERE siswa_id=?";
                        $stmt = $db->prepare($update_query);
                        $stmt->bind_param(
                            "ssssssii", 
                            $nama,
                            $nis, // diubah dari nisn
                            $email,
                            $telepon,
                            $username,
                            $password,
                            $kelas_id,
                            $id
                        );
                    } else {
                        $update_query = "UPDATE siswa SET nama=?, nis=?, email=?, telepon=?, username=?, kelas_id=? WHERE siswa_id=?";
                        $stmt = $db->prepare($update_query);
                        $stmt->bind_param(
                            "sssssii", 
                            $nama,
                            $nis, // diubah dari nisn
                            $email,
                            $telepon,
                            $username,
                            $kelas_id,
                            $id
                        );
                    }
                    $stmt->execute();
                    $_SESSION['success'] = "Data siswa berhasil diperbarui";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                header("Location: ?page=siswa");
                exit();
                break;

            case 'delete':
                // (Tidak ada perubahan di sini)
                try {
                    $delete_query = "DELETE FROM siswa WHERE siswa_id = ?";
                    $stmt = $db->prepare($delete_query);
                    $stmt->bind_param("i", $_POST['id']);
                    $stmt->execute();
                    $_SESSION['success'] = "Data siswa berhasil dihapus";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                header("Location: ?page=siswa");
                exit();
                break;

            case 'export':
                $search = isset($_POST['search']) ? $_POST['search'] : '';
                $kelas_filter = isset($_POST['kelas_filter']) ? $_POST['kelas_filter'] : '';
                
                // === PERBAIKAN: s.nisn diubah menjadi s.nis ===
                $query = "SELECT s.siswa_id, s.nama, s.nis, s.email, s.telepon, s.username, s.kelas_id, COALESCE(k.nama,'') AS nama_kelas
                          FROM siswa s
                          LEFT JOIN kelas k ON s.kelas_id = k.kelas_id";
                $conds = [];
                $params = [];
                $types = '';
                if ($search) {
                    // === PERBAIKAN: s.nisn diubah menjadi s.nis ===
                    $conds[] = "(s.nama LIKE ? OR s.username LIKE ? OR s.nis LIKE ? OR s.email LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $types .= 'ssss';
                }
                if ($kelas_filter !== '' && $kelas_filter !== null) {
                    $conds[] = "s.kelas_id = ?";
                    $params[] = $kelas_filter;
                    $types .= 'i';
                }
                if ($conds) {
                    $query .= " WHERE " . implode(" AND ", $conds);
                }
                $query .= " ORDER BY k.nama, s.nama";
                $stmt = $db->prepare($query);
                if ($stmt === false) {
                    $_SESSION['error'] = "Export failed: " . (string)$db->error;
                    header("Location: ?page=siswa");
                    exit();
                }
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $res = $stmt->get_result();
                if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                    $_SESSION['error'] = "Silakan jalankan composer require phpoffice/phpspreadsheet";
                    header("Location: ?page=siswa");
                    exit();
                }
                try {
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle('Siswa');
                    
                    // === PERBAIKAN: 'NISN' diubah menjadi 'NIS' ===
                    $headers = ['ID', 'Nama', 'NIS', 'Email', 'telepon', 'Username', 'Kelas'];
                    $sheet->fromArray($headers, null, 'A1');
                    $rowIndex = 2;
                    if ($res && $res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            // === PERBAIKAN: 'nisn' diubah menjadi 'nis' ===
                            $sheet->setCellValue('A' . $rowIndex, $row['siswa_id']);
                            $sheet->setCellValue('B' . $rowIndex, $row['nama']);
                            $sheet->setCellValue('C' . $rowIndex, $row['nis']);
                            $sheet->setCellValue('D' . $rowIndex, $row['email']);
                            $sheet->setCellValue('E' . $rowIndex, $row['telepon']);
                            $sheet->setCellValue('F' . $rowIndex, $row['username']);
                            $sheet->setCellValue('G' . $rowIndex, $row['nama_kelas'] ?: '');
                            $rowIndex++;
                        }
                    } else {
                        $sheet->setCellValue('B2', 'Data tidak ditemukan');
                    }
                    
                    foreach (range('A', 'G') as $col) {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    }
                    if (ini_get('zlib.output_compression')) {
                        @ini_set('zlib.output_compression', '0');
                    }
                    while (ob_get_level() > 0) {
                        @ob_end_clean();
                    }
                    if (headers_sent($file, $line)) {
                        throw new Exception("Tidak dapat mengirim file karena output sudah dikirim ({$file}:{$line}).");
                    }
                    $filename = 'siswa_export_' . date('Ymd_His') . '.xlsx';
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');
                    header('Pragma: public');
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('php://output');
                    exit();
                } catch (Throwable $e) {
                    $_SESSION['error'] = "Export gagal: " . $e->getMessage();
                    header("Location: ?page=siswa");
                    exit();
                }
                break;

            case 'import':
                // (Kode import sudah ada di file, tinggal disesuaikan)
                if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error'] = "File tidak diunggah atau terjadi kesalahan upload.";
                    header("Location: ?page=siswa");
                    exit();
                }
                $file_tmp = $_FILES['import_file']['tmp_name'];
                if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    try {
                        $spreadsheet = IOFactory::load($file_tmp);
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray(null, true, true, true);
                    } catch (Exception $e) {
                        $_SESSION['error'] = "Gagal membaca file Excel/CSV: " . $e->getMessage();
                        header("Location: ?page=siswa");
                        exit();
                    }
                } else {
                        $_SESSION['error'] = "Library PhpSpreadsheet tidak ditemukan.";
                        header("Location: ?page=siswa");
                        exit();
                }
                $rownum = 0;
                $inserted = 0;
                $updated = 0;
                $skipped = 0;
                $skipped_reasons = []; 
                $db->begin_transaction();
                try {
                    if (empty($rows)) {
                        throw new Exception("File kosong atau tidak bisa dibaca.");
                    }
                    $kelas_map = [];
                    $res_kelas = $db->query("SELECT kelas_id, nama FROM kelas");
                    if ($res_kelas) {
                        while ($k_row = $res_kelas->fetch_assoc()) {
                            $clean_nama = strtolower(trim($k_row['nama']));
                            $kelas_map[$clean_nama] = (int) $k_row['kelas_id'];
                        }
                    }
                    $firstRow = reset($rows);
                    if (is_array($firstRow) && isset(array_keys($firstRow)[0]) && array_keys($firstRow)[0] === 'A') {
                        $normalized = [];
                        foreach ($rows as $r) {
                            $normalized[] = array_values($r);
                        }
                        $rows = $normalized;
                        $firstRow = reset($rows); 
                    }
                    if (!is_array($firstRow)) {
                            throw new Exception("Format header file tidak valid.");
                    }
                    $headmap = array_map('strtolower', $firstRow);
                    
                    // === PERBAIKAN: 'nisn' diubah menjadi 'nis' ===
                    $col_pos = [
                        'nama' => array_search('nama', $headmap),
                        'nis' => array_search('nis', $headmap),       // diubah dari nisn
                        'email' => array_search('email', $headmap),
                        'telepon' => array_search('telepon', $headmap),
                        'username' => array_search('username', $headmap),
                        'password' => array_search('password', $headmap),
                        'kelas' => array_search('kelas', $headmap),
                    ];
                    
                    if ($col_pos['nama'] === false || $col_pos['username'] === false || $col_pos['kelas'] === false || $col_pos['nis'] === false) {
                            throw new Exception("Format header file salah. Pastikan ada kolom 'nama', 'nis', 'username', dan 'kelas'.");
                    }
                    $total_rows = count($rows);
                    for ($i = 1; $i < $total_rows; $i++) {
                        $rownum++;
                        $data = $rows[$i];
                        if (!is_array($data)) continue;
                        
                        $nama = (isset($col_pos['nama']) && $col_pos['nama'] !== false && isset($data[$col_pos['nama']])) ? $data[$col_pos['nama']] : null;
                        $nis = (isset($col_pos['nis']) && $col_pos['nis'] !== false && isset($data[$col_pos['nis']])) ? $data[$col_pos['nis']] : null; // diubah dari nisn
                        $email = (isset($col_pos['email']) && $col_pos['email'] !== false && isset($data[$col_pos['email']])) ? $data[$col_pos['email']] : null;
                        $telepon = (isset($col_pos['telepon']) && $col_pos['telepon'] !== false && isset($data[$col_pos['telepon']])) ? $data[$col_pos['telepon']] : null;
                        $username = (isset($col_pos['username']) && $col_pos['username'] !== false && isset($data[$col_pos['username']])) ? $data[$col_pos['username']] : null;
                        $raw_password = (isset($col_pos['password']) && $col_pos['password'] !== false && isset($data[$col_pos['password']])) ? $data[$col_pos['password']] : null;
                        $kelas_id = null;
                        $nama_kelas_excel = (isset($col_pos['kelas']) && $col_pos['kelas'] !== false && isset($data[$col_pos['kelas']])) ? $data[$col_pos['kelas']] : null;
                        
                        if ($nama_kelas_excel) {
                            $clean_nama_key = strtolower(trim($nama_kelas_excel));
                            if (isset($kelas_map[$clean_nama_key])) {
                                $kelas_id = $kelas_map[$clean_nama_key];
                            } else {
                                $skipped_reasons[] = "Baris $rownum (User: $username): Nama kelas '$nama_kelas_excel' tidak ditemukan. Dibiarkan kosong (NULL).";
                            }
                        }
                        
                        if (empty($username) || empty($nama) || empty($nis)) { // diubah dari nisn
                            $skipped++;
                            if (empty($username) && empty($nama) && empty($nis)) { // diubah dari nisn
                                $skipped_reasons[] = "Baris $rownum dilewati (nama, nis, dan username kosong).";
                            } elseif (empty($username)) {
                                $skipped_reasons[] = "Baris $rownum (Nama: $nama) dilewati (username kosong).";
                            } elseif (empty($nis)) { // diubah dari nisn
                                $skipped_reasons[] = "Baris $rownum (User: $username) dilewati (nis kosong).";
                            } else {
                                $skipped_reasons[] = "Baris $rownum (User: $username) dilewati (nama kosong).";
                            }
                            continue;
                        }
                        
                        $password_hash = $raw_password ? password_hash($raw_password, PASSWORD_DEFAULT) : password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);
                        
                        $check = $db->prepare("SELECT siswa_id FROM siswa WHERE username = ?");
                        $check->bind_param("s", $username);
                        $check->execute();
                        $res_check = $check->get_result();
                        if ($res_check && $res_check->num_rows > 0) {
                            $existing = $res_check->fetch_assoc();
                            
                            // === PERBAIKAN: 'nisn' diubah menjadi 'nis' ===
                            $update = $db->prepare("UPDATE siswa SET nama = ?, nis = ?, email = ?, telepon = ?, password = ?, kelas_id = ? WHERE siswa_id = ?");
                            $update->bind_param("sssssii", $nama, $nis, $email, $telepon, $password_hash, $kelas_id, $existing['siswa_id']);
                            $update->execute();
                            $updated++;
                        } else {
                            $res_next = $db->query("SELECT COALESCE(MAX(siswa_id),0)+1 AS next_id FROM siswa");
                            $row_next = $res_next ? $res_next->fetch_assoc() : null;
                            $next_id = $row_next ? (int) $row_next['next_id'] : 1;
                            
                            // === PERBAIKAN: 'nisn' diubah menjadi 'nis' ===
                            $insert = $db->prepare("INSERT INTO siswa (siswa_id, nama, nis, email, telepon, username, password, kelas_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            if (!$insert) {
                                throw new Exception("Prepare failed: " . (string)$db->error);
                            }
                            $insert->bind_param("issssssi", $next_id, $nama, $nis, $email, $telepon, $username, $password_hash, $kelas_id);
                            $insert->execute();
                            $inserted++;
                        }
                    }
                    $db->commit();
                    $_SESSION['success'] = "Import selesai. Ditambahkan: $inserted, Diperbarui: $updated, Dilewati: $skipped";
                    if (!empty($skipped_reasons)) {
                        $_SESSION['import_skipped_reasons'] = $skipped_reasons;
                    }
                } catch (Exception $e) {
                    $db->rollback();
                    $_SESSION['error'] = "Import gagal: " . $e->getMessage();
                }

                header("Location: ?page=siswa");
                exit();
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
$total_pages = $total > 0 ? ceil($total / $limit) : 1;

$siswa_list = getAllSiswa($db, $start, $limit, $search, $kelas_filter);

// Get kelas list for dropdown
$kelas_query = "SELECT * FROM kelas ORDER BY nama";
$kelas_result = $db->query($kelas_query);
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) // Selalu escape output ?>
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
    <div class="card mt-2">
        <div class="card-body">
            <h6>Detail baris yang dilewati/bermasalah:</h6>
            <ul style="max-height: 150px; overflow-y: auto; padding-left: 20px;">
                <?php foreach ($_SESSION['import_skipped_reasons'] as $reason): ?>
                    <li><?= htmlspecialchars($reason) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php unset($_SESSION['import_skipped_reasons']); ?>
<?php endif; ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                <h3 class="card-title mb-2 mb-md-0">Data Siswa</h3>
                
                <div class="d-flex flex-wrap gap-2">
                    
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="export">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <input type="hidden" name="kelas_filter" value="<?= htmlspecialchars($kelas_filter) ?>">
                        <button class="btn btn-success" type="submit">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </form>

                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import"></i> Import File
                    </button>
                    
                    <a href="?page=siswa-form" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            
            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="page" value="siswa">
                
                <input type="hidden" name="kelas_filter" value="<?= htmlspecialchars($kelas_filter) ?>">
                
                <div class="col-md-9">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, username, nis, atau email..."
                        value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>

            <div class="mb-3">
                <label class="form-label" style="font-weight: 500;">Filter per Kelas:</label>
                <div class="d-flex flex-wrap gap-2">
                    
                    <?php
                    // 1. Tombol "Semua Kelas"
                    $all_active_class = (empty($kelas_filter)) ? 'btn-primary' : 'btn-outline-primary';
                    ?>
                    <a href="?page=siswa&search=<?= urlencode($search) ?>&kelas_filter=" 
                       class="btn btn-sm <?= $all_active_class ?>">
                       Semua Kelas
                    </a>

                    <?php
                    // 2. Loop untuk tombol kelas lainnya
                    $kelas_result->data_seek(0); // Reset query kelas
                    while ($kelas = $kelas_result->fetch_assoc()):
                        $kelas_id = $kelas['kelas_id'];
                        $btn_active_class = ($kelas_filter == $kelas_id) ? 'btn-primary' : 'btn-outline-primary';
                    ?>
                        <a href="?page=siswa&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_id) ?>" 
                           class="btn btn-sm <?= $btn_active_class ?>">
                           <?= htmlspecialchars($kelas['nama']) ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th>Email / No. Telp</th>
                            <th>Username</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($siswa_list && $siswa_list->num_rows > 0):
                            $no = $start + 1;
                            while ($row = $siswa_list->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['nis'] ?? '') ?></td>
                                    <td>
                                        <?= htmlspecialchars($row['email'] ?? '') ?>
                                        <?php if (!empty($row['email']) && !empty($row['telepon'])) echo "<br>"; ?>
                                        <?= htmlspecialchars($row['telepon'] ?? '') ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['username'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['nama_kelas'] ?? '') ?></td>
                                    <td>
                                        <a href="?page=siswa-form&id=<?= $row['siswa_id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
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
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <nav>
                <ul class="pagination justify-content-center">
                    </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="?page=siswa" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file_input" class="form-label">Pilih File (Excel/CSV)</label>
                        <input type="file" name="import_file" id="import_file_input" class="form-control"
                            accept=".csv,.xls,.xlsx,.ods" required>
                        <div class="form-text">
                            Format Kolom (Baris pertama harus header):
                            <ul style="padding-left: 20px; margin-bottom: 0;">
                                <li><strong>nama</strong> (Wajib)</li>
                                <li><strong>nis</strong> (Wajib)</li>
                                <li><strong>username</strong> (Wajib, akan di-update jika ada)</li>
                                <li><strong>kelas</strong> (Wajib, nama harus persis, cth: '12 RPL 1')</li>
                                <li><strong>email</strong> (Opsional)</li>
                                <li><strong>no_telp</strong> (Opsional)</li>
                                <li><strong>password</strong> (Opsional, jika kosong, password lama/random)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
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
<?php // ob_end_flush() SUDAH DIHAPUS ?>