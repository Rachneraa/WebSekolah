<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    header('Location: ../index.php');
    exit();
}

$siswa_id = $_SESSION['user_id'];
$tanggal_hari_ini = date('Y-m-d');

// Cek apakah sudah absen hari ini
$sudah_absen = false;
$waktu_absen = null;
$status_absen = null;

$cek_absen = $db->prepare("SELECT status, waktu_absen FROM absensi_detail WHERE siswa_id = ? AND tanggal = ? AND status = 'Hadir' LIMIT 1");
$cek_absen->bind_param("is", $siswa_id, $tanggal_hari_ini);
$cek_absen->execute();
$result_absen = $cek_absen->get_result();

if ($row_absen = $result_absen->fetch_assoc()) {
    $sudah_absen = true;
    $waktu_absen = $row_absen['waktu_absen'];
    $status_absen = $row_absen['status'];
}
$cek_absen->close();

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
    <meta name="theme-color" content="#00499D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC">

    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi QR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
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
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
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
            flex-grow: 1;
            overflow-y: auto;
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
            position: static;
            padding: 15px 15px 0;
            margin-top: auto;
            flex-shrink: 0;
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

        /* ============================================================
           PERBAIKAN KEDUA: Mengubah bentuk kotak agar Potrait
           ============================================================ */
        .scanner-container {
            position: relative;
            width: 300px;
            height: 400px;
            /* DIUBAH DARI 300px (menjadi Potrait 3:4) */
            margin: 0 auto;
            overflow: hidden;
            border-radius: 10px;
            border: 2px solid #ccc;
            background: #000;
        }

        /* ============================================================
           PERBAIKAN KESATU: Mengembalikan ke 'cover'
           ============================================================ */
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
            /* DIKEMBALIKAN KE 'cover' */
            transform-origin: center;
        }

        /* ================= AKHIR PERBAIKAN ================= */

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
                height: 100%;
                border-radius: 20px 0 0 20px;
                transition: right 0.3s ease;
                padding-bottom: 20px;
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

            /* ============================================================
               PERBAIKAN KETIGA: Menyesuaikan tinggi di tablet
               ============================================================ */
            .scanner-container {
                width: 100%;
                height: 350px;
                /* DIUBAH DARI 220px */
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

            /* ============================================================
               PERBAIKAN KEEMPAT: Menyesuaikan tinggi di HP
               ============================================================ */
            .scanner-container {
                width: 100%;
                height: 300px;
                /* DIUBAH DARI 180px */
            }

            .absensi-card {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

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

        <?php if ($sudah_absen): ?>
            <!-- Tampilan jika sudah absen -->
            <div class="absensi-card">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h4 class="text-success mb-3">Anda Sudah Absen Hari Ini!</h4>
                    <div class="alert alert-success d-inline-block">
                        <p class="mb-1"><strong>Status:</strong> <?= htmlspecialchars($status_absen) ?></p>
                        <p class="mb-1"><strong>Waktu Absen:</strong> <?= date('H:i', strtotime($waktu_absen)) ?> WIB</p>
                        <p class="mb-0"><strong>Tanggal:</strong> <?= date('d F Y', strtotime($tanggal_hari_ini)) ?></p>
                    </div>
                    <p class="text-muted mt-4">
                        <i class="fas fa-info-circle"></i>
                        Fitur absensi akan aktif kembali besok.
                    </p>
                    <a href="dashboard.php" class="btn btn-primary mt-3">
                        <i class="fas fa-home"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Tampilan scan QR jika belum absen -->
            <div class="absensi-card">
                <h5 class="mb-3"><i class="fas fa-qrcode"></i> Scan QR Code Kelas</h5>
                <div class="scanner-container mx-auto mb-3">
                    <div id="preview" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
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
        <?php endif; ?>
    </main>

    <?php if (!$sudah_absen): ?>
        <script>
            // FUNGSI SCAN QR dengan html5-qrcode
            let html5QrCode = null;
            let isScanning = false;

            async function startScanner() {
                try {
                    html5QrCode = new Html5Qrcode("preview");

                    const config = {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    };

                    await html5QrCode.start(
                        { facingMode: "environment" }, // Kamera belakang
                        config,
                        (decodedText, decodedResult) => {
                            // QR berhasil di-scan
                            kirimAbsensi(decodedText);
                        },
                        (errorMessage) => {
                            // Error scan (abaikan, ini normal saat tidak ada QR)
                        }
                    );

                    isScanning = true;
                    document.getElementById('startButton').innerHTML =
                        '<i class="fas fa-times"></i> Tutup Kamera';
                    document.getElementById('switchButton').style.display = 'inline-block';

                } catch (err) {
                    console.error('Scanner error:', err);
                    alert('Error mengakses kamera: ' + err.message);
                }
            }

            async function stopScanner() {
                if (html5QrCode && isScanning) {
                    try {
                        await html5QrCode.stop();
                        document.getElementById('preview').innerHTML = '';
                        document.getElementById('startButton').innerHTML =
                            '<i class="fas fa-camera"></i> Buka Kamera';
                        document.getElementById('switchButton').style.display = 'none';
                        isScanning = false;
                    } catch (err) {
                        console.error('Error stopping scanner:', err);
                    }
                }
            }

            async function switchCamera() {
                if (html5QrCode && isScanning) {
                    try {
                        await html5QrCode.stop();

                        // Toggle antara front dan back camera
                        const currentFacingMode = html5QrCode._currentFacingMode === "environment" ? "user" : "environment";

                        const config = {
                            fps: 10,
                            qrbox: { width: 250, height: 250 },
                            aspectRatio: 1.0
                        };

                        await html5QrCode.start(
                            { facingMode: currentFacingMode },
                            config,
                            (decodedText) => kirimAbsensi(decodedText),
                            (errorMessage) => { }
                        );
                    } catch (err) {
                        console.error('Error switching camera:', err);
                        alert('Gagal mengganti kamera. Coba tutup dan buka kamera kembali.');
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('switchButton').style.display = 'none';
            });

            document.getElementById('startButton').addEventListener('click', async function () {
                if (!isScanning) {
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
                if (html5QrCode && isScanning) {
                    html5QrCode.stop();
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
        </script>
    <?php endif; ?>

    <!-- Script Sidebar (selalu aktif) -->
    <script>
        // SIDEBAR & OVERLAY FUNGSI (TIDAK BERUBAH)
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
        // SERVICE WORKER REGISTRATION
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => console.log('SW Registered'))
                .catch(error => console.log('SW Registration failed:', error));
        }
    </script>
</body>

</html>