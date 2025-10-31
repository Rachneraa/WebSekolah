<?php
// File: guru/dashboard.php
require_once '../config/koneksi.php';

// Cek login dan level guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

// === PERBAIKAN: Gunakan ID guru yang benar dari session ===
$guru_id = $_SESSION['guru_id']; // Menggunakan ID dari tabel 'guru' (misal: 14)

// Ambil nama guru
$nama_guru = 'Guru';
if ($stmt_nama = $db->prepare("SELECT nama FROM guru WHERE id = ? LIMIT 1")) {
    $stmt_nama->bind_param("i", $guru_id);
    $stmt_nama->execute();
    $result_nama = $stmt_nama->get_result();
    if ($data_nama = $result_nama->fetch_assoc()) {
        $nama_guru = $data_nama['nama'];
    }
    $stmt_nama->close();
}

// Jumlah kelas yang diajar (Query sekarang aman)
$jml_kelas = 0;
if ($stmt_kelas = $db->prepare("SELECT COUNT(DISTINCT kelas_id) as jml FROM jadwal WHERE guru_id = ?")) {
    $stmt_kelas->bind_param("i", $guru_id);
    $stmt_kelas->execute();
    $result_kelas = $stmt_kelas->get_result();
    if ($data_kelas = $result_kelas->fetch_assoc()) {
        $jml_kelas = $data_kelas['jml'] ?? 0;
    }
    $stmt_kelas->close();
}

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
// Pastikan hari default ada jika 'l' gagal
$hari_ini = $hari_map[date('l')] ?? 'Senin'; 

// Jumlah jadwal hari ini (Query sekarang aman)
$jml_jadwal = 0;
if ($stmt_jadwal = $db->prepare("SELECT COUNT(*) as jml FROM jadwal WHERE guru_id = ? AND hari = ?")) {
    $stmt_jadwal->bind_param("is", $guru_id, $hari_ini);
    $stmt_jadwal->execute();
    $result_jadwal = $stmt_jadwal->get_result();
    if ($data_jadwal = $result_jadwal->fetch_assoc()) {
        $jml_jadwal = $data_jadwal['jml'] ?? 0;
    }
    $stmt_jadwal->close();
}

// ==========================================================
// === PERBAIKAN: Mengganti strftime() yang usang ===
// ==========================================================
$tanggal_hari_ini = '';
// Cek apakah ekstensi 'intl' aktif
if (class_exists('IntlDateFormatter')) {
    $formatter = new IntlDateFormatter(
        'id_ID', // Locale: Indonesian
        IntlDateFormatter::FULL, // Date type
        IntlDateFormatter::NONE, // Time type
        'Asia/Jakarta', // Timezone
        IntlDateFormatter::GREGORIAN,
        'EEEE, dd MMMM yyyy' // Format: Hari, Tanggal Bulan Tahun
    );
    $tanggal_hari_ini = $formatter->format(new DateTime());
} else {
    // Fallback (cadangan) jika ekstensi 'intl' tidak ada
    // Ini akan tampil dalam bahasa Inggris
    $tanggal_hari_ini = date('l, d F Y');
}
// ==========================================================
// === BATAS AKHIR PERBAIKAN ===
// ==========================================================

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    
    <!-- PWA META TAGS -->
    <meta name="theme-color" content="#00499D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC">
    
    <!-- ICONS - PATH YANG BENAR -->
    <link rel="icon" type="image/png" href="../icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../icons/favicon.svg" />
    <link rel="shortcut icon" href="../icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../icons/apple-touch-icon.png" />
    <link rel="manifest" href="../manifest.json">
    
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
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card h5 {
             margin-bottom: 10px;
             font-size: 1.1rem;
             font-weight: 600;
        }

        .stat-card .number {
            font-size: 2.2em;
            font-weight: 700;
        }
        
        .stat-card .date-text {
            font-size: 1.25rem;
            font-weight: 600;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            #content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div id="content">
            <h1 class="mb-4 fw-bold" style="color:#1565c0;">Dashboard Guru</h1>
            <div class="row g-4 justify-content-start">
                
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card text-center">
                        <i class="fas fa-chalkboard-teacher fa-2x mb-2" style="color:#1565c0;"></i>
                        <h5>Kelas Diampu</h5>
                        <div class="number"><?= $jml_kelas ?></div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="stat-card text-center" style="background:#ffe0e6;">
                        <i class="fas fa-calendar-alt fa-2x mb-2" style="color:#c2185b;"></i>
                        <h5>Jadwal Hari Ini</h5>
                        <div class="number" style="color:#c2185b;"><?= $jml_jadwal ?></div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="stat-card text-center" style="background:#e0f2f1;">
                        <i class="fas fa-calendar fa-2x mb-2" style="color:#388e3c;"></i>
                        <h5>Kalender</h5>
                        <div class="date-text" style="color:#388e3c;"><?= $tanggal_hari_ini ?></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // SERVICE WORKER REGISTRATION - PATH YANG BENAR
        if ('serviceWorker' in navigator) {
            // Karena file ini ada di folder guru/, gunakan path relative ke root
            navigator.serviceWorker.register('../sw.js')
                .then(registration => {
                    console.log('✅ SW Registered successfully di Guru Dashboard');
                    console.log('Scope:', registration.scope);
                })
                .catch(error => {
                    console.log('❌ SW Registration failed:', error);
                    // Fallback: coba path absolute
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('✅ SW Registered dengan path absolute'))
                        .catch(err => console.log('❌ Semua path gagal'));
                });
        }

        // Debug: test apakah file bisa diakses
        console.log('🔍 Testing PWA files...');
        fetch('../manifest.json')
            .then(r => console.log('📄 Manifest status:', r.status))
            .catch(e => console.log('📄 Manifest error'));
        
        fetch('../sw.js')
            .then(r => console.log('⚙️ SW status:', r.status))
            .catch(e => console.log('⚙️ SW error'));
    </script>
</body>
</html>