<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Export function
if (isset($_POST['export'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="absensi_report.xls"');

    echo "Nama\tKelas\tTanggal\tStatus\n";

    $query = "SELECT s.nama, k.nama as kelas, l.login_time, l.status 
              FROM login_history l 
              JOIN siswa s ON l.id_siswa = s.id 
              JOIN kelas k ON s.kelas_id = k.id 
              ORDER BY l.login_time DESC";

    $result = $db->query($query);
    while ($row = $result->fetch_assoc()) {
        echo $row['nama'] . "\t";
        echo $row['kelas'] . "\t";
        echo date('d/m/Y H:i', strtotime($row['login_time'])) . "\t";
        echo $row['status'] . "\n";
    }
    exit;
}

// Get date filter
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Display report
$query = "SELECT s.nama, k.nama as kelas, l.login_time, l.status 
          FROM login_history l 
          JOIN siswa s ON l.siswa_id = s.id 
          JOIN kelas k ON s.kelas_id = k.id 
          WHERE DATE(l.login_time) = ?
          ORDER BY k.nama, s.nama";

$stmt = $db->prepare($query);
$stmt->bind_param("s", $date_filter);
$stmt->execute();
$result = $stmt->get_result();

// Get all siswa for checking absences
$all_siswa = "SELECT s.id, s.nama, k.nama as kelas 
              FROM siswa s 
              JOIN kelas k ON s.kelas_id = k.id 
              ORDER BY k.nama, s.nama";
$all_result = $db->query($all_siswa);
$siswa_attendance = [];
while ($row = $all_result->fetch_assoc()) {
    $siswa_attendance[$row['id']] = [
        'nama' => $row['nama'],
        'kelas' => $row['kelas'],
        'status' => 'Tidak Hadir'
    ];
}

// Update status for present students
while ($row = $result->fetch_assoc()) {
    if (isset($siswa_attendance[$row['siswa_id']])) {
        $siswa_attendance[$row['siswa_id']]['status'] = $row['status'];
        $siswa_attendance[$row['siswa_id']]['login_time'] = $row['login_time'];
    }
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Laporan Absensi Siswa</h5>
        <div>
            <input type="date" class="form-control d-inline-block" style="width: auto;" value="<?= $date_filter ?>"
                onchange="window.location.href='?page=absensi&date='+this.value">
            <form method="post" class="d-inline-block">
                <button type="submit" name="export" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Waktu Absen</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswa_attendance as $siswa_id => $data): ?>
                        <tr class="<?= $data['status'] == 'Tidak Hadir' ? 'table-danger' : '' ?>">
                            <td><?= htmlspecialchars($data['nama']) ?></td>
                            <td><?= htmlspecialchars($data['kelas']) ?></td>
                            <td><?= isset($data['login_time']) ? date('H:i', strtotime($data['login_time'])) : '-' ?></td>
                            <td><?= $data['status'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>