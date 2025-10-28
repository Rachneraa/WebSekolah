<?php
require_once '../config/koneksi.php';

// Cek login dan level guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

$guru_id = $_SESSION['user_id'];

// Ambil nama guru
$q_nama = mysqli_query($db, "SELECT nama FROM guru WHERE id='$guru_id' LIMIT 1");
$nama_guru = mysqli_fetch_assoc($q_nama)['nama'] ?? 'Guru';

// Jumlah kelas yang diajar
$q_kelas = mysqli_query($db, "SELECT COUNT(DISTINCT kelas_id) as jml FROM jadwal WHERE guru_id='$guru_id'");
$jml_kelas = mysqli_fetch_assoc($q_kelas)['jml'] ?? 0;

// Mapping hari Inggris ke Indonesia
$hari_map = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Minggu'
];
$hari_ini = $hari_map[date('l')];

// Jumlah jadwal hari ini
$q_jadwal = mysqli_query($db, "SELECT COUNT(*) as jml FROM jadwal WHERE guru_id='$guru_id' AND hari='$hari_ini'");
$jml_jadwal = mysqli_fetch_assoc($q_jadwal)['jml'] ?? 0;

// Tanggal hari ini (kalender)
$tanggal_hari_ini = date('l, d F Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f8ff;
            color: #333;
        }

        .wrapper {
            display: flex;
            width: 100%;
        }


        #content {
            flex: 1;
            padding: 25px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .stat-card h3 {
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: 0;
                position: fixed;
                z-index: 999;
            }

            #content {
                width: 100%;
                margin-left: 0;
            }



    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <!-- Content -->
        <div id="content">
            <h1 class="mb-4 fw-bold" style="color:#1565c0;">Dashboard Guru</h1>
            <div class="row g-4 justify-content-start">
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color:#1565c0;"></i>
                        <h5>Kelas Diampu</h5>
                        <div class="number"><?= $jml_kelas ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center" style="background:#ffe0e6;">
                        <i class="fas fa-calendar-alt fa-2x mb-2" style="color:#c2185b;"></i>
                        <h5>Jadwal Hari Ini</h5>
                        <div class="number" style="color:#c2185b;"><?= $jml_jadwal ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center" style="background:#e0f2f1;">
                        <i class="fas fa-calendar fa-2x mb-2" style="color:#388e3c;"></i>
                        <h5>Kalender</h5>
                        <div class="number" style="color:#388e3c;"><?= $tanggal_hari_ini ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

    </script>
</body>

</html>