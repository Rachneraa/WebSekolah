<!DOCTYPE html>
<html lang="id">

<head>
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            flex: 1; /* biarkan nav mengambil ruang tengah antara logo dan hamburger */
        }

        /* Center the nav links */
        header nav ul {
            list-style: none;
            display: flex;
            gap: 35px;
            margin: 0 auto; /* <- ini yang membuat link berada di tengah */
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

        /* Hamburger Menu (Hidden on Desktop) */
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

            /* Show Hamburger */
            .hamburger {
                display: flex;
            }

            /* Mobile Navigation */
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

            /* Mobile Register Button */
            .btn-register {
                width: 100%;
                justify-content: center;
                margin-top: 30px;
                padding: 15px;
                font-size: 15px;
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
        }
    </style>
</head>

<body>
    <!-- Loading Screen -->
    <div class="loader-wrapper" id="mainLoader">
        <div class="loader"></div>
    </div>

    <!-- Header Navigation -->
    <header id="header">
        <div class="logo" onclick="window.location.href='index.php'">
            <img src="/pkl/assets/logo.png" alt="Logo SMK TI Garuda Nusantara" class="logo-img">
            <span class="logo-text">SMK TI Garuda Nusantara</span>
        </div>

        <nav id="nav">
            <ul>
                <li><a href="index.php#beranda">Beranda</a></li>
                <li><a href="index.php#jurusan-program">Jurusan</a></li>
                <li><a href="index.php#artikel">Artikel</a></li>
                <li><a href="ppdb.php">PPDB</a></li>
                <li><a href="#kontak">Kontak</a></li>
            </ul>
            <button class="btn-register" onclick="window.location.href='pendaftaran.php'">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </nav>

        <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.getElementById('hamburger');
            const nav = document.getElementById('nav');
            const header = document.getElementById('header');
            const loader = document.getElementById('mainLoader');

            // ==================== HAMBURGER MENU ====================
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                hamburger.classList.toggle('active');
                nav.classList.toggle('active');
                document.body.style.overflow = nav.classList.contains('active') ? 'hidden' : '';
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (nav.classList.contains('active') && 
                    !nav.contains(e.target) && 
                    !hamburger.contains(e.target)) {
                    hamburger.classList.remove('active');
                    nav.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });

            // Close menu when clicking nav links
            const navLinks = nav.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        hamburger.classList.remove('active');
                        nav.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            });

            // ==================== SCROLL EFFECT ====================
            window.addEventListener('scroll', function() {
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
            window.addEventListener('load', function() {
                setTimeout(() => {
                    loader.classList.add('hidden');
                    setTimeout(() => loader.remove(), 500);
                }, 500);
            });

            // ==================== RESIZE HANDLER ====================
            window.addEventListener('resize', function() {
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
                navigator.serviceWorker.register('/pkl/service-worker.js')
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