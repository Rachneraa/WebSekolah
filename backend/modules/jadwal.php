<?php
// Cek login & hak akses
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['level'], ['admin', 'guru'])) {
    header('Location: ../index.php');
    exit();
}

// Ambil filter dari URL
$filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
$filter_hari = isset($_GET['hari']) ? $_GET['hari'] : ''; // Filter hari
// Variabel filter_guru dihapus

$redirect_url = "admin.php?page=jadwal&kelas_id=$filter_kelas&hari=" . urlencode($filter_hari);

// --- LOGIKA POST (TAMBAH, EDIT, HAPUS) ---
// (Logika POST tidak berubah, $redirect_url sudah diupdate)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    try {
        switch ($_POST['action']) {
            case 'tambah':
                $kelas_id = intval($_POST['kelas_id']);
                $hari = $_POST['hari'];
                $jam = $_POST['jam'];
                $mapel_id = intval($_POST['mapel_id']);
                $guru_id = intval($_POST['guru_id']);

                $cek_stmt = $db->prepare("SELECT id_jadwal FROM jadwal WHERE kelas_id=? AND hari=? AND jam=?");
                $cek_stmt->bind_param("iss", $kelas_id, $hari, $jam);
                $cek_stmt->execute();
                if ($cek_stmt->get_result()->num_rows > 0) {
                    throw new Exception("Jadwal bentrok pada kelas, hari, dan jam yang sama!");
                }
                
                $stmt = $db->prepare("INSERT INTO jadwal (kelas_id, hari, jam, mapel_id, guru_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issii", $kelas_id, $hari, $jam, $mapel_id, $guru_id);
                $stmt->execute();
                $_SESSION['success'] = "Jadwal berhasil ditambahkan.";
                break;

            case 'edit':
                $id_jadwal = intval($_POST['id_jadwal']);
                $kelas_id = intval($_POST['kelas_id']);
                $hari = $_POST['hari'];
                $jam = $_POST['jam'];
                $mapel_id = intval($_POST['mapel_id']);
                $guru_id = intval($_POST['guru_id']);

                $cek_stmt = $db->prepare("SELECT id_jadwal FROM jadwal WHERE kelas_id=? AND hari=? AND jam=? AND id_jadwal!=?");
                $cek_stmt->bind_param("issi", $kelas_id, $hari, $jam, $id_jadwal);
                $cek_stmt->execute();
                if ($cek_stmt->get_result()->num_rows > 0) {
                    throw new Exception("Jadwal bentrok pada kelas, hari, dan jam yang sama!");
                }
                
                $stmt = $db->prepare("UPDATE jadwal SET kelas_id=?, hari=?, jam=?, mapel_id=?, guru_id=? WHERE id_jadwal=?");
                $stmt->bind_param("issiii", $kelas_id, $hari, $jam, $mapel_id, $guru_id, $id_jadwal);
                $stmt->execute();
                $_SESSION['success'] = "Jadwal berhasil diupdate.";
                break;

            case 'hapus':
                $id_jadwal = intval($_POST['id_jadwal']);
                $stmt = $db->prepare("DELETE FROM jadwal WHERE id_jadwal=?");
                $stmt->bind_param("i", $id_jadwal);
                $stmt->execute();
                $_SESSION['success'] = "Jadwal berhasil dihapus.";
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: $redirect_url");
    exit();
}

// --- LOGIKA GET (TAMPILKAN DATA) ---

// Ambil data untuk filter tombol
$kelas_result = $db->query("SELECT kelas_id, nama FROM kelas ORDER BY nama ASC");
// Query guru_result dihapus
$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

// Query jadwal dengan filter (PREPARED STATEMENT)
$where = [];
$params = [];
$types = '';

if ($filter_kelas) {
    $where[] = "j.kelas_id = ?";
    $params[] = $filter_kelas;
    $types .= 'i';
}
// Filter guru dihapus dari sini
if ($filter_hari) {
    $where[] = "j.hari = ?";
    $params[] = $filter_hari;
    $types .= 's';
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$query = "
    SELECT j.*, k.nama AS nama_kelas, g.nama AS nama_guru, m.nama AS nama_mapel
    FROM jadwal j
    LEFT JOIN kelas k ON j.kelas_id = k.kelas_id
    LEFT JOIN guru g ON j.guru_id = g.id
    LEFT JOIN mapel m ON j.mapel_id = m.id
    $where_sql
    ORDER BY FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'), j.jam
";

$stmt = $db->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$jadwal = $stmt->get_result();
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


<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Jadwal Pelajaran</h3>
        <a href="?page=jadwal-form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Jadwal
        </a>
    </div>
    <div class="card-body">
        
        <div class="mb-2">
            <label class="form-label" style="font-weight: 500;">Filter Kelas:</label>
            <div class="d-flex flex-wrap gap-2">
                <a href="?page=jadwal&hari=<?= urlencode($filter_hari) ?>" 
                   class="btn btn-sm <?= $filter_kelas == 0 ? 'btn-primary' : 'btn-outline-primary' ?>">
                   Semua Kelas
                </a>
                <?php while ($k = $kelas_result->fetch_assoc()): ?>
                <a href="?page=jadwal&kelas_id=<?= $k['kelas_id'] ?>&hari=<?= urlencode($filter_hari) ?>" 
                   class="btn btn-sm <?= $filter_kelas == $k['kelas_id'] ? 'btn-primary' : 'btn-outline-primary' ?>">
                   <?= htmlspecialchars($k['nama']) ?>
                </a>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" style="font-weight: 500;">Filter Hari:</label>
            <div class="d-flex flex-wrap gap-2">
                <a href="?page=jadwal&kelas_id=<?= $filter_kelas ?>" 
                   class="btn btn-sm <?= $filter_hari == '' ? 'btn-primary' : 'btn-outline-primary' ?>">
                   Semua Hari
                </a>
                <?php foreach ($hari_list as $h): ?>
                <a href="?page=jadwal&kelas_id=<?= $filter_kelas ?>&hari=<?= urlencode($h) ?>" 
                   class="btn btn-sm <?= $filter_hari == $h ? 'btn-primary' : 'btn-outline-primary' ?>">
                   <?= htmlspecialchars($h) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($jadwal->num_rows == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada jadwal yang cocok dengan filter.</td>
                        </tr>
                    <?php else:
                        while ($row = $jadwal->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['hari']) ?></td>
                                <td><?= htmlspecialchars($row['jam']) ?></td>
                                <td><?= htmlspecialchars($row['nama_mapel']) ?></td>
                                <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                                <td>
                                    <a href="admin.php?page=jadwal-form&id=<?= $row['id_jadwal'] ?>"
                                       class="btn btn-sm btn-warning">
                                       <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="hapusJadwal(<?= $row['id_jadwal'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function hapusJadwal(id) {
    if (confirm('Anda yakin ingin menghapus jadwal ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        // PERUBAHAN: form.action sudah diupdate otomatis oleh $redirect_url
        form.action = '<?= $redirect_url ?>'; 
        
        form.innerHTML = `
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id_jadwal" value="${id}">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>