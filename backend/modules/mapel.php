<?php

require_once dirname(__DIR__) . '/../config/koneksi.php';

// Cek login & hak akses (hanya admin)
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// Proses tambah mapel
if (isset($_POST['tambah'])) {
    $nama = trim(mysqli_real_escape_string($db, $_POST['nama']));
    $cek = mysqli_query($db, "SELECT * FROM mapel WHERE nama='$nama'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Nama mata pelajaran sudah ada!";
    } else {
        mysqli_query($db, "INSERT INTO mapel (nama) VALUES ('$nama')");
        $success = "Berhasil menambah mapel!";
        // Tidak perlu header redirect
    }
}

// Proses edit mapel
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $nama = trim(mysqli_real_escape_string($db, $_POST['nama']));
    $cek = mysqli_query($db, "SELECT * FROM mapel WHERE nama='$nama' AND id!=$id");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Nama mata pelajaran sudah ada!";
    } else {
        mysqli_query($db, "UPDATE mapel SET nama='$nama' WHERE id=$id");
        $success = "Berhasil mengedit mapel!";
        // Tidak perlu header redirect
        $edit_data = null; // Reset form
    }
}

// Proses hapus mapel
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($db, "DELETE FROM mapel WHERE id=$id");
    $success = "Berhasil menghapus mapel!";
    // Tidak perlu header redirect
}

// Ambil data mapel untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = mysqli_query($db, "SELECT * FROM mapel WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($res);
}

// Ambil semua mapel
$mapel = mysqli_query($db, "SELECT * FROM mapel ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Mata Pelajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h3>Data Mata Pelajaran</h3>
        <div class="card mb-3">
            <div class="card-header"><?= $edit_data ? 'Edit Mapel' : 'Tambah Mapel' ?></div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <form method="post" class="row g-2">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>
                    <div class="col-md-8">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Mata Pelajaran"
                            value="<?= $edit_data ? htmlspecialchars($edit_data['nama']) : '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary">
                            <?= $edit_data ? 'Update' : 'Tambah' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Mata Pelajaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = mysqli_fetch_assoc($mapel)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td>
                            <a href="?page=mapel&edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="?page=mapel&hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Hapus mapel ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile;
                if ($no == 1): ?>
                    <tr>
                        <td colspan="3" class="text-center">Belum ada data</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>