<?php

// session_start(); // Sudah dipanggil di admin.php

require_once dirname(__DIR__) . '/../config/koneksi.php'; // Pastikan path benar

// Cek login & hak akses
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['level'], ['admin', 'guru'])) {
    header('Location: ../index.php');
    exit();
}

// Ambil data kelas & guru untuk dropdown
$kelas = mysqli_query($db, "SELECT * FROM kelas ORDER BY nama ASC");
$guru = mysqli_query($db, "SELECT * FROM guru ORDER BY nama ASC");
$mapel = mysqli_query($db, "SELECT * FROM mapel ORDER BY nama ASC"); // Tambahkan ini

// Filter
$filter_kelas = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
$filter_guru = isset($_GET['guru_id']) ? intval($_GET['guru_id']) : 0;

// Query jadwal dengan filter
$where = [];
if ($filter_kelas)
    $where[] = "j.kelas_id = $filter_kelas";
if ($filter_guru)
    $where[] = "j.guru_id = $filter_guru";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$jadwal = mysqli_query($db, "
    SELECT j.*, k.nama AS nama_kelas, g.nama AS nama_guru
    FROM jadwal j
    JOIN kelas k ON j.kelas_id = k.id
    JOIN guru g ON j.guru_id = g.id
    $where_sql
    ORDER BY j.hari, j.jam
");

// Proses tambah jadwal
if (isset($_POST['tambah'])) {
    $kelas_id = intval($_POST['kelas_id']);
    $hari = mysqli_real_escape_string($db, $_POST['hari']);
    $jam = mysqli_real_escape_string($db, $_POST['jam']);
    $mapel = mysqli_real_escape_string($db, $_POST['mapel']);
    $guru_id = intval($_POST['guru_id']);

    // Cek bentrok
    $cek = mysqli_query($db, "SELECT * FROM jadwal WHERE kelas_id=$kelas_id AND hari='$hari' AND jam='$jam'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Jadwal bentrok pada kelas, hari, dan jam yang sama!";
    } else {
        mysqli_query($db, "INSERT INTO jadwal (kelas_id, hari, jam, mapel, guru_id) VALUES ($kelas_id, '$hari', '$jam', '$mapel', $guru_id)");
        header("Location: jadwal.php?kelas_id=$kelas_id&guru_id=$guru_id");
        exit();
    }
}

// Proses hapus jadwal
if (isset($_GET['hapus'])) {
    $id_jadwal = intval($_GET['hapus']);
    mysqli_query($db, "DELETE FROM jadwal WHERE id_jadwal=$id_jadwal");
    header("Location: jadwal.php?kelas_id=$filter_kelas&guru_id=$filter_guru");
    exit();
}

// Proses edit jadwal
if (isset($_POST['edit'])) {
    $id_jadwal = intval($_POST['id_jadwal']);
    $kelas_id = intval($_POST['kelas_id']);
    $hari = mysqli_real_escape_string($db, $_POST['hari']);
    $jam = mysqli_real_escape_string($db, $_POST['jam']);
    $mapel = mysqli_real_escape_string($db, $_POST['mapel']);
    $guru_id = intval($_POST['guru_id']);

    // Cek bentrok (kecuali id_jadwal yang diedit)
    $cek = mysqli_query($db, "SELECT * FROM jadwal WHERE kelas_id=$kelas_id AND hari='$hari' AND jam='$jam' AND id_jadwal!=$id_jadwal");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Jadwal bentrok pada kelas, hari, dan jam yang sama!";
    } else {
        mysqli_query($db, "UPDATE jadwal SET kelas_id=$kelas_id, hari='$hari', jam='$jam', mapel='$mapel', guru_id=$guru_id WHERE id_jadwal=$id_jadwal");
        header("Location: jadwal.php?kelas_id=$kelas_id&guru_id=$guru_id");
        exit();
    }
}

// Ambil data jadwal untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_jadwal = intval($_GET['edit']);
    $res = mysqli_query($db, "SELECT * FROM jadwal WHERE id_jadwal=$id_jadwal");
    $edit_data = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelajaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h3>Jadwal Pelajaran</h3>
        <!-- Filter -->
        <form method="get" class="row g-2 mb-3">
            <div class="col-md-4">
                <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Pilih Kelas --</option>
                    <?php while ($k = mysqli_fetch_assoc($kelas)): ?>
                        <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <select name="guru_id" class="form-select" onchange="this.form.submit()">
                    <option value="0">-- Pilih Guru --</option>
                    <?php mysqli_data_seek($guru, 0);
                    while ($g = mysqli_fetch_assoc($guru)): ?>
                        <option value="<?= $g['id'] ?>" <?= $filter_guru == $g['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['nama']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>

        <!-- Form tambah/edit jadwal -->
        <div class="card mb-3">
            <div class="card-header"><?= $edit_data ? 'Edit Jadwal' : 'Tambah Jadwal' ?></div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="post" class="row g-2">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_jadwal" value="<?= $edit_data['id_jadwal'] ?>">
                    <?php endif; ?>
                    <div class="col-md-2">
                        <select name="kelas_id" class="form-select" required>
                            <option value="">Kelas</option>
                            <?php mysqli_data_seek($kelas, 0);
                            while ($k = mysqli_fetch_assoc($kelas)): ?>
                                <option value="<?= $k['id'] ?>" <?= ($edit_data && $edit_data['kelas_id'] == $k['id']) || ($filter_kelas == $k['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="hari" class="form-select" required>
                            <?php
                            $hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            foreach ($hari_list as $h):
                                ?>
                                <option value="<?= $h ?>" <?= ($edit_data && $edit_data['hari'] == $h) ? 'selected' : '' ?>>
                                    <?= $h ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="jam" class="form-control" placeholder="Jam (ex: 07:00-08:00)"
                            value="<?= $edit_data ? $edit_data['jam'] : '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <select name="mapel" class="form-select" required>
                            <option value="">Mata Pelajaran</option>
                            <?php mysqli_data_seek($mapel, 0);
                            while ($m = mysqli_fetch_assoc($mapel)): ?>
                                <option value="<?= htmlspecialchars($m['nama']) ?>"
                                    <?= ($edit_data && $edit_data['mapel'] == $m['nama']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="guru_id" class="form-select" required>
                            <option value="">Guru</option>
                            <?php mysqli_data_seek($guru, 0);
                            while ($g = mysqli_fetch_assoc($guru)): ?>
                                <option value="<?= $g['id'] ?>" <?= ($edit_data && $edit_data['guru_id'] == $g['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['nama']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="<?= $edit_data ? 'edit' : 'tambah' ?>" class="btn btn-primary">
                            <?= $edit_data ? 'Update' : 'Tambah' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel jadwal -->
        <table class="table table-bordered">
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
                <?php if (mysqli_num_rows($jadwal) == 0): ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada jadwal</td>
                    </tr>
                <?php else:
                    while ($row = mysqli_fetch_assoc($jadwal)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['hari']) ?></td>
                            <td><?= htmlspecialchars($row['jam']) ?></td>
                            <td><?= htmlspecialchars($row['mapel']) ?></td>
                            <td><?= htmlspecialchars($row['nama_guru']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
                            <td>
                                <a href="jadwal.php?edit=<?= $row['id_jadwal'] ?>&kelas_id=<?= $filter_kelas ?>&guru_id=<?= $filter_guru ?>"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <a href="jadwal.php?hapus=<?= $row['id_jadwal'] ?>&kelas_id=<?= $filter_kelas ?>&guru_id=<?= $filter_guru ?>"
                                    class="btn btn-sm btn-danger" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>