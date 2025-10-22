<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    header('Location: ../index.php');
    exit();
}

// Get siswa data
$stmt = $db->prepare("SELECT s.*, k.nama as nama_kelas 
                      FROM siswa s 
                      JOIN kelas k ON s.kelas_id = k.id 
                      WHERE s.id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$siswa = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Portal Siswa</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="absensi.php">Absensi QR</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../config/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Profile Siswa</h5>
                        <hr>
                        <p class="card-text">
                            <strong>Nama:</strong> <?= htmlspecialchars($siswa['nama']) ?><br>
                            <strong>Kelas:</strong> <?= htmlspecialchars($siswa['nama_kelas']) ?><br>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Riwayat Absensi</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $absensi_query = "SELECT * FROM login_history 
                                                     WHERE id = ? 
                                                     ORDER BY login_time DESC LIMIT 10";
                                    $stmt = $db->prepare($absensi_query);
                                    $stmt->bind_param("i", $_SESSION['user_id']);
                                    $stmt->execute();
                                    $absensi = $stmt->get_result();

                                    if ($absensi->num_rows > 0):
                                        while ($row = $absensi->fetch_assoc()):
                                            ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($row['login_time'])) ?></td>
                                                <td><?= date('H:i', strtotime($row['login_time'])) ?></td>
                                                <td><?= $row['status'] ?></td>
                                            </tr>
                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <tr>
                                            <td colspan="3" class="text-center">Belum ada riwayat absensi</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>