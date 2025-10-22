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
                $judul = mysqli_real_escape_string($db, $_POST['judul']);
                $isi = str_replace('\r\n', "\n", $_POST['isi']); // Perbaiki line breaks
                $isi = mysqli_real_escape_string($db, $isi);
                $tags = mysqli_real_escape_string($db, $_POST['tags']);
                $penulis = $_SESSION['username'];

                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $target_dir = "../berita/";
                    $imageFileType = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
                    $gambar = uniqid() . '.' . $imageFileType;
                    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $gambar);
                }

                $query = "INSERT INTO berita (judul, isi, tags, gambar, penulis) VALUES (?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->bind_param("sssss", $judul, $isi, $tags, $gambar, $penulis);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Berita berhasil ditambahkan</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                break;

            case 'edit':
                $id = (int) $_POST['id'];
                $judul = mysqli_real_escape_string($db, $_POST['judul']);
                // Clean up line breaks before saving
                $isi = str_replace(['\r\n', '\r', '\n'], "\n", $_POST['isi']);
                $isi = mysqli_real_escape_string($db, $isi);
                $tags = mysqli_real_escape_string($db, $_POST['tags']);

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
                    echo "<div class='alert alert-success'>Berita berhasil diupdate</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
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
                    echo "<div class='alert alert-success'>Berita berhasil dihapus</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                }
                break;
        }
    }
}

// Ambil data berita untuk ditampilkan
$query = "SELECT * FROM berita ORDER BY tanggal DESC";
$result = mysqli_query($db, $query);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kelola Berita</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBerita">
            <i class="fas fa-plus"></i> Tambah Berita
        </button>
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
                                <button class="btn btn-sm btn-warning" onclick="editBerita(<?= $row['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
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

<!-- Modal Tambah Berita -->
<div class="modal fade" id="tambahBerita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="tambah">

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Berita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Berita</label>
                        <small class="text-muted d-block mb-2">
                            Gunakan ## untuk membuat sub judul. Contoh:<br>
                            Paragraf pembuka...<br>
                            ## Sub Judul 1<br>
                            Isi sub judul 1...
                        </small>
                        <textarea class="form-control" name="isi" rows="10" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <input type="text" class="form-control" name="tags" placeholder="tag1, tag2, tag3">
                        <small class="text-muted">Pisahkan dengan koma</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
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

<!-- Update Modal Edit Berita -->
<div class="modal fade" id="editBerita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Berita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" id="edit_judul" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Berita</label>
                        <div class="text-muted small mb-2">
                            <strong>Panduan format:</strong><br>
                            1. Tulis paragraf pembuka<br>
                            2. Untuk sub judul, gunakan ## di awal baris<br>
                            3. Tulis konten sub judul di bawahnya<br>
                            <br>
                            Contoh:<br>
                            <pre class="bg-light p-2">
Ini adalah paragraf pembuka.
Bisa lebih dari satu baris.

## Sub Judul Pertama
Ini adalah isi dari sub judul pertama.

## Sub Judul Kedua
Ini adalah isi dari sub judul kedua.</pre>
                        </div>
                        <textarea class="form-control" name="isi" id="edit_isi" rows="10" required
                            style="font-family: monospace;"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <input type="text" class="form-control" name="tags" id="edit_tags"
                            placeholder="tag1, tag2, tag3">
                        <small class="text-muted">Pisahkan dengan koma</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                        <div id="current_image" class="mt-2"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editBerita(id) {
        // Ambil data berita dengan AJAX
        fetch(`get_berita.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_judul').value = data.judul;
                // Clean up line breaks
                document.getElementById('edit_isi').value = data.isi.replace(/\\r\\n/g, '\n');
                document.getElementById('edit_tags').value = data.tags || '';

                // Show current image if exists
                const currentImage = document.getElementById('current_image');
                if (data.gambar) {
                    currentImage.innerHTML = `
                        <img src="../berita/${data.gambar}" 
                             alt="Current image" 
                             style="max-height: 100px;">
                        <p class="small">Gambar saat ini</p>`;
                } else {
                    currentImage.innerHTML = '';
                }

                new bootstrap.Modal(document.getElementById('editBerita')).show();
            });
    }

    function hapusBerita(id) {
        if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
            <input type="hidden" name="action" value="hapus">
            <input type="hidden" name="id" value="${id}">
        `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>