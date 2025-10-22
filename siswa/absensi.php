<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    header('Location: ../index.php');
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
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <style>
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
            /* Force no mirroring */
            object-fit: cover;
            transform-origin: center;
        }

        #preview::-webkit-media-controls {
            display: none !important;
        }

        video::-webkit-media-controls {
            display: none !important;
        }

        video {
            transform: scaleX(1) !important;
            /* Force no mirroring */
            -webkit-transform: scaleX(1) !important;
            -moz-transform: scaleX(1) !important;
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
    </style>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="absensi.php">Absensi QR</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-3">Scan QR Code Kelas</h5>
                        <div class="scanner-container">
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
                </div>
            </div>
        </div>
    </div>

    <script>
        let scanner = null;
        let currentCamera = 0;
        let cameras = [];

        async function startScanner() {
            try {
                scanner = new Instascan.Scanner({
                    video: document.getElementById('preview'),
                    mirror: false, // Disable mirroring
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
                    // Try to find back camera
                    let selectedCamera = cameras[cameras.length - 1]; // Default to last camera (usually back)

                    try {
                        await scanner.start(selectedCamera);
                        currentCamera = cameras.indexOf(selectedCamera);

                        // Prevent fullscreen
                        const video = document.getElementById('preview');
                        video.setAttribute('playsinline', ''); // Prevent fullscreen on iOS
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
                        if ('vibrate' in navigator) {
                            navigator.vibrate(200);
                        }

                        fetch('../api/absensi.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                qr_content: content,
                                id_siswa: <?= $_SESSION['user_id'] ?>,
                                kelas_id: <?= $_SESSION['kelas_id'] ?>
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('Absensi berhasil!');
                                    stopScanner().then(() => {
                                        window.location.href = 'dashboard.php';
                                    });
                                } else {
                                    alert(data.message || 'Gagal melakukan absensi!');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Terjadi kesalahan saat memproses absensi');
                            });
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

        // Add this new function for better camera switching
        async function switchCamera() {
            try {
                if (cameras.length > 1 && scanner) {
                    await scanner.stop();
                    currentCamera = (currentCamera + 1) % cameras.length;

                    try {
                        await scanner.start(cameras[currentCamera]);
                    } catch (e) {
                        console.error('Switch camera error:', e);
                        // Try alternative method
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
    </script>
</body>

</html>