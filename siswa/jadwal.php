<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    header('Location: ../index.php');
    exit();
}

// Get siswa data (untuk sidebar/header)
$stmt = $db->prepare("SELECT s.*, k.nama as nama_kelas 
                        FROM siswa s 
                        JOIN kelas k ON s.kelas_id = k.kelas_id 
                        WHERE s.siswa_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$siswa = $stmt->get_result()->fetch_assoc();

// Ambil kelas siswa
$kelas_id = $siswa['kelas_id'] ?? 0;

// Ambil jadwal pelajaran untuk kelas siswa
$jadwal = [];
$stmt = $db->prepare("SELECT j.hari, j.jam, m.nama as nama_mapel, g.nama as nama_guru
    FROM jadwal j
    LEFT JOIN mapel m ON j.mapel_id = m.id
    LEFT JOIN guru g ON j.guru_id = g.id
    WHERE j.kelas_id = ?
    ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), j.jam");
$stmt->bind_param("i", $kelas_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc())
    $jadwal[] = $row;
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <!-- PWA META TAGS -->
    <meta name="theme-color" content="#00499D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC">

    <!-- ICONS -->
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pelajaran - SMK TI Garuda Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-blue: #00499d;
            --primary-orange: #ff8303;
            --dark-blue: #003366;
            --light-blue: #e3f2fd;
            --success-green: #28a745;
            --warning-yellow: #ffc107;
            --danger-red: #dc3545;
            --info-blue: #17a2b8;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border-color: #e0e0e0;
            --sidebar-width: 260px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .sidebar {
            position: fixed;
            top: 20px;
            left: 20px;
            width: var(--sidebar-width);
            height: calc(100vh - 40px);
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 30px 0;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 0 25px 25px;
            border-bottom: 2px solid var(--border-color);
            text-align: center;
        }

        .sidebar-header h4 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--text-muted);
            font-size: 13px;
            margin: 0;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--text-dark);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
        }

        .nav-link:hover {
            background: var(--light-blue);
            color: var(--primary-blue);
            transform: translateX(5px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            box-shadow: 0 4px 15px rgba(0, 73, 157, 0.3);
        }

        .nav-link i {
            width: 24px;
            font-size: 18px;
            margin-right: 12px;
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            left: 15px;
            right: 15px;
        }

        .btn-logout {
            width: 100%;
            background: linear-gradient(135deg, var(--danger-red), #c82333);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }

        .main-content {
            margin-left: calc(var(--sidebar-width) + 40px);
            padding: 0 20px 20px 0;
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .page-header h2 {
            color: var(--primary-blue);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .page-header .user-name {
            color: var(--primary-orange);
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-muted);
            margin: 0;
            font-size: 14px;
        }

        .jadwal-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
        }

        .jadwal-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2575fc;
            margin-bottom: 1.5rem;
        }

        .table thead th {
            background: #e3f0ff;
            color: #2575fc;
            font-weight: 600;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .table-responsive {
            margin-top: 1rem;
        }

        @media (max-width: 1200px) {
            .main-content {
                margin-left: 0;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                right: -280px;
                left: auto;
                top: 0;
                height: 100%;
                border-radius: 20px 0 0 20px;
                transition: right 0.3s ease;
            }

            .sidebar.active {
                right: 0;
            }

            .main-content {
                margin-left: 0;
                margin-right: 0;
                margin-top: 70px;
                padding: 0;
            }
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            top: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            z-index: 1001;
            cursor: pointer;
            color: var(--primary-blue);
            font-size: 20px;
        }

        @media (max-width: 992px) {
            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-graduation-cap"></i> Portal Siswa</h4>
            <p>SMK TI Garuda Nusantara</p>
        </div>

        <nav class="nav-menu">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="absensi.php" class="nav-link">
                    <i class="fas fa-qrcode"></i>
                    <span>Absensi QR</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="jadwal.php" class="nav-link active">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Jadwal Pelajaran</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="nilai.php" class="nav-link">
                    <i class="fas fa-chart-line"></i>
                    <span>Nilai</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="pengaturan.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Pengaturan Akun</span>
                </a>
            </div>
        </nav>
        </nav>

        <div class="logout-section">
            <a href="../config/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="page-header">
            <h2><i class="fas fa-calendar-alt"></i> Pelajaran Hari Ini</h2>
            <div class="user-name"><?= htmlspecialchars($siswa['nama'] ?? '') ?> - Kelas
                <?= htmlspecialchars($siswa['nama_kelas'] ?? '') ?>
            </div>
        </div>
        <div class="jadwal-card">
            <div class="jadwal-title">Jadwal Pelajaran</div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($jadwal):
                            foreach ($jadwal as $j): ?>
                                <tr>
                                    <td><?= htmlspecialchars($j['hari'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($j['jam'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($j['nama_mapel'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($j['nama_guru'] ?? '') ?></td>
                                </tr>
                            <?php endforeach;
                        else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada jadwal pelajaran.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        mobileToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });

        // Close sidebar when clicking overlay
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            }
        });
        // SERVICE WORKER REGISTRATION
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => console.log('SW Registered'))
                .catch(error => console.log('SW Registration failed:', error));
        }
    </script>
</body>

</html>