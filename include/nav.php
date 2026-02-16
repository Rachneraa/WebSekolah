<?php
// WAJIB: Mulai session di baris paling atas
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="icons/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ==================== ROOT VARIABLES ==================== */
        :root {
            --primary-orange: #ff8303;
            --primary-blue: #00499d;
            --text-dark: #0f1724;
            --text-gray: #6b7280;
            --border-gray: #e6e9ee;
        }

        /* ==================== GLOBAL RESET ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent !important;
        }

        *:focus,
        *:active {
            outline: none !important;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            padding-top: 80px;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            -webkit-tap-highlight-color: transparent !important;
            -webkit-appearance: none;
            appearance: none;
            border: none;
            background: none;
            cursor: pointer;
        }

        /* ==================== LOADING SCREEN ==================== */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .loader-wrapper.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid #f0f0f0;
            border-top: 5px solid var(--primary-orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ==================== HEADER NAVBAR ==================== */
        header {
            background: #ffffff;
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border-gray);
            height: 80px;
        }

        header.scrolled {
            padding: 12px 60px;
            height: 70px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: 0.5px;
            cursor: pointer;
            color: var(--primary-blue);
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.02);
        }

        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        header.scrolled .logo-img {
            width: 40px;
            height: 40px;
        }

        /* Navigation */
        header nav {
            display: flex;
            align-items: center;
            gap: 30px;
            flex: 1;
        }

        header nav ul {
            list-style: none;
            display: flex;
            gap: 35px;
            margin: 0 auto;
            padding: 0;
        }

        header nav ul li {
            position: relative;
        }

        header nav ul li a {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-gray);
            transition: color 0.3s ease;
            padding: 8px 0;
            display: inline-block;
            position: relative;
        }

        header nav ul li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--primary-orange);
            transition: width 0.3s ease;
        }

        header nav ul li a:hover,
        header nav ul li a.active {
            color: var(--primary-orange);
        }

        header nav ul li a:hover::after,
        header nav ul li a.active::after {
            width: 100%;
        }

        /* Nav Buttons Container */
        .nav-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Register Button */
        .btn-register {
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            color: white;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 50px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 131, 3, 0.25);
            letter-spacing: 0.5px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 131, 3, 0.35);
        }

        /* Admin Button */
        .btn-admin {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-admin i {
            color: #fff;
            font-size: 1.2rem;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 73, 157, 0.3);
            background: #003d7a;
        }

        /* Hamburger Menu */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 28px;
            height: 24px;
            padding: 0;
            z-index: 1001;
        }

        .hamburger span {
            display: block;
            width: 100%;
            height: 3px;
            background: var(--text-dark);
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(8px, -8px);
        }

        /* ==================== LOGIN MODAL ==================== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .login-modal {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
            max-width: 420px;
            width: 90%;
            padding: 2.5rem;
            position: relative;
            transform: scale(0.9) translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .login-modal {
            transform: scale(1) translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f5f6fa;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }

        .modal-close:hover {
            background: #e6e9ee;
            transform: rotate(90deg);
        }

        .modal-close i {
            color: var(--text-dark);
            font-size: 1.2rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            display: block;
        }

        .login-header h2 {
            color: var(--primary-blue);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-gray);
            font-size: 14px;
            line-height: 1.5;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-gray);
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .input-group input:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 73, 157, 0.1);
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 15px 0 25px;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-blue);
        }

        .remember-me label {
            font-size: 14px;
            color: var(--text-gray);
            cursor: pointer;
            user-select: none;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary-blue), #003d7a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 73, 157, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 73, 157, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ==================== PASSWORD TOGGLE ICON ==================== */
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        /* Ini akan menimpa padding-right dari .input-group input */
        .password-wrapper input {
            /* Beri ruang di kanan untuk ikon */
            padding-right: 45px;
        }

        .password-wrapper i {
            position: absolute;
            top: 50%;
            right: 16px;
            /* Posisikan ikon di kanan */
            transform: translateY(-50%);
            color: var(--text-gray);
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .password-wrapper i:hover {
            color: var(--primary-blue);
        }

        /* ==================== LOGIN ERROR MESSAGE ==================== */
        .login-error-message {
            padding: 15px;
            background: #f8d7da;
            /* Merah muda */
            color: #721c24;
            /* Merah tua */
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            /* Samakan dengan radius input */
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
        }

        /* ==================== TABLET RESPONSIVE ==================== */
        @media (max-width: 1024px) {
            header {
                padding: 20px 40px;
            }

            header.scrolled {
                padding: 12px 40px;
            }

            header nav ul {
                gap: 25px;
            }
        }

        /* ==================== MOBILE RESPONSIVE ==================== */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            header {
                padding: 15px 25px;
                height: 70px;
            }

            header.scrolled {
                padding: 12px 25px;
                height: 60px;
            }

            .logo {
                font-size: 16px;
                gap: 10px;
            }

            .logo-img {
                width: 42px;
                height: 42px;
            }

            header.scrolled .logo-img {
                width: 36px;
                height: 36px;
            }

            .hamburger {
                display: flex;
            }

            header nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 75%;
                max-width: 300px;
                height: 100vh;
                background: #ffffff;
                flex-direction: column;
                align-items: flex-start;
                padding: 100px 30px 30px;
                gap: 0;
                box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
                transition: right 0.3s ease;
                overflow-y: auto;
            }

            header nav.active {
                right: 0;
            }

            header nav ul {
                flex-direction: column;
                gap: 0;
                width: 100%;
            }

            header nav ul li {
                width: 100%;
                border-bottom: 1px solid var(--border-gray);
            }

            header nav ul li:last-child {
                border-bottom: none;
            }

            header nav ul li a {
                font-size: 16px;
                color: var(--text-dark);
                padding: 18px 0;
                display: block;
                width: 100%;
            }

            header nav ul li a::after {
                display: none;
            }

            .btn-register {
                width: calc(100% - 40px);
                margin: 0 20px;
                text-align: center;
            }

            .login-label-mobile {
                display: block;
                text-align: center;
                color: var(--primary-blue);
                font-size: 13px;
                font-weight: 500;
                margin-top: 5px;
                margin-bottom: 10px;
            }

            .btn-admin {
                margin: 10px auto;
                width: 45px;
                height: 45px;
            }

            .btn-admin i {
                font-size: 1.4rem;
            }

            .nav-buttons {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                margin-top: 20px;
            }

            .btn-register {
                width: 100%;
                text-align: center;
            }

            .login-modal {
                padding: 2rem;
            }

            .login-header h2 {
                font-size: 20px;
            }
        }

        /* ==================== SMALL MOBILE ==================== */
        @media (max-width: 480px) {
            header {
                padding: 12px 20px;
                height: 65px;
            }

            header.scrolled {
                padding: 10px 20px;
                height: 55px;
            }

            .logo {
                font-size: 14px;
                gap: 8px;
            }

            .logo-img {
                width: 36px;
                height: 36px;
            }

            header.scrolled .logo-img {
                width: 30px;
                height: 30px;
            }

            .hamburger {
                width: 24px;
                height: 20px;
            }

            .hamburger span {
                height: 2.5px;
            }

            header nav {
                width: 80%;
                padding: 80px 25px 25px;
            }

            .login-modal {
                padding: 1.5rem;
            }

            .login-logo {
                width: 70px;
                height: 70px;
            }
        }

        /* Tombol full width di mobile */
        @media (max-width: 768px) {
            .nav-buttons {
                flex-direction: column;
                gap: 18px;
                width: 100%;
                padding: 0;
            }

            .btn-block {
                width: 100%;
                box-sizing: border-box;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                padding: 14px 0;
                border-radius: 30px;
                margin: 0;
            }

            .btn-register.btn-block {
                background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
                color: white;
                font-weight: 600;
                box-shadow: 0 4px 15px rgba(255, 131, 3, 0.25);
                letter-spacing: 0.5px;
                gap: 8px;
            }

            .btn-admin.btn-block {
                background: var(--primary-blue);
                color: white;
                font-weight: 600;
                gap: 8px;
                box-shadow: 0 4px 12px rgba(0, 73, 157, 0.3);
                border: none;
            }

            .btn-admin.btn-block i {
                font-size: 1.2rem;
            }

            .btn-admin.btn-block .login-label {
                font-size: 15px;
                font-weight: 500;
            }
        }

        /* Desktop: tombol tetap seperti semula */
        @media (min-width: 769px) {
            .btn-block {
                width: auto;
                padding: 12px 28px;
                font-size: 14px;
                border-radius: 50px;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-admin.btn-block .login-label {
                color: white;
                font-size: 14px;
                font-weight: 500;
                margin-left: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="loader-wrapper" id="mainLoader">
        <div class="loader"></div>
    </div>

    <header id="header">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="assets/logo.png" alt="Logo SMK TI Garuda Nusantara" class="logo-img">
            <span class="logo-text">SMK TI Garuda Nusantara</span>
        </div>

        <nav id="nav">
            <ul>
                <li><a href="index.php#beranda">Beranda</a></li>
                <li><a href="index.php#jurusan-program">Jurusan</a></li>
                <li><a href="index.php#artikel">Artikel</a></li>
                <li><a href="ppdb.php">PPDB</a></li>
                <li><a href="contact.php">Kontak</a></li>
            </ul>
            <div class="nav-buttons">
                <a href="pendaftaran.php" class="btn-register btn-block">Daftar</a>
                <button class="btn-admin btn-block" id="loginBtn">
                    <i class="fas fa-user"></i>
                    <span class="login-label"><b>Login</b></span>
                </button>
            </div>
        </nav>

        <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <div class="modal-overlay" id="loginModal">
        <div class="login-modal">
            <button class="modal-close" id="closeModal">
                <i class="fas fa-times"></i>
            </button>

            <div class="login-header">
                <img src="assets/smk.png" alt="Logo Sekolah" class="login-logo">
                <h2>SMK TI Garuda Nusantara</h2>
                <p>Selamat datang di<br>Portal Login Sekolah</p>
            </div>

            <?php
            if (isset($_SESSION['error'])) {
                // Tampilkan pesan error
                echo '<div class="login-error-message">';
                echo htmlspecialchars($_SESSION['error']);
                echo '</div>';
                // Session akan di-unset di bagian JavaScript
            }
            ?>

            <form id="loginForm" action="config/process_login.php" method="POST" novalidate>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username / NIP" required>
                </div>

                <div class="input-group">
                    <label>Kata Sandi</label>

                    <div class="password-wrapper">
                        <input type="password" name="password" id="loginPassword" placeholder="Kata Sandi" required>
                        <i class="fas fa-eye-slash" id="togglePassword"></i>
                    </div>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="rememberMe" name="rememberMe">
                    <label for="rememberMe">Ingat Saya</label>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburger = document.getElementById('hamburger');
            const nav = document.getElementById('nav');
            const header = document.getElementById('header');
            const loader = document.getElementById('mainLoader');
            const loginBtn = document.getElementById('loginBtn');
            const loginModal = document.getElementById('loginModal');
            const closeModal = document.getElementById('closeModal');
            const loginForm = document.getElementById('loginForm');

            // ===================================================================
            // == PERBAIKAN 2: JAVASCRIPT VALIDASI KUSTOM
            // ===================================================================
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('loginPassword');
            // Cari kotak error PHP. Kita akan gunakan ulang
            let phpErrorBox = document.querySelector('.login-error-message');

            // Fungsi untuk menampilkan pesan error
            function showError(message) {
                event.preventDefault(); // Hentikan form

                if (phpErrorBox) {
                    // Jika kotak error sudah ada, ganti pesannya
                    phpErrorBox.innerHTML = message;
                    phpErrorBox.style.display = 'block';
                } else {
                    // Jika tidak ada (misal, tidak ada error PHP), buat baru
                    const newErrorBox = document.createElement('div');
                    newErrorBox.className = 'login-error-message'; // Pakai style yg sama
                    newErrorBox.innerHTML = message;
                    newErrorBox.style.display = 'block';

                    // Masukkan sebelum form
                    loginForm.parentNode.insertBefore(newErrorBox, loginForm);

                    // Simpan referensi ke error box baru ini
                    phpErrorBox = newErrorBox;
                }
            }

            loginForm.addEventListener('submit', function (event) {

                const username = usernameInput.value.trim();
                const password = passwordInput.value.trim();

                // Sembunyikan error lama (jika ada) saat submit ulang
                if (phpErrorBox) {
                    phpErrorBox.style.display = 'none';
                }

                // Logika Validasi Kustom
                if (username === '' && password === '') {
                    showError('Username dan Kata Sandi tidak boleh kosong.');
                } else if (username === '') {
                    showError('Username tidak boleh kosong.');
                } else if (password === '') {
                    showError('Kata Sandi tidak boleh kosong.');
                }
                // Jika semua terisi, biarkan form ter-submit
            });
            // Pindah fokus ke password jika user menekan Enter saat di field username
            if (usernameInput) {
                usernameInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (passwordInput) passwordInput.focus();
                    }
                });
            }
            // ==================== AKHIR PERBAIKAN 2 ====================


            // ==================== BARU: SHOW/HIDE PASSWORD ====================
            const togglePassword = document.getElementById('togglePassword');

            // Pastikan elemennya ada sebelum menambahkan listener
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    // Toggle tipe input
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle ikon
                    if (type === 'password') {
                        // Jika tipe password (tersembunyi)
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    } else {
                        // Jika tipe text (terlihat)
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    }
                });
            }
            // ==================== AKHIR KODE BARU ====================


            // ==================== LOGIN MODAL ====================
            loginBtn.addEventListener('click', function (e) {
                e.preventDefault();
                loginModal.classList.add('active');
                document.body.style.overflow = 'hidden';
                // Fokus ke username saat modal terbuka
                setTimeout(function () {
                    if (usernameInput) usernameInput.focus();
                }, 50);
            });

            closeModal.addEventListener('click', function () {
                loginModal.classList.remove('active');
                document.body.style.overflow = '';
            });

            // Close modal when clicking overlay
            loginModal.addEventListener('click', function (e) {
                if (e.target === loginModal) {
                    loginModal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // ==================== PENANGANAN LOGIN ERROR (SUDAH DIPERBAIKI) ====================
            <?php
            // Jika ada session error (dideteksi oleh PHP)
            if (isset($_SESSION['error'])) {

                // PHP akan "mencetak" kode JavaScript ini
                // untuk membuka modal secara otomatis
                echo "loginModal.classList.add('active');\n";
                echo "document.body.style.overflow = 'hidden';\n";
                echo "setTimeout(function(){ if (typeof usernameInput !== 'undefined' && usernameInput) usernameInput.focus(); }, 50);\n";

                // Setelah modal dipastikan terbuka, hapus session error
                // agar tidak muncul lagi jika halaman di-refresh manual
                unset($_SESSION['error']);
            }
            ?>
            // ==================== AKHIR KODE BARU ====================


            // ==================== HAMBURGER MENU ====================
            hamburger.addEventListener('click', function (e) {
                e.stopPropagation();
                hamburger.classList.toggle('active');
                nav.classList.toggle('active');
                document.body.style.overflow = nav.classList.contains('active') ? 'hidden' : '';
            });

            document.addEventListener('click', function (e) {
                if (nav.classList.contains('active') &&
                    !nav.contains(e.targe) &&
                    !hamburger.contains(e.target)) {
                    hamburger.classList.remove('active');
                    nav.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            const navLinks = nav.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        hamburger.classList.remove('active');
                        nav.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            });

            // ==================== SCROLL EFFECT ====================
            window.addEventListener('scroll', function () {
                if (window.scrollY > 20) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
            });

            // ==================== ACTIVE NAV LINK ====================
            const currentPage = window.location.pathname.split('/').pop() || 'index.php';
            navLinks.forEach(link => {
                const linkPage = link.getAttribute('href').split('#')[0];
                if (linkPage === currentPage ||
                    (currentPage === '' && linkPage === 'index.php')) {
                    link.classList.add('active');
                }
            });

            // ==================== LOADING SCREEN ====================
            window.addEventListener('load', function () {
                setTimeout(() => {
                    loader.classList.add('hidden');
                    setTimeout(() => loader.remove(), 500);
                }, 500);
            });

            // ==================== RESIZE HANDLER ====================
            window.addEventListener('resize', function () {
                if (window.innerWidth > 768) {
                    nav.classList.remove('active');
                    hamburger.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // ==================== SERVICE WORKER ====================
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('//service-worker.js')
                    .then(registration => {
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    if (confirm('Versi baru tersedia. Update sekarang?')) {
                                        newWorker.postMessage('skipWaiting');
                                        window.location.reload();
                                    }
                                }
                            });
                        });
                    })
                    .catch(err => console.log('Service Worker registration failed:', err));
            });
        }
    </script>
</body>

</html>