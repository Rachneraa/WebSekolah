<?php
ob_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

require_once '../config/koneksi.php';

// Autoload composer (PhpSpreadsheet) — vendor ada di project root
$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Tambahkan error reporting saat development (hapus/ubah di production)
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
        $where[] = "(s.nama LIKE ? OR s.username LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
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
        throw new Exception("Prepare failed: " . $db->error);
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
        $where[] = "(s.nama LIKE ? OR s.username LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= 'ss';
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
        throw new Exception("Prepare failed: " . $db->error);
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
                    // Hash password
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    // Generate next siswa_id karena kolom tidak auto_increment di DB
                    $res_next = $db->query("SELECT COALESCE(MAX(siswa_id),0)+1 AS next_id FROM siswa");
                    $row_next = $res_next ? $res_next->fetch_assoc() : null;
                    $next_id = $row_next ? (int) $row_next['next_id'] : 1;

                    // Insert dengan menyertakan siswa_id yang di-generate
                    $stmt = $db->prepare("INSERT INTO siswa (siswa_id, nama, username, password, kelas_id, nama_kelas) VALUES (?, ?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $db->error);
                    }
                    $stmt->bind_param(
                        "isssis",
                        $next_id,
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
                header("Location: ?page=siswa");
                exit();
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
                header("Location: ?page=siswa");
                exit();
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
                header("Location: ?page=siswa");
                exit();
                break;

            case 'export':
                // Export ke Excel (XLSX)
                $search = isset($_POST['search']) ? $_POST['search'] : '';
                $kelas_filter = isset($_POST['kelas_filter']) ? $_POST['kelas_filter'] : '';

                $query = "SELECT s.siswa_id, s.nama, s.username, s.kelas_id, COALESCE(k.nama,'') AS nama_kelas
                          FROM siswa s
                          LEFT JOIN kelas k ON s.kelas_id = k.kelas_id";
                $conds = [];
                $params = [];
                $types = '';

                if ($search) {
                    $conds[] = "(s.nama LIKE ? OR s.username LIKE ?)";
                    $params[] = "%$search%";
                    $params[] = "%$search%";
                    $types .= 'ss';
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
                    $_SESSION['error'] = "Export failed: " . $db->error;
                    header("Location: ?page=siswa");
                    exit();
                }
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $res = $stmt->get_result();

                // Pastikan library PhpSpreadsheet tersedia
                if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
                    $_SESSION['error'] = "Silakan jalankan composer require phpoffice/phpspreadsheet";
                    header("Location: ?page=siswa");
                    exit();
                }

                try {
                    // Buat spreadsheet dan header kolom
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle('Siswa');
                    $headers = ['ID', 'Nama', 'Username', 'Kelas'];
                    $sheet->fromArray($headers, null, 'A1');

                    $rowIndex = 2;
                    if ($res && $res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            $sheet->setCellValue('A' . $rowIndex, $row['siswa_id']);
                            $sheet->setCellValue('B' . $rowIndex, $row['nama']);
                            $sheet->setCellValue('C' . $rowIndex, $row['username']);
                            $sheet->setCellValue('D' . $rowIndex, $row['nama_kelas'] ?: '');
                            $rowIndex++;
                        }
                    } else {
                        // Jika data kosong, buat contoh baris
                        $sheet->setCellValue('A2', '');
                        $sheet->setCellValue('B2', 'Data tidak ditemukan');
                        $sheet->setCellValue('C2', '');
                        $sheet->setCellValue('D2', '');
                    }

                    // Auto size kolom (opsional)
                    foreach (range('A', 'D') as $col) {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    }

                    // Nonaktifkan output compression jika aktif (menghindari korup)
                    if (ini_get('zlib.output_compression')) {
                        @ini_set('zlib.output_compression', '0');
                    }

                    // Bersihkan semua output buffer yang mungkin sudah berisi HTML/whitespace
                    while (ob_get_level() > 0) {
                        @ob_end_clean();
                    }

                    // Pastikan tidak ada output tersisa
                    if (headers_sent($file, $line)) {
                        // Jika header sudah terkirim, batal export agar tidak menghasilkan file korup
                        throw new Exception("Tidak dapat mengirim file karena output sudah dikirim sebelumnya ({$file}:{$line}).");
                    }

                    // Header untuk unduh XLSX
                    $filename = 'siswa_export_' . date('Ymd_His') . '.xlsx';
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    header('Cache-Control: max-age=0');
                    header('Pragma: public');

                    // Tulis file ke output
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('php://output');
                    exit();
                } catch (Throwable $e) {
                    // Tangani error saat generate file
                    // Pastikan tidak memunculkan output selain redirect/session
                    $_SESSION['error'] = "Export gagal: " . $e->getMessage();
                    header("Location: ?page=siswa");
                    exit();
                }
                break;

            case 'import':
                // Import CSV
                if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['error'] = "File tidak diunggah atau terjadi kesalahan upload.";
                    header("Location: ?page=siswa");
                    exit();
                }

                $file_tmp = $_FILES['import_file']['tmp_name'];
                // Jika file Excel, gunakan PhpSpreadsheet
                if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    try {
                        $spreadsheet = IOFactory::load($file_tmp);
                        $sheet = $spreadsheet->getActiveSheet();
                        $rows = $sheet->toArray(null, true, true, true);
                    } catch (Exception $e) {
                        $_SESSION['error'] = "Gagal membaca file Excel: " . $e->getMessage();
                        header("Location: ?page=siswa");
                        exit();
                    }
                } else {
                    // Jika PhpSpreadsheet tidak tersedia, fallback: coba baca sebagai CSV
                    $handle = fopen($file_tmp, 'r');
                    if ($handle === false) {
                        $_SESSION['error'] = "Gagal membuka file. Install PhpSpreadsheet untuk dukungan Excel.";
                        header("Location: ?page=siswa");
                        exit();
                    }
                    $rows = [];
                    while (($r = fgetcsv($handle)) !== false) {
                        $rows[] = $r;
                    }
                    fclose($handle);
                }

                $rownum = 0;
                $inserted = 0;
                $updated = 0;
                $skipped = 0;
                $skipped_reasons = []; // kumpulkan alasan dilewati
                $db->begin_transaction();
                try {
                    if (empty($rows)) {
                        throw new Exception("File kosong atau tidak bisa dibaca.");
                    }
                    // Jika rows indexed by numeric (CSV fallback) -> convert to zero-based arrays
                    // Normalize header row (first row)
                    $firstRow = $rows[0];
                    // If PhpSpreadsheet returned associative A,B,C keys, convert to numeric
                    if (is_array($firstRow) && array_keys($firstRow)[0] === 'A') {
                        // convert A.. to numeric
                        $normalized = [];
                        foreach ($rows as $r) {
                            $normalized[] = array_values($r);
                        }
                        $rows = $normalized;
                        $firstRow = $rows[0];
                    }
                    $headmap = array_map('strtolower', $firstRow);

                    // Determine column positions (allow flexible order)
                    $col_pos = [
                        'siswa_id' => array_search('siswa_id', $headmap),
                        'nama' => array_search('nama', $headmap),
                        'username' => array_search('username', $headmap),
                        'password' => array_search('password', $headmap),
                        'kelas_id' => array_search('kelas_id', $headmap),
                        'nama_kelas' => array_search('nama_kelas', $headmap),
                    ];

                    // Process data rows (start from index 1)
                    for ($i = 1; $i < count($rows); $i++) {
                        $rownum++;
                        $data = $rows[$i];
                        $nama = isset($col_pos['nama']) && $col_pos['nama'] !== false ? (isset($data[$col_pos['nama']]) ? $data[$col_pos['nama']] : null) : null;
                        $username = isset($col_pos['username']) && $col_pos['username'] !== false ? (isset($data[$col_pos['username']]) ? $data[$col_pos['username']] : null) : null;
                        $raw_password = isset($col_pos['password']) && $col_pos['password'] !== false ? (isset($data[$col_pos['password']]) ? $data[$col_pos['password']] : null) : null;
                        $kelas_id = isset($col_pos['kelas_id']) && $col_pos['kelas_id'] !== false ? (isset($data[$col_pos['kelas_id']]) ? $data[$col_pos['kelas_id']] : null) : null;
                        $nama_kelas = isset($col_pos['nama_kelas']) && $col_pos['nama_kelas'] !== false ? (isset($data[$col_pos['nama_kelas']]) ? $data[$col_pos['nama_kelas']] : '') : '';

                        if (empty($username) || empty($nama)) {
                            $skipped++;
                            // spesisfikasi alasan berdasarkan kolom yang kosong
                            if (empty($username) && empty($nama)) {
                                $skipped_reasons[] = "Baris $rownum dilewati karena kolom nama dan username kosong.";
                            } elseif (empty($username)) {
                                $skipped_reasons[] = "Baris $rownum (Nama: " . ($nama ?: '[kosong]') . ") dilewati karena kolom username kosong.";
                            } else {
                                $skipped_reasons[] = "Baris $rownum (Username: " . ($username ?: '[kosong]') . ") dilewati karena kolom nama kosong.";
                            }
                            continue;
                        }

                        // Hash password if provided, otherwise set a random password (or skip)
                        $password_hash = $raw_password ? password_hash($raw_password, PASSWORD_DEFAULT) : password_hash(bin2hex(random_bytes(4)), PASSWORD_DEFAULT);

                        // Check existing by username
                        $check = $db->prepare("SELECT siswa_id FROM siswa WHERE username = ?");
                        $check->bind_param("s", $username);
                        $check->execute();
                        $res_check = $check->get_result();

                        if ($res_check && $res_check->num_rows > 0) {
                            // Update
                            $existing = $res_check->fetch_assoc();
                            $update = $db->prepare("UPDATE siswa SET nama = ?, password = ?, kelas_id = ?, nama_kelas = ? WHERE siswa_id = ?");
                            $update->bind_param("ssisi", $nama, $password_hash, $kelas_id, $nama_kelas, $existing['siswa_id']);
                            $update->execute();
                            $updated++;
                        } else {
                            // Insert (generate siswa_id karena tidak auto_increment)
                            $res_next = $db->query("SELECT COALESCE(MAX(siswa_id),0)+1 AS next_id FROM siswa");
                            $row_next = $res_next ? $res_next->fetch_assoc() : null;
                            $next_id = $row_next ? (int) $row_next['next_id'] : 1;

                            $insert = $db->prepare("INSERT INTO siswa (siswa_id, nama, username, password, kelas_id, nama_kelas) VALUES (?, ?, ?, ?, ?, ?)");
                            if (!$insert) {
                                throw new Exception("Prepare failed: " . $db->error);
                            }
                            $insert->bind_param("isssis", $next_id, $nama, $username, $password_hash, $kelas_id, $nama_kelas);
                            $insert->execute();
                            $inserted++;
                        }
                    }

                    $db->commit();
                    $_SESSION['success'] = "Import selesai. Inserted: $inserted, Updated: $updated, Skipped: $skipped";
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

<?php if (isset($_SESSION['import_skipped_reasons']) && is_array($_SESSION['import_skipped_reasons'])): ?>
    <div class="card mt-2">
        <div class="card-body">
            <h6>Detail baris yang dilewati:</h6>
            <ul>
                <?php foreach ($_SESSION['import_skipped_reasons'] as $reason): ?>
                    <li><?= htmlspecialchars($reason) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php unset($_SESSION['import_skipped_reasons']); ?>
<?php endif; ?>

<!-- Main Content -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title float-start">Data Siswa</h3>
            <div class="float-end d-flex gap-2">
                <!-- Export form (keadaan saat ini: kirim filter agar export sesuai) -->
                <form method="post" style="display:inline;">
                    <input type="hidden" name="action" value="export">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="kelas_filter" value="<?= htmlspecialchars($kelas_filter) ?>">
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </button>
                </form>

                <!-- Import form -->
                <form method="post" enctype="multipart/form-data" style="display:inline;">
                    <input type="hidden" name="action" value="import">
                    <input type="file" name="import_file" accept=".csv" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-file-import"></i> Import CSV
                    </button>
                </form>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
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
                            <option value="<?= $kelas['kelas_id'] ?>" <?= (isset($_GET['kelas_filter']) && $_GET['kelas_filter'] == $kelas['kelas_id']) ? 'selected' : '' ?>>
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
                        if ($siswa_list && $siswa_list->num_rows > 0):
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
                            href="?page=siswa&page_no=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>">Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=siswa&page_no=<?= $i ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=siswa&page_no=<?= min($total_pages, $page + 1) ?>&search=<?= urlencode($search) ?>&kelas_filter=<?= urlencode($kelas_filter) ?>">Next</a>
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
                                <option value="<?= $kelas['kelas_id'] ?>" data-nama="<?= htmlspecialchars($kelas['nama']) ?>">
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
                                <option value="<?= $kelas['kelas_id'] ?>" data-nama="<?= htmlspecialchars($kelas['nama']) ?>">
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
        namaKelasInput.value = selectedOption ? (selectedOption.dataset.nama || '') : '';
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
<?php ob_end_flush(); ?>