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
    <style>
        body {
            background: #f5f8ff;
        }

        .content {
            margin-left: 240px;
            padding: 30px;
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
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="mb-2">
            <h4 class="fw-bold" style="color:#1565c0;">Halo, <?= htmlspecialchars($nama_guru) ?></h4>
        </div>
        <h1 class="mb-4 fw-bold" style="color:#1565c0;">Dashboard Guru</h1>
        <div class="row g-4 justify-content-start">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card shadow border-0 h-100"
                    style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-chalkboard-teacher fa-3x" style="color:#1565c0;"></i>
                        </div>
                        <h5 class="card-title fw-semibold">Kelas Diampu</h5>
                        <div class="display-4 fw-bold" style="color:#1565c0;"><?= $jml_kelas ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="card shadow border-0 h-100"
                    style="background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-calendar-day fa-3x" style="color:#ad1457;"></i>
                        </div>
                        <h5 class="card-title fw-semibold">Jadwal Hari Ini</h5>
                        <div class="display-4 fw-bold" style="color:#ad1457;"><?= $jml_jadwal ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-4">
                <div class="card shadow border-0 h-100"
                    style="background: linear-gradient(135deg, #e8f5e9 0%, #a5d6a7 100%);">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-calendar-alt fa-3x" style="color:#388e3c;"></i>
                        </div>
                        <h5 class="card-title fw-semibold">Kalender</h5>
                        <div class="display-6 fw-bold" style="color:#388e3c;"><?= $tanggal_hari_ini ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Animasi Bootstrap & Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</body>

</html>