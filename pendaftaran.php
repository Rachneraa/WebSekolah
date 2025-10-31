<?php
session_start();
include 'config/koneksi.php';

// Tampilkan notifikasi jika pendaftaran sukses (mendukung ?success=1 atau ?status=success)
if ((isset($_GET['success']) && $_GET['success'] == '1') || (isset($_GET['status']) && $_GET['status'] === 'success')) {
    echo "<script>
        window.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil!',
                text: 'Terima kasih, data Anda telah terkirim. Silakan tunggu konfirmasi dari admin.',
                confirmButtonText: 'OK'
            }).then(function() {
                // Setelah pengguna menekan OK, redirect ke halaman utama
                window.location.href = 'index.php';
            });
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir PPDB - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* CSS Variables */
        :root {
            --primary-orange: #ff8303;
            --primary-blue: #00499d;
            --dark-blue: #003366;
            --text-dark: #0f1724;
            --text-gray: #6b7280;
            --border-gray: #e5e7eb;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding-top: 0;
        }

        /* Hero Section for Form */
        .form-hero {
            position: relative;
            width: 100vw;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            min-height: 350px;
            background: url('assets/c.jpeg') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            overflow: hidden;
            padding: 80px 20px 60px;
            margin-top: -80px;
        }

        .form-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg,
                    rgba(0, 73, 157, 0.92) 0%,
                    rgba(31, 117, 216, 0.88) 100%);
            z-index: 1;
        }

        .form-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
        }

        .form-hero h1 {
            font-weight: 800;
            font-size: 42px;
            letter-spacing: 1px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease;
        }

        .form-hero p {
            font-size: 18px;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.2s both;
        }

        /* Form Container */
        .form-container {
            max-width: 900px;
            margin: -40px auto 80px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .form-wrapper {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: 1px solid var(--border-gray);
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .form-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 131, 3, 0.1), transparent);
            animation: pulse 4s ease-in-out infinite;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .form-header p {
            margin-top: 10px;
            opacity: 0.9;
            font-size: 15px;
            position: relative;
            z-index: 1;
        }

        /* Form Content */
        .form-content {
            padding: 50px 40px;
        }

        .form-step {
            margin-bottom: 40px;
        }

        .step-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary-orange);
            display: inline-block;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
            font-size: 15px;
        }

        .form-group label .required {
            color: var(--primary-orange);
            margin-left: 3px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            font-size: 16px;
            pointer-events: none;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 14px 18px 14px 50px;
            border: 2px solid var(--border-gray);
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 73, 157, 0.1);
        }

        .date-group {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .date-group select {
            padding-left: 18px;
        }

        .error-hint {
            color: var(--text-gray);
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .error-hint i {
            color: var(--primary-orange);
        }

        /* Submit Button */
        .submit-section {
            margin-top: 40px;
            text-align: center;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            color: white;
            border: none;
            padding: 16px 60px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 25px rgba(255, 131, 3, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 131, 3, 0.5);
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.05), rgba(255, 131, 3, 0.05));
            border-left: 4px solid var(--primary-orange);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .info-box h4 {
            color: var(--primary-blue);
            font-size: 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box ul {
            list-style: none;
            padding-left: 0;
        }

        .info-box ul li {
            padding: 5px 0;
            color: var(--text-gray);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box ul li i {
            color: var(--primary-orange);
            font-size: 12px;
        }

        /* Footer */
        .footer-bottom {
            background: linear-gradient(135deg, var(--dark-blue), #001a33);
            color: white;
            padding: 25px 20px;
            text-align: center;
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            margin-top: 0;
        }

        .footer-bottom p {
            opacity: 0.9;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-bottom .fa-heart {
            color: var(--primary-orange);
            animation: heartbeat 1.5s ease infinite;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 0.3;
            }

            50% {
                transform: scale(1.2) rotate(180deg);
                opacity: 0.6;
            }
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-hero {
                min-height: 280px;
                padding: 60px 20px 40px;
            }

            .form-hero h1 {
                font-size: 28px;
            }

            .form-hero p {
                font-size: 15px;
            }

            .form-container {
                margin: -30px auto 60px;
                padding: 0 15px;
            }

            .form-header {
                padding: 30px 20px;
            }

            .form-header h2 {
                font-size: 22px;
            }

            .form-content {
                padding: 35px 25px;
            }

            .step-title {
                font-size: 18px;
            }

            /* Tanggal lahir tetap sejajar di mobile */
            .date-group {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .date-group select {
                padding: 12px 8px;
                font-size: 13px;
            }

            .submit-btn {
                width: 100%;
                padding: 15px 40px;
            }
        }

        @media (max-width: 480px) {
            .form-hero h1 {
                font-size: 24px;
            }

            .form-content {
                padding: 30px 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            select {
                padding: 12px 15px 12px 45px;
                font-size: 14px;
            }

            .input-icon {
                left: 15px;
                font-size: 14px;
            }

            /* Tanggal lahir tetap 3 kolom di mobile kecil */
            .date-group {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }

            .date-group select {
                padding: 11px 6px;
                font-size: 12px;
            }
        }

        /* Untuk layar sangat kecil */
        @media (max-width: 360px) {
            .date-group {
                gap: 5px;
            }

            .date-group select {
                padding: 10px 4px;
                font-size: 11px;
            }
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>

    <!-- Hero Section -->
    <section class="form-hero">
        <div class="form-hero-content">
            <h1><i class="fas fa-graduation-cap"></i> Pendaftaran Peserta Didik Baru</h1>
            <p>Wujudkan masa depan gemilang bersama SMK TI Garuda Nusantara</p>
        </div>
    </section>

    <!-- Form Container -->
    <div class="form-container">
        <div class="form-wrapper">
            <div class="form-header">
                <h2>Formulir Pendaftaran PPDB 2025/2026</h2>
                <p>Silakan lengkapi data diri Anda dengan benar</p>
            </div>

            <div class="form-content">
                <!-- Info Box -->
                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Informasi Penting</h4>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Pastikan semua data yang diisi sudah benar</li>
                        <li><i class="fas fa-check-circle"></i> Isi sesuai dengan dokumen resmi (Ijazah/Rapor)</li>
                        <li><i class="fas fa-check-circle"></i> Nomor HP/WhatsApp akan digunakan untuk konfirmasi</li>
                    </ul>
                </div>

                <form id="ppdbForm" action="backend/modules/proses_pendaftaran.php" method="POST">
                    <!-- Data Pribadi -->
                    <div class="form-step">
                        <h3 class="step-title"><i class="fas fa-user"></i> Data Pribadi Calon Siswa</h3>

                        <div class="form-group">
                            <label for="namaLengkap">Nama Lengkap <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="namaLengkap" name="nama_lengkap"
                                    placeholder="Masukkan nama lengkap sesuai ijazah" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="jenisKelamin">Jenis Kelamin <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-venus-mars input-icon"></i>
                                <select id="jenisKelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="agama">Agama <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-pray input-icon"></i>
                                <select id="agama" name="agama" required>
                                    <option value="">-- Pilih Agama --</option>
                                    <option value="islam">Islam</option>
                                    <option value="kristen">Kristen</option>
                                    <option value="katolik">Katolik</option>
                                    <option value="hindu">Hindu</option>
                                    <option value="buddha">Buddha</option>
                                    <option value="konghucu">Konghucu</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tempatLahir">Tempat Kelahiran <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" id="tempatLahir" name="tempat_lahir" placeholder="Contoh: Jakarta"
                                    required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Kelahiran <span class="required">*</span></label>
                            <div class="date-group">
                                <select id="tanggal" name="tanggal" required>
                                    <option value="">Tanggal</option>
                                    <script>
                                        for (let i = 1; i <= 31; i++) {
                                            document.write(`<option value="${i.toString().padStart(2, '0')}">${i}</option>`);
                                        }
                                    </script>
                                </select>
                                <select id="bulan" name="bulan" required>
                                    <option value="">Bulan</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                <select id="tahun" name="tahun" required>
                                    <option value="">Tahun</option>
                                    <script>
                                        const currentYear = new Date().getFullYear();
                                        for (let i = currentYear - 5; i >= currentYear - 20; i--) {
                                            document.write(`<option value="${i}">${i}</option>`);
                                        }
                                    </script>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nisn">NISN <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-id-card input-icon"></i>
                                <input type="text" id="nisn" name="nisn" placeholder="Masukkan NISN" required
                                    maxlength="10" inputmode="numeric">
                            </div>
                        </div>
                    </div>

                    <!-- Data Kontak -->
                    <div class="form-step">
                        <h3 class="step-title"><i class="fas fa-phone"></i> Data Kontak</h3>

                        <div class="form-group">
                            <label for="alamatEmail">Alamat Email</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="alamatEmail" name="alamat_email" placeholder="contoh@email.com">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="noHp">Nomor HP/WhatsApp Orang Tua <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-mobile-alt input-icon"></i>
                                <input type="tel" id="noHp" name="no_hp" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>
                    </div>

                    <!-- Data Sekolah -->
                    <div class="form-step">
                        <h3 class="step-title"><i class="fas fa-school"></i> Data Sekolah & Jurusan</h3>

                        <div class="form-group">
                            <label for="namaSekolah">Nama Sekolah Asal <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-graduation-cap input-icon"></i>
                                <input type="text" id="namaSekolah" name="nama_sekolah"
                                    placeholder="Contoh: SMP Negeri 1 Jakarta" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="namaJurusan">Pilihan Jurusan <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-book input-icon"></i>
                                <select id="namaJurusan" name="jurusan" required>
                                    <option value="">-- Pilih Jurusan yang Diminati --</option>
                                    <option value="rpl">Rekayasa Perangkat Lunak (RPL)</option>
                                    <option value="tkj">Teknik Komputer dan Jaringan (TKJ)</option>
                                    <option value="animasi">Animasi</option>
                                    <option value="dkv">Desain Komunikasi Visual (DKV)</option>
                                    <option value="mp">Managemen Perkantoran (MP)</option>
                                    <option value="tjat">Teknik Jaringan Akses Telekomunikasi (TJAT)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="submit-section">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-bottom">
        <p>
            &copy; 2025 SMK TI Garuda Nusantara. All Rights Reserved.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let nisnAlerted = false;
        let nisnValid = true;
        const nisnInput = document.getElementById('nisn');
        const ppdbForm = document.getElementById('ppdbForm');
        let nisnDebounceTimer = null;
        const DEBOUNCE_MS = 400;

        function checkNISNFetch(nisn) {
            return fetch('backend/modules/cek_nisn.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'nisn=' + encodeURIComponent(nisn)
            }).then(response => response.json());
        }

        function handleDuplicate() {
            nisnValid = false;
            if (!nisnAlerted) {
                nisnAlerted = true;
                Swal.fire({
                    icon: 'error',
                    title: 'NISN sama',
                    text: 'NISN sudah terdaftar. Silakan gunakan NISN lain.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    nisnInput.value = '';
                    nisnInput.focus();
                    nisnAlerted = false;
                });
            }
        }

        nisnInput.addEventListener('input', function () {
            // Hanya angka dan maksimal 10 digit
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
            const nisn = this.value.trim();
            nisnValid = true; // reset optimistic
            clearTimeout(nisnDebounceTimer);
            if (nisn.length < 8) return;

            nisnDebounceTimer = setTimeout(() => {
                checkNISNFetch(nisn)
                    .then(data => {
                        if (data.status === 'exists') {
                            handleDuplicate();
                        } else {
                            nisnValid = true;
                        }
                    })
                    .catch(err => console.error('AJAX error:', err));
            }, DEBOUNCE_MS);
        });

        // Juga cek saat blur (user berhenti mengetik)
        nisnInput.addEventListener('blur', function () {
            clearTimeout(nisnDebounceTimer);
            const nisn = this.value.trim();
            if (nisn.length < 8) return;
            checkNISNFetch(nisn)
                .then(data => {
                    if (data.status === 'exists') {
                        handleDuplicate();
                    } else {
                        nisnValid = true;
                    }
                })
                .catch(err => console.error('AJAX error:', err));
        });

        ppdbForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const nisn = nisnInput.value.trim();
            if (nisn.length < 8) {
                nisnInput.focus();
                Swal.fire({
                    icon: 'error',
                    title: 'NISN tidak valid',
                    text: 'Pastikan NISN minimal 8 digit.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Final check sebelum submit untuk menghindari race
            checkNISNFetch(nisn)
                .then(data => {
                    if (data.status === 'exists') {
                        handleDuplicate();
                    } else {
                        // Lanjutkan submit jika valid
                        ppdbForm.submit();
                    }
                })
                .catch(err => {
                    console.error('AJAX error:', err);
                    // Kalau error jaringan, beri pilihan submit manual
                    Swal.fire({
                        icon: 'warning',
                        title: 'Terjadi kesalahan jaringan',
                        text: 'Tidak bisa memverifikasi NISN saat ini. Coba lagi.',
                        confirmButtonText: 'OK'
                    });
                });
        });
    </script>
</body>

</html>