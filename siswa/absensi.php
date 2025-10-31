<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    header('Location: ../index.php');
    exit();
}

if (isset($_POST['absen'])) {
    // Proses absen siswa
    // ...kode absen...

    // Redirect dengan parameter sukses
    echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "?absen=success';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .welcome-text {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .welcome-content {
            flex: 1;
        }

        .welcome-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .absensi-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            max-width: 400px;
            margin: 0 auto;
        }

        .scanner-container {
            position: relative;
            width: 300px;
            height: 300px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 10px;
            border: 2px solid #ccc;
            background: #000;
        }

        #preview {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%) scaleX(1) !important;
            object-fit: cover;
            transform-origin: center;
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #fff;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
        }

        .button-group {
            margin-top: 1rem;
        }

        /* Mobile Menu Toggle */
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
                height: 100vh;
                border-radius: 20px 0 0 20px;
                transition: right 0.3s ease;
            }

            .sidebar.active {
                right: 0;
            }

            .mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .main-content {
                margin-left: 0;
                margin-right: 0;
                margin-top: 70px;
                padding: 0;
            }

            .page-header h2 {
                font-size: 20px;
            }

            .page-header .user-name {
                font-size: 20px;
            }

            .page-header p {
                font-size: 12px;
            }

            .welcome-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .scanner-container {
                width: 100%;
                height: 220px;
            }

            .absensi-card {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .mobile-toggle {
                top: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .main-content {
                margin-top: 80px;
            }

            .page-header {
                padding: 15px;
                margin-bottom: 15px;
            }

            .page-header h2 {
                font-size: 16px;
                margin-bottom: 5px;
            }

            .page-header .user-name {
                font-size: 18px;
                margin-bottom: 8px;
            }

            .page-header p {
                font-size: 11px;
            }

            .welcome-text {
                gap: 10px;
            }

            .welcome-icon {
                width: 35px;
                height: 35px;
                font-size: 18px;
            }

            .scanner-container {
                width: 100%;
                height: 180px;
            }

            .absensi-card {
                padding: 10px;
            }
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
                <a href="absensi.php" class="nav-link active">
                    <i class="fas fa-qrcode"></i>
                    <span>Absensi QR</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="jadwal.php" class="nav-link">
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
            <div class="welcome-text">
                <div class="welcome-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="welcome-content">
                    <h2>Absensi QR Kelas</h2>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></div>
                    <p><i class="fas fa-calendar"></i> <?= date('l, d F Y') ?> | <i class="fas fa-clock"></i>
                        <?= date('H:i') ?> WIB
                    </p>
                </div>
            </div>
        </div>
        <div class="absensi-card">
            <h5 class="mb-3"><i class="fas fa-qrcode"></i> Scan QR Code Kelas</h5>
            <div class="scanner-container mx-auto mb-3">
                <video id="preview"></video>
                <div class="scanner-overlay"></div>
            </div>
            <div class="button-group text-center">
                <button id="startButton" class="btn btn-primary me-2">
                    <i class="fas fa-camera"></i> Buka Kamera
                </button>
                <button id="switchButton" class="btn btn-secondary">
                    <i class="fas fa-sync"></i> Ganti Kamera
                </button>
            </div>
        </div>
    </main>

    <script>
        // FUNGSI SCAN QR TETAP DIPERTAHANKAN
        let scanner = null;
        let currentCamera = 0;
        let cameras = [];

        async function startScanner() {
            try {
                scanner = new Instascan.Scanner({
                    video: document.getElementById('preview'),
                    mirror: false,
                    captureImage: true,
                    scanPeriod: 5
                });

                try {
                    cameras = await Instascan.Camera.getCameras();

                    if (cameras.length === 0) {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        cameras = devices.filter(device => device.kind === 'videoinput');
                    }
                } catch (e) {
                    console.error('Camera detection error:', e);
                    cameras = [{ deviceId: '' }];
                }

                if (cameras.length > 0) {
                    let selectedCamera = cameras[cameras.length - 1];

                    try {
                        await scanner.start(selectedCamera);
                        currentCamera = cameras.indexOf(selectedCamera);

                        const video = document.getElementById('preview');
                        video.setAttribute('playsinline', '');
                        video.setAttribute('controls', 'false');
                        video.style.width = '100%';
                        video.style.transform = 'scaleX(1)';

                        document.getElementById('startButton').innerHTML =
                            '<i class="fas fa-times"></i> Tutup Kamera';
                        document.getElementById('switchButton').style.display =
                            cameras.length > 1 ? 'inline-block' : 'none';
                    } catch (e) {
                        console.error('Camera start error:', e);
                        await scanner.start(cameras[0]);
                    }

                    scanner.addListener('scan', function (content) {
                        kirimAbsensi(content);
                    });
                } else {
                    alert('Tidak ada kamera yang tersedia');
                }
            } catch (err) {
                console.error('Scanner error:', err);
                alert('Error mengakses kamera: ' + err.message);
            }
        }

        async function stopScanner() {
            if (scanner) {
                try {
                    await scanner.stop();
                    document.getElementById('startButton').innerHTML =
                        '<i class="fas fa-camera"></i> Buka Kamera';
                    scanner = null;
                } catch (err) {
                    console.error('Error stopping scanner:', err);
                }
            }
        }

        async function switchCamera() {
            try {
                if (cameras.length > 1 && scanner) {
                    await scanner.stop();
                    currentCamera = (currentCamera + 1) % cameras.length;

                    try {
                        await scanner.start(cameras[currentCamera]);
                    } catch (e) {
                        console.error('Switch camera error:', e);
                        await scanner.start({
                            deviceId: cameras[currentCamera].deviceId,
                            facingMode: currentCamera === 0 ? 'user' : 'environment'
                        });
                    }
                }
            } catch (err) {
                console.error('Error switching camera:', err);
                alert('Gagal mengganti kamera. Coba tutup dan buka kamera kembali.');
                await stopScanner();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('switchButton').style.display = 'none';
        });

        document.getElementById('startButton').addEventListener('click', async function () {
            if (!scanner) {
                await startScanner();
            } else {
                await stopScanner();
            }
        });

        document.getElementById('switchButton').addEventListener('click', async function () {
            this.disabled = true;
            await switchCamera();
            this.disabled = false;
        });

        window.addEventListener('beforeunload', function () {
            if (scanner) {
                scanner.stop();
            }
        });

        function kirimAbsensi(qr_content) {
            fetch('../api/absensi.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ qr_content: qr_content })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Absen Berhasil',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Absen',
                            text: data.message
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan koneksi!'
                    });
                });
        }

        // SIDEBAR & OVERLAY FUNGSI SAMA DENGAN DASHBOARD
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        mobileToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 992) {
                if (!sidebar.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            }
        });
        window.addEventListener('resize', function () {
            if (window.innerWidth > 992) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    </script>
</body>

</html>