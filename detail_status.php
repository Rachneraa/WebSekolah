<?php
session_start();
include 'config/koneksi.php'; // Pastikan path ini benar

// Cek apakah pendaftar sudah login
if (!isset($_SESSION['level']) || $_SESSION['level'] != 'pendaftar' || !isset($_SESSION['pendaftar_nisn'])) {
    // Arahkan ke halaman login utama jika tidak sesuai
    header("Location: index.php"); // Atau ke ppdb.php jika lebih sesuai
    exit();
}

$pendaftar_nisn = $_SESSION['pendaftar_nisn'];
$hasil_pencarian = null;

// Ambil data lengkap pendaftar
$query = "SELECT nama_lengkap, nisn, agama, jurusan, status
          FROM pendaftaran
          WHERE nisn = ?";

if ($stmt = mysqli_prepare($db, $query)) {
    mysqli_stmt_bind_param($stmt, "s", $pendaftar_nisn);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nama, $nisn, $agama, $jurusan, $status);

    if (mysqli_stmt_fetch($stmt)) {
        $hasil_pencarian = [
            'nama' => $nama,
            'nisn' => $nisn,
            'agama' => $agama,
            'jurusan' => $jurusan,
            'status' => $status
        ];
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Pendaftar - <?= htmlspecialchars($hasil_pencarian['nama'] ?? 'SMK TI') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #00499d; /* Mengambil dari nav.php */
            --primary-orange: #ff8303; /* Mengambil dari nav.php */
            --dark-blue: #003366;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --text-dark: #0f1724; /* Mengambil dari nav.php */
            --text-gray: #6b7280; /* Mengambil dari nav.php */
            --border-gray: #e5e7eb; /* Mengambil dari nav.php */
            --background-light: #f4f7f6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        /* === CSS BODY (Termasuk Animasi & Padding) === */
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            padding-top: 80px; /* Padding untuk header fixed */
            background: linear-gradient(-45deg, #f4f7f6, #e9f0f4, #f4f7f6, #fafafa);
            background-size: 400% 400%;
            animation: animatedBackground 15s ease infinite;
             overflow-x: hidden; /* Pastikan tidak ada scroll horizontal */
        }
        @keyframes animatedBackground { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        /* === AKHIR CSS BODY === */


        /* ==================== CSS HEADER (DISEDERHANAKAN) ==================== */
        header { background: #ffffff; padding: 20px 60px; display: flex; justify-content: space-between; align-items: center; position: fixed; top: 0; left: 0; right: 0; z-index: 1000; transition: all 0.3s ease; border-bottom: 1px solid var(--border-gray); height: 80px; }
        header.scrolled { padding: 12px 60px; height: 70px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); }
        .logo { display: flex; align-items: center; gap: 15px; font-weight: 800; font-size: 18px; letter-spacing: 0.5px; cursor: pointer; color: var(--primary-blue); text-decoration: none; transition: transform 0.3s ease; }
        .logo:hover { transform: scale(1.02); }
        .logo-img { width: 50px; height: 50px; object-fit: contain; transition: all 0.3s ease; }
        header.scrolled .logo-img { width: 40px; height: 40px; }
        header nav { display: flex; align-items: center; margin-left: auto; /* Otomatis dorong ke kanan */ }
        header nav ul { display: none; /* Sembunyikan UL kosong */ }
        .nav-buttons { display: flex; align-items: center; }
        /* Style Tombol Logout (.btn-admin digunakan untuk styling) */
        .btn-admin { display: flex; align-items: center; justify-content: center; gap: 8px; /* Jarak ikon & teks */ height: 40px; padding: 0 25px; /* Padding horizontal */ border-radius: 50px; background: var(--primary-orange); color: white; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.3s ease; }
        .btn-admin i { font-size: 1.1rem; }
        .btn-admin:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255, 131, 3, 0.3); background: #e07b00; }
        .btn-admin .login-label { /* Teks Logout */ display: inline; }

        /* --- Responsive Header (Tanpa Hamburger) --- */
        @media (max-width: 1024px) { header { padding: 20px 40px; } header.scrolled { padding: 12px 40px; } }
        @media (max-width: 768px) {
            body { padding-top: 60px; } /* Sesuaikan padding body */
            header { padding: 10px 15px; height: 60px; align-items: center; }
            header.scrolled { padding: 8px 15px; height: 55px; }
            .logo { font-size: 14px; gap: 8px; }
            .logo-img { width: 35px; height: 35px; }
            header.scrolled .logo-img { width: 30px; height: 30px; }
            /* Tombol Logout di Mobile */
            .btn-admin { height: 38px; padding: 0 15px; border-radius: 20px; }
            .btn-admin i { font-size: 1rem; }
            .btn-admin .login-label { display: none; } /* Sembunyikan teks di mobile */
        }
        @media (max-width: 480px) {
            body { padding-top: 55px; }
            header { padding: 8px 10px; height: 55px; }
            header.scrolled { padding: 6px 10px; height: 50px; }
            .logo { gap: 6px; }
            .logo-img { width: 30px; height: 30px; }
            header.scrolled .logo-img { width: 28px; height: 28px; }
            .logo-text { display: none; } /* Sembunyikan teks logo */
            .btn-admin { height: 34px; padding: 0 12px; }
            .btn-admin i { font-size: 0.9rem; }
        }
        /* ==================== AKHIR CSS HEADER ==================== */


        /* === CSS KONTEN HALAMAN (Sama seperti sebelumnya) === */
        .result-container { max-width: 700px; margin: 50px auto; /* Kurangi margin atas sedikit */ padding: 30px; background: white; border-radius: 15px; box-shadow: var(--shadow); text-align: center; }
        .result-container h2 { color: var(--primary-blue); font-size: 28px; margin-bottom: 20px; font-weight: 800; }
        .result-box { padding: 30px; border-radius: 10px; text-align: left; margin-top: 30px; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); /* Shadow ke dalam */ }
        /* Status Colors */
        .result-diterima { background-color: #e6ffe6; border: 1px solid #c3e6cb; color: #155724; }
        .result-proses { background-color: #e0f7ff; border: 1px solid #b8daff; color: #004085; }
        .result-ditolak { background-color: #ffe6e6; border: 1px solid #f5c6cb; color: #721c24; }

        .result-box h4 { font-size: 24px; margin-bottom: 25px; /* Tambah jarak bawah */ display: flex; align-items: center; justify-content: center; /* Tengahkan */ gap: 10px; font-weight: 700; text-transform: uppercase; padding-bottom: 15px; /* Garis bawah */ border-bottom: 2px dashed var(--border-gray); }
        .profile-icon { font-size: 80px; color: #ccc; margin-bottom: 25px; /* Tambah jarak */ display: block; text-align: center; }
        .result-detail { background-color: rgba(255,255,255,0.5); border-radius: 8px; padding: 20px; margin-top: 15px; border: 1px solid #eee; }
        .result-detail p { margin: 0; padding: 12px 0; /* Tambah padding */ border-bottom: 1px dashed #eee; display: flex; justify-content: space-between; align-items: center; /* Vertikal center */ flex-wrap: wrap; /* Wrap jika perlu */ gap: 5px; }
        .result-detail p:last-child { border-bottom: none; }
        .result-detail strong { color: var(--text-dark); font-weight: 600; font-size: 14px; /* Kecilkan label */ flex-shrink: 0; /* Agar tidak menyusut */ margin-right: 10px; }
        .result-detail span { font-weight: 600; /* Normal weight */ color: var(--text-gray); /* Warna value */ text-align: right; /* Rata kanan */ font-size: 15px; word-break: break-word; /* Agar teks panjang wrap */ }
        .result-detail span.nisn-text { color: var(--primary-orange) !important; font-weight: 700; } /* Khusus NISN */
        .result-detail span[style*="color"] { font-weight: 700; } /* Status tetap bold */
        p.not-found { text-align: center; color: var(--text-gray); margin-top: 20px;} /* Untuk pesan data tidak ditemukan */

        /* --- Responsif Konten --- */
        @media (max-width: 600px) {
            .result-container { width: 90%; margin: 30px auto; padding: 20px; }
            .result-container h2 { font-size: 22px; }
            .result-box { padding: 20px; }
            .result-box h4 { font-size: 18px; padding-bottom: 10px; margin-bottom: 20px; }
            .profile-icon { font-size: 60px; margin-bottom: 20px; }
            .result-detail p { flex-direction: column; align-items: flex-start; gap: 5px; padding: 10px 0;}
            .result-detail strong { margin-right: 0; margin-bottom: 3px; font-size: 13px; }
            .result-detail span { text-align: left; font-size: 14px; }
        }
        /* ==================== AKHIR CSS KONTEN ==================== */

    </style>
</head>

<body>
    <header id="header">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="assets/logo.png" alt="Logo SMK TI Garuda Nusantara" class="logo-img">
            <span class="logo-text">SMK TI Garuda Nusantara</span>
        </div>
        <nav id="nav">
            <ul></ul> <div class="nav-buttons">
                 <a href="config/logout.php" class="btn-admin btn-block" title="Keluar">
                     <i class="fas fa-sign-out-alt"></i>
                     <span class="login-label">Logout</span>
                 </a>
            </div>
        </nav>
        </header>
    <div class="result-container">
        <h2>Detail Status Pendaftaran Anda</h2>

        <?php if ($hasil_pencarian): ?>
            <div class="result-box result-<?= htmlspecialchars(strtolower($hasil_pencarian['status'])) ?>">

                <h4>
                    <i class="fas fa-<?= $hasil_pencarian['status'] == 'diterima' ? 'check-circle' : ($hasil_pencarian['status'] == 'proses' ? 'clock' : 'times-circle') ?>"></i>
                    STATUS: <?= htmlspecialchars(strtoupper($hasil_pencarian['status'])) ?>
                </h4>

                <span class="profile-icon"><i class="fas fa-user-circle"></i></span>

                <div class="result-detail">
                         <p><strong>Nama</strong> <span><?= htmlspecialchars($hasil_pencarian['nama']) ?></span></p>
                         <p><strong>NISN</strong> <span class="nisn-text"><?= htmlspecialchars($hasil_pencarian['nisn']) ?></span></p>
                         <p><strong>Agama</strong> <span><?= htmlspecialchars(ucwords($hasil_pencarian['agama'] ? $hasil_pencarian['agama'] : '-')) ?></span></p>
                         <p><strong>Jurusan yang Diambil</strong> <span><?= htmlspecialchars(ucwords($hasil_pencarian['jurusan'])) ?></span></p>
                         <p><strong>Status</strong> <span style="color: <?= $hasil_pencarian['status'] == 'diterima' ? '#28a745' : ($hasil_pencarian['status'] == 'proses' ? '#0A66C2' : '#dc3545') ?>;">
                            <?= htmlspecialchars(ucwords($hasil_pencarian['status'])) ?>
                         </span></p>
                </div>
            </div>

        <?php else: ?>
            <p class="not-found">Data pendaftar dengan NISN tersebut tidak ditemukan. Silakan hubungi admin sekolah jika terjadi kesalahan.</p>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const header = document.getElementById('header');

        // Scroll Effect for Header
        if (header) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 20) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });
        }
        // Tidak perlu Resize Handler khusus untuk menu lagi
    });
    </script>
     </body>
</html>