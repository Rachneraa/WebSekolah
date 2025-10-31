<?php
// Cek akses
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Handler untuk operasi CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'tambah':
                $judul = $_POST['judul'];
                $isi = str_replace('\r\n', "\n", $_POST['isi']); // Perbaiki line breaks
                $tags = $_POST['tags'];
                $penulis = $_SESSION['username'];

                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $target_dir = "../berita/";
                    if (!file_exists($target_dir)) {
                         mkdir($target_dir, 0777, true);
                    }
                    $imageFileType = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                    $gambar = uniqid() . '.' . $imageFileType;
                    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $gambar);
                }

                $query = "INSERT INTO berita (judul, isi, tags, gambar, penulis) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sssss", $judul, $isi, $tags, $gambar, $penulis);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Berita berhasil ditambahkan";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;

            case 'edit':
                $id = (int) $_POST['id'];
                $judul = $_POST['judul'];
                $isi = str_replace(['\r\n', '\r', '\n'], "\n", $_POST['isi']);
                $tags = $_POST['tags'];

                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    // Hapus gambar lama
                    $query = "SELECT gambar FROM berita WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        if ($row['gambar'] && file_exists("../berita/" . $row['gambar'])) {
                            unlink("../berita/" . $row['gambar']);
                        }
                    }

                    // Upload gambar baru
                    $target_dir = "../berita/";
                    $imageFileType = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                    $gambar = uniqid() . '.' . $imageFileType;
                    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $gambar);

                    $query = "UPDATE berita SET judul=?, isi=?, tags=?, gambar=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("ssssi", $judul, $isi, $tags, $gambar, $id);
                } else {
                    $query = "UPDATE berita SET judul=?, isi=?, tags=? WHERE id=?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("sssi", $judul, $isi, $tags, $id);
                }

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Berita berhasil diupdate";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;

            case 'hapus':
                $id = (int) $_POST['id'];

                // Hapus gambar terlebih dahulu
                $query = "SELECT gambar FROM berita WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    if ($row['gambar'] && file_exists("../berita/" . $row['gambar'])) {
                        unlink("../berita/" . $row['gambar']);
                    }
                }

                // Hapus data dari database
                $query = "DELETE FROM berita WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Berita berhasil dihapus";
                } else {
                    $_SESSION['error'] = "Error: " . $stmt->error;
                }
                break;
        }
        
        // === PERBAIKAN PENTING: Redirect setelah POST ===
        header("Location: ?page=berita");
        exit();
    }
}

// Ambil data berita untuk ditampilkan
$query = "SELECT * FROM berita ORDER BY tanggal DESC";
$result = mysqli_query($db, $query); // Menggunakan mysqli_query (sesuai kode asli)
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
        <h5 class="mb-0">Kelola Berita</h5>
        
        <a href="?page=berita-form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Berita
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Tags</th>
                        <th>Tanggal</th>
                        <th>Penulis</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td>
                                <?php
                                if (!empty($row['tags'])) {
                                    $tags = explode(',', $row['tags']);
                                    foreach ($tags as $tag) {
                                        echo '<span class="badge bg-secondary me-1">' . htmlspecialchars(trim($tag)) . '</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['penulis']) ?></td>
                            <td>
                                <?php if ($row['gambar']): ?>
                                    <img src="../berita/<?= $row['gambar'] ?>" alt="Gambar Berita" style="max-height: 50px;">
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada gambar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?page=berita-form&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger" onclick="hapusBerita(<?= $row['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // FUNGSI editBerita() SUDAH DIHAPUS

    function hapusBerita(id) {
        if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            // === PERBAIKAN: Pastikan form action mengarah ke halaman yang benar ===
            form.action = '?page=berita'; 
            form.innerHTML = `
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id" value="${id}">
        `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>