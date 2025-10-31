<?php
// Cek login & hak akses (hanya admin)
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// --- LOGIKA AKSI (TAMBAH, EDIT, HAPUS) ---
// Dibuat aman dengan PREPARED STATEMENTS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    $redirect_url = "admin.php?page=mapel"; // Halaman untuk redirect

    try {
        switch ($_POST['action']) {
            case 'tambah':
                $nama = trim($_POST['nama']);
                
                // Cek duplikat dengan prepared statement
                $cek_stmt = $db->prepare("SELECT id FROM mapel WHERE nama = ?");
                $cek_stmt->bind_param("s", $nama);
                $cek_stmt->execute();
                if ($cek_stmt->get_result()->num_rows > 0) {
                    throw new Exception("Nama mata pelajaran '$nama' sudah ada!");
                }
                
                // Insert dengan prepared statement
                $stmt = $db->prepare("INSERT INTO mapel (nama) VALUES (?)");
                $stmt->bind_param("s", $nama);
                $stmt->execute();
                $_SESSION['success'] = "Berhasil menambah mapel!";
                break;

            case 'edit':
                $id = intval($_POST['id']);
                $nama = trim($_POST['nama']);
                
                // Cek duplikat dengan prepared statement
                $cek_stmt = $db->prepare("SELECT id FROM mapel WHERE nama = ? AND id != ?");
                $cek_stmt->bind_param("si", $nama, $id);
                $cek_stmt->execute();
                if ($cek_stmt->get_result()->num_rows > 0) {
                    throw new Exception("Nama mata pelajaran '$nama' sudah ada!");
                }
                
                // Update dengan prepared statement
                $stmt = $db->prepare("UPDATE mapel SET nama = ? WHERE id = ?");
                $stmt->bind_param("si", $nama, $id);
                $stmt->execute();
                $_SESSION['success'] = "Berhasil mengedit mapel!";
                break;

            case 'hapus':
                $id = intval($_POST['id']);
                
                // Delete dengan prepared statement
                $stmt = $db->prepare("DELETE FROM mapel WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $_SESSION['success'] = "Berhasil menghapus mapel!";
                break;
        }
    } catch (Exception $e) {
        // Tangkap error (misal: duplikat) dan kirim sebagai notifikasi
        $_SESSION['error'] = $e->getMessage();
    }
    
    // Redirect kembali ke halaman ini
    header("Location: $redirect_url");
    exit();
}


// --- AMBIL DATA UNTUK DITAMPILKAN ---
$mapel_result = $db->query("SELECT * FROM mapel ORDER BY nama ASC");
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

<div class="container-fluid"> <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Data Mata Pelajaran</h3>
            <a href="?page=mapel-form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Mapel
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Mata Pelajaran</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        if ($mapel_result->num_rows == 0): ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data</td>
                            </tr>
                        <?php else:
                            while ($row = $mapel_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td>
                                        <a href="?page=mapel-form&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="hapusMapel(<?= $row['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile;
                        endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function hapusMapel(id) {
    if (confirm('Anda yakin ingin menghapus mapel ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'admin.php?page=mapel'; // Submit ke halaman ini
        
        form.innerHTML = `
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id" value="${id}">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>