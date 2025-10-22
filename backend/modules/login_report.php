<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Export function
if (isset($_POST['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="login_report.xls"');

    echo "Nama\tKelas\tWaktu Login\tStatus\tFoto\n";

    $query = "SELECT s.nama, k.nama as kelas, l.login_time, l.login_status, l.login_photo 
              FROM login_history l 
              JOIN siswa s ON l.siswa_id = s.id 
              JOIN kelas k ON s.kelas_id = k.id 
              ORDER BY l.login_time DESC";

    $result = $db->query($query);
    while ($row = $result->fetch_assoc()) {
        echo $row['nama'] . "\t";
        echo $row['kelas'] . "\t";
        echo $row['login_time'] . "\t";
        echo $row['login_status'] . "\t";
        echo $row['login_photo'] . "\n";
    }
    exit;
}

// Display report
$query = "SELECT s.nama, k.nama as kelas, l.login_time, l.login_status, l.login_photo 
          FROM login_history l 
          JOIN siswa s ON l.siswa_id = s.id 
          JOIN kelas k ON s.kelas_id = k.id 
          ORDER BY l.login_time DESC 
          LIMIT 100";

$result = $db->query($query);
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Laporan Login Siswa</h5>
        <form method="post">
            <button type="submit" name="export" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Waktu Login</th>
                        <th>Status</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= htmlspecialchars($row['kelas']) ?></td>
                            <td><?= $row['login_time'] ?></td>
                            <td><?= $row['login_status'] ?></td>
                            <td>
                                <?php if ($row['login_photo']): ?>
                                    <a href="../uploads/login_photos/<?= $row['login_photo'] ?>" target="_blank">
                                        <img src="../uploads/login_photos/<?= $row['login_photo'] ?>" alt="Login Photo"
                                            style="height: 50px;">
                                    </a>
                                <?php else: ?>
                                    No photo
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>