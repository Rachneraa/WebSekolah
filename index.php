<?php
session_start();
require_once 'config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMK TI Garuda Nusantara - Sekolah Unggulan Teknologi</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="/Web-sekolah/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/Web-sekolah/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/Web-sekolah/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/Web-sekolah/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Variables */
        :root {
            --primary-orange: #ff8303;
            --primary-blue: #00499d;
            --text-dark: #0f1724;
            --text-gray: #6b7280;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            padding-top: 0;
            /* Hapus padding-top */
            overflow-x: hidden;
            /* Add padding for fixed header */
        }



        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ========================================
        HERO SECTION - PROFESSIONAL WHITE
        ======================================== */
        .hero {
            position: relative;
            width: 100vw;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            min-height: 100vh;
            /* Ubah ke 100vh */
            background: url('assets/c.jpeg') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--text-dark);
            text-align: center;
            overflow: hidden;
            border-bottom: 1px solid var(--border-gray);
            padding: 80px 20px;
            margin-top: -80px;
            /* Tambahkan margin negative sebesar tinggi header */
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg,
                    #0A3C96 0%,
                    #1557B0 50%,
                    #1F75D8 100%);
            opacity: 0.88;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            padding: 0 20px;
        }

        .hero h1 {
            font-weight: 800;
            font-size: 56px;
            letter-spacing: 2px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.05);
            animation: fadeInUp 1s ease 0.2s both;
            color: white;
        }

        .hero p {
            font-weight: 400;
            font-size: 20px;
            margin: 15px auto 0;
            max-width: 700px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.05);
            animation: fadeInUp 1s ease 0.4s both;
            text-align: center;
            line-height: 1.6;
            padding: 0 20px;
            color: white;

        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            margin-top: 30px;
            animation: fadeInUp 1s ease 0.6s both;
            justify-content: center;
        }

        .btn-hero {
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-primary {
            background: var(--primary-orange);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 131, 3, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 131, 3, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            box-shadow: 0 4px 15px rgba(0, 73, 157, 0.15);
        }

        .btn-secondary:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 73, 157, 0.25);
        }

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

        /* Floating Icons */
        .floating-icon {
            position: absolute;
            font-size: 40px;
            opacity: 0.05;
            animation: float 6s ease-in-out infinite;
            color: var(--primary-blue);
        }

        .floating-icon:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-icon:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: 2s;
        }

        .floating-icon:nth-child(3) {
            bottom: 15%;
            left: 20%;
            animation-delay: 4s;
        }

        .floating-icon:nth-child(4) {
            bottom: 20%;
            right: 10%;
            animation-delay: 3s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* ========================================
        STATS SECTION - WHITE THEME
        ======================================== */
        .stats {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            display: flex;
            justify-content: center;
            gap: 80px;
            padding: 50px 20px;
            color: var(--text-dark);
            border-top: 1px solid var(--border-gray);
            border-bottom: 1px solid var(--border-gray);
        }

        .stat-item {
            text-align: center;
            position: relative;
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-10px);
        }

        .stat-item i {
            font-size: 40px;
            color: var(--primary-orange);
            margin-bottom: 10px;
        }

        .stat-item .number {
            display: block;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 5px;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-item .label {
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--text-gray);
        }

        /* ========================================
        MAIN CONTENT
        ======================================== */
        main {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .section-title {
            font-size: 36px;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 40px;
            margin top: 50px;
            position: relative;
            display: inline-block;
        }

        .section-title::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 60px;
            height: 5px;
            background: var(--primary-orange);
            border-radius: 3px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 70px;
            bottom: -10px;
            width: 30px;
            height: 5px;
            background: var(--primary-orange);
            opacity: 0.5;
            border-radius: 3px;
        }

        /* ========================================
        ABOUT SECTION
        ======================================== */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            margin-bottom: 60px;
        }

        .about-text p {
            font-size: 16px;
            color: var(--text-gray);
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .about-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .about-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .about-image:hover img {
            transform: scale(1.1);
        }

        .about-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.3), rgba(255, 131, 3, 0.3));
            z-index: 1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .about-image:hover::before {
            opacity: 1;
        }

        /* ========================================
        SAMBUTAN KEPALA SEKOLAH
        ======================================== */
        .sambutan-section {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            padding: 60px;
            border-radius: 20px;
            margin-bottom: 80px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-gray);
        }

        .sambutan-container {
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .sambutan-img {
            position: relative;
            flex-shrink: 0;
        }

        .sambutan-img img {
            width: 180px;
            height: 220px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: var(--shadow);
            border: 5px solid white;
        }

        .sambutan-badge {
            position: absolute;
            bottom: -15px;
            right: -15px;
            background: var(--primary-orange);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(255, 131, 3, 0.4);
        }

        .sambutan-content h3 {
            font-size: 24px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .sambutan-content p {
            font-size: 15px;
            color: var(--text-gray);
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .sambutan-signature {
            margin-top: 20px;
            font-weight: 600;
            color: var(--primary-blue);
        }

        /* ========================================
         JURUSAN SECTION - WHITE PROFESSIONAL
        ======================================== */
        .jurusan-program-section {
            background: white;
            /* Changed from gradient to solid white */
            padding: 80px 0;
            position: relative;
            overflow: hidden;
            margin: 0 -40px 80px -40px;
            border-top: 1px solid var(--border-gray);
            border-bottom: 1px solid var(--border-gray);
        }

        .jurusan-program-section::before {
            content: '';
            /* Removed the radial gradient background */
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            /* Removed background: radial-gradient(circle, rgba(0, 73, 157, 0.03), transparent); */
            border-radius: 50%;
            pointer-events: none;
        }

        .jurusan-program-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
            position: relative;
            z-index: 1;
        }

        .jurusan-program-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .jurusan-program-title {
            font-size: 42px;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 15px;
            letter-spacing: -0.5px;
            position: relative;
            display: inline-block;
        }

        .jurusan-program-title::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-orange));
            border-radius: 2px;
        }

        .jurusan-program-subtitle {
            font-size: 18px;
            color: #666;
            font-weight: 400;
            margin-top: 25px;
            line-height: 1.6;
        }

        /* Slider Wrapper */
        .jurusan-slider-wrapper {
            position: relative;
            margin-bottom: 40px;
        }

        /* Grid for Desktop */
        .jurusan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        /* Navigation Buttons */
        .slider-nav {
            display: none;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            f background: white;
            border: 2px solid var(--border-gray);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            z-index: 10;
            color: var(--primary-blue);
            font-size: 20px;
            align-items: center;
            justify-content: center;
        }

        .slider-nav:hover {
            background: var(--primary-orange);
            color: white;
            border-color: var(--primary-orange);
            transform: translateY(-50%) scale(1.1);
        }

        .slider-nav:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .slider-nav.prev {
            left: -25px;
        }

        .slider-nav.next {
            right: -25px;
        }

        /* Slider Dots */
        .slider-dots {
            display: none;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(0, 73, 157, 0.2);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .slider-dot.active {
            background: var(--primary-blue);
            width: 30px;
            border-radius: 6px;
        }

        /* Jurusan Card */
        .jurusan-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .jurusan-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.02), rgba(255, 131, 3, 0.02));
            opacity: 0;
            transition: opacity 0.4s ease;
            border-radius: 20px;
        }

        .jurusan-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-orange);
        }

        .jurusan-card:hover::before {
            opacity: 1;
        }

        .jurusan-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            position: relative;
            z-index: 1;
            transition: transform 0.4s ease;
        }

        .jurusan-card:hover .jurusan-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .jurusan-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 5px 15px rgba(0, 73, 157, 0.2));
        }

        /* Jurusan Content */
        .jurusan-content {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .jurusan-content h3 {
            font-size: 19px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 12px;
            line-height: 1.4;
            position: relative;
            z-index: 1;
            transition: color 0.3s ease;
            min-height: 50px;
            width: 100%;
        }

        .jurusan-card:hover .jurusan-content h3 {
            color: var(--dark-blue);
        }

        .jurusan-content p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
            position: relative;
            z-index: 1;
            margin-bottom: 15px;
            width: 100%;
        }

        .jurusan-content ul {
            list-style: none;
            padding: 0;
            margin: 0 auto 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
            width: 100%;
        }

        .jurusan-content ul li {
            position: relative;
            padding-left: 25px;
            color: #444;
            font-size: 13px;
            line-height: 1.5;
            text-align: left;
            width: 100%;
            max-width: 200px;
            display: block;
        }

        .jurusan-content ul li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 0;
            color: var(--primary-orange);
            font-size: 16px;
            font-weight: bold;
        }

        .btn-detail {
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            align-self: center;
            box-shadow: 0 4px 15px rgba(255, 131, 3, 0.25);
            margin-top: auto;
        }

        .btn-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 131, 3, 0.35);
        }

        .jurusan-motivasi {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: #FFFFFF;
            padding: 30px 40px;
            border-radius: 20px;
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            line-height: 1.6;
            box-shadow: 0 10px 30px rgba(0, 73, 157, 0.2);
            position: relative;
            overflow: hidden;
        }

        .jurusan-motivasi::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 131, 3, 0.1), transparent);
            animation: pulse 4s ease-in-out infinite;
        }

        .jurusan-motivasi p {
            position: relative;
            z-index: 1;
            margin: 0;
        }

        .highlight-text {
            color: var(--primary-orange);
            font-weight: 800;
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


        /* ========== Ekstrakurikuler - full‑bleed update ========== */
        /* ========================================
   EKSTRAKURIKULER SECTION (RESPONSIVE & SPECIAL)
   ======================================== */
:root {
    /* Pastikan variabel ini ada di atas */
    --primary-orange: #ff8303;
    --primary-blue: #00499d;
    --text-dark: #0f1724;
    --text-gray: #6b7280;
}

/* Animasi Pulsate untuk Logo */
@keyframes pulsate {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 73, 157, 0.1);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(0, 73, 157, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0, 73, 157, 0);
    }
}

.ekstrakurikuler-section {
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    width: 100vw;
    background: #f3f6f9;
    padding: 80px 0;
    overflow: hidden;
    font-family: 'Poppins', Inter, sans-serif;
    z-index: 0;
}

.ekstrakurikuler-inner {
    position: relative;
    z-index: 2;
    max-width: 1100px;
    margin: 0 auto;
    padding: 30px;
    background: #ffffff;
    border-radius: 28px;
    box-shadow: 0 18px 50px rgba(8, 30, 52, 0.08); 
    border: 2px solid #e0e7ff; 
    display: flex;
    flex-direction: column;
    gap: 24px;
    align-items: center;
    justify-content: center;
}

.ekstrakurikuler-title {
    text-align: center;
    color: var(--primary-blue);
    font-size: 40px;
    font-weight: 700;
    margin: 0;
    letter-spacing: 0.6px;
    position: relative;
    padding-bottom: 10px;
}

.ekstrakurikuler-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: var(--primary-orange); 
    border-radius: 2px;
}

.ekstrakurikuler-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    width: 100%;
    align-items: center;
    justify-items: center;
    max-width: 960px;
}

/* LEFT - Logo Carousel Block */
.ekstrakurikuler-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    width: 100%;
}

.ekstrakurikuler-carousel {
    position: relative;
    width: 260px;
    height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ekskul-logo {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(8, 30, 52, 0.08);
    border: 6px solid rgba(0, 73, 157, 0.1); 
    transition: transform 0.35s ease;
    animation: pulsate 3s infinite ease-out;
}

.ekskul-logo img {
    width: 88%;
    height: 88%;
    object-fit: contain;
}

.ekskul-meta {
    text-align: center;
    margin-top: 2px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.ekskul-meta .name {
    font-size: 19px; 
    font-weight: 800;
    color: var(--primary-blue);
    width: 100%;
    text-align: center;
}

.ekskul-meta .subtitle {
    font-size: 13px; 
    color: var(--text-gray);
    margin-top: 4px;
    letter-spacing: 1px;
    width: 100%;
    text-align: center;
}

/* Navigasi Buttons */
.ekstrakurikuler-carousel .ekstrakurikuler-nav {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between;
    width: 100%;
    padding: 0 8px;
    pointer-events: none;
}

.ekstrakurikuler-carousel .ekstrakurikuler-nav button {
    pointer-events: all;
    width: 40px; 
    height: 40px;
    border-radius: 50%;
    border: 3px solid white; 
    background: var(--primary-blue);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(0, 73, 157, 0.3);
    transition: all 0.2s ease;
    font-size: 16px;
}

.ekstrakurikuler-carousel .ekstrakurikuler-nav button:hover {
    background: var(--primary-orange);
    box-shadow: 0 10px 25px rgba(255, 131, 3, 0.4);
    transform: scale(1.1);
}

/* RIGHT - Deskripsi dan Detail Styling */

/* Menyembunyikan Ilustrasi Besar di Kanan (FIXED) */
.ekstrakurikuler-illustration, 
#ekskulIllustration {
    display: none !important; 
    width: 0; /* Pastikan tidak memakan ruang */
    height: 0;
}

/* Deskripsi */
.ekskul-description {
    transition: opacity 0.3s ease-in-out; 
    padding: 20px 0;
}

.ekskul-description h3 {
    font-size: 24px;
    font-weight: 800;
    color: var(--primary-blue);
    margin-bottom: 10px;
    line-height: 1.3;
}

.ekskul-description p {
    font-size: 15px;
    color: var(--text-gray);
    line-height: 1.7;
    margin-bottom: 20px;
}

/* Detail Points */
.ekskul-details {
    display: flex;
    flex-direction: column; 
    gap: 5px; 
    margin-top: 15px;
    padding-left: 0;
    list-style: none; 
    text-align: left; 
}

.ekskul-details .detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-dark);
    padding: 8px 0;
    border-bottom: 1px dashed #e0e0e0;
}

.ekskul-details .detail-item:last-child {
    border-bottom: none;
}

.ekskul-details .detail-item i {
    color: var(--primary-orange); 
    font-size: 18px;
    min-width: 18px;
    text-align: center;
}

/* ========================================
   RESPONSIVE OVERRIDES (MOBILE FIXES)
   ======================================== */

@media (max-width: 980px) {
     .ekstrakurikuler-inner {
        padding: 20px;
    }
}
 
@media (max-width: 768px) {
    /* Menghilangkan layout grid 1fr 1fr dan memaksa tumpukan vertikal */
    .ekstrakurikuler-content {
        display: flex; /* Ganti dari grid ke flex */
        flex-direction: column; 
        gap: 18px; 
        align-items: center;
        justify-content: center;
        max-width: 100%; /* Lebarkan max-width untuk mobile */
    }
    
    /* Mengatasi masalah width */
    .ekstrakurikuler-left,
    .ekstrakurikuler-right {
        width: 100%;
        max-width: 400px; /* Batasi maksimal agar tidak terlalu lebar di tablet */
    }

    .ekstrakurikuler-left {
        order: 1; 
    }
    
    .ekstrakurikuler-right {
        order: 2; 
        text-align: center;
        padding: 0 10px; /* Tambahkan padding agar deskripsi tidak menempel ke pinggir */
    }
    
    .ekskul-description h3 {
        font-size: 20px;
    }
    
    .ekskul-description p {
        font-size: 14px;
    }

    .ekskul-details {
        align-items: center; 
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 10px; /* Adjust padding for list items */
    }
    .ekskul-details .detail-item {
        justify-content: flex-start;
        width: 100%;
        padding: 6px 0;
    }
    
    .ekstrakurikuler-carousel {
        width: 200px; /* Kurangi ukuran logo carousel di mobile */
        height: 200px;
    }
    
    .ekskul-logo {
        width: 120px;
        height: 120px;
        border-width: 4px;
    }
}

@media (max-width: 480px) {
    .ekstrakurikuler-title {
        font-size: 30px;
    }
    .ekstrakurikuler-inner {
        padding: 16px;
        border-radius: 18px;
    }
}

        /* ========== end Ekstrakurikuler styles ========== */

        /* ========================================
        ARTIKEL SECTION
        ======================================== */
        .artikel-container {
            position: relative;
            margin-bottom: 100px;
            margin-top: 50px;
        }

        .artikel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .btn-view-all {
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 73, 157, 0.25);
        }

        .btn-view-all:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 73, 157, 0.35);
        }

        .artikel-slider {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .artikel-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid var(--border-gray);
        }

        .artikel-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .artikel-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .artikel-card:hover img {
            transform: scale(1.1);
        }

        .artikel-content {
            padding: 25px;
        }

        .artikel-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 12px;
            color: var(--text-gray);
        }

        .artikel-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .artikel-content h3 {
            font-size: 18px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-weight: 700;
            line-height: 1.4;
        }

        .artikel-content p {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .artikel-link {
            color: var(--primary-orange);
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
        }

        .artikel-link:hover {
            gap: 10px;
        }

        /* ========================================
        FOOTER - DARK PROFESSIONAL
        ======================================== */
        footer {
            background: linear-gradient(135deg, var(--dark-blue), #001a33);
            color: white;
            padding: 60px 20px 30px;
            /* Kurangi padding horizontal */
            width: 100vw;
            /* Full viewport width */
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            padding: 0 20px;
            /* Tambah padding untuk konten */
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Center content */
            text-align: center;
        }

        .footer-section h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--primary-orange);
            text-align: center;
            /* Center headings */
        }

        .footer-section p {
            font-size: 14px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            /* Center paragraphs */
        }

        .footer-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            /* Center logo and text */
            gap: 12px;
            margin-bottom: 15px;
        }

        .footer-section ul {
            list-style: none;
            width: 100%;
            text-align: center;
            /* Center list items */
        }

        .footer-section ul li {
            margin-bottom: 12px;
            text-align: center;
        }

        .footer-section ul li a {
            justify-content: center;
            /* Center link content */
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-section.footer-contact ul li a {
            justify-content: center;
            /* Center contact info */
        }

        /* Keep social links left-aligned */
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            /* Remove justify-content: center to keep social icons left-aligned */
        }

        .recent-post-item {
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .recent-post-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .recent-post-item a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            line-height: 1.5;
            transition: all 0.3s ease;
        }

        .recent-post-item a:hover {
            color: var(--primary-orange);
            padding-left: 5px;
        }

        .recent-post-date {
            display: block;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .social-links a:hover {
            background: var(--primary-orange);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        /* ========================================
        SCROLL TO TOP BUTTON
        ======================================== */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255, 131, 3, 0.4);
            z-index: 999;
        }

        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(255, 131, 3, 0.5);
        }

        /* ========================================
        UTILITY CLASSES
        ======================================== */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        button,
        .btn-detail {
            position: relative;
            overflow: hidden;
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* ========================================
        RESPONSIVE DESIGN
        ======================================== */
        @media (max-width: 1024px) {
            header {
                padding: 20px 30px;
            }

            .logo {
                font-size: 16px;
            }

            .logo-img {
                width: 45px;
                height: 45px;
            }

            .footer-content {
                grid-template-columns: 2fr 1fr 1fr;
            }

            .footer-section:last-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }

            .logo {
                font-size: 14px;
                gap: 10px;
            }

            .logo-img {
                width: 38px;
                height: 38px;
            }

            header nav {
                position: fixed;
                top: 0;
                right: -100%;
                width: 70%;
                height: 100vh;
                background: #ffffff;
                padding: 80px 30px 30px;
                transition: right 0.3s ease;
                box-shadow: -5px 0 25px rgba(0, 0, 0, 0.15);
                overflow-y: auto;
            }

            header nav.active {
                right: 0;
            }

            header nav ul {
                flex-direction: column;
                gap: 25px;
            }

            header nav ul li a {
                font-size: 18px;
                color: var(--text-dark);
            }

            .btn-register {
                display: none;
            }

            .mobile-register-btn {
                display: block;
            }

            .hero {
                min-height: 500px;
                padding: 60px 20px;
            }

            .hero-content {
                padding: 0 15px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .hero p {
                font-size: 15px;
                line-height: 1.6;
                padding: 0 10px;
                margin: 15px auto 0;
            }

            .hero-buttons {
                flex-direction: column;
                width: 100%;
                max-width: 280px;
                margin: 25px auto 0;
            }

            .btn-hero {
                width: 100%;
                padding: 14px 30px;
                font-size: 14px;
            }

            .stats {
                flex-direction: row;
                gap: 20px;
                padding: 40px 15px;
                justify-content: space-around;
            }

            .stat-item {
                padding: 10px 5px;
                flex: 1;
            }

            .stat-item i {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .stat-item .number {
                font-size: 32px;
                margin-bottom: 3px;
            }

            .stat-item .label {
                font-size: 11px;
                letter-spacing: 0.3px;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .about-image img {
                height: 300px;
            }

            .sambutan-container {
                flex-direction: column;
                text-align: center;
                align-items: center;
            }

            .sambutan-section {
                padding: 30px 20px;
            }

            .sambutan-img img {
                width: 150px;
                height: 180px;
            }

            .sambutan-content h3 {
                font-size: 20px;
            }

            .sambutan-content p {
                font-size: 14px;
            }

            .artikel-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }

            .artikel-slider {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .artikel-card img {
                height: 200px;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 35px;
            }

            .footer-section {
                text-align: center;
            }

            .footer-logo {
                justify-content: center;
            }

            .footer-logo-img {
                width: 35px;
                height: 35px;
            }

            .footer-logo span {
                font-size: 16px;
            }

            .social-links {
                justify-content: center;
            }

            .footer-section ul li {
                text-align: center;
            }

            .footer-section ul li a {
                justify-content: center;
            }

            main {
                padding: 40px 20px;
            }

            .section-title {
                font-size: 28px;
            }

            .jurusan-program-section {
                padding: 60px 0;
                margin: 0 -20px 60px -20px;
            }

            .jurusan-program-container {
                padding: 0 20px;
            }

            .jurusan-program-title {
                font-size: 28px;
            }

            .jurusan-program-subtitle {
                font-size: 15px;
                padding: 0 10px;
            }

            .jurusan-slider-wrapper {
                position: relative;
                padding: 0 10px;
                display: flex;
                justify-content: center;
            }

            .jurusan-grid {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                scroll-snap-type: x mandatory;
                scroll-behavior: smooth;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                gap: 20px;
                padding: 20px 5px;
                margin: 0 auto;
                justify-content: center;
            }

            .jurusan-grid::-webkit-scrollbar {
                display: none;
            }

            .jurusan-card {
                min-width: calc(100% - 40px);
                max-width: calc(100% - 40px);
                flex-shrink: 0;
                scroll-snap-align: center;
                padding: 30px 20px;
                margin: 0 auto;
            }

            .jurusan-content h3 {
                font-size: 17px;
                min-height: auto;
            }

            .jurusan-content p {
                font-size: 13px;
                padding: 0 10px;
            }

            .jurusan-content ul {
                gap: 6px;
                padding: 0 5px;
            }

            .jurusan-content ul li {
                font-size: 12px;
                max-width: 180px;
            }

            .btn-detail {
                font-size: 12px;
                padding: 10px 20px;
            }

            .slider-nav {
                display: flex;
            }

            .slider-nav.prev {
                left: 10px;
            }

            .slider-nav.next {
                right: 10px;
            }

            .slider-dots {
                display: flex;
            }

            .jurusan-motivasi {
                font-size: 16px;
                padding: 25px 20px;
                margin-top: 20px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                min-height: 450px;
                padding: 50px 15px;
            }

            .hero h1 {
                font-size: 28px;
            }

            .hero p {
                font-size: 14px;
            }

            .stats {
                gap: 10px;
                padding: 35px 10px;
            }

            .stat-item i {
                font-size: 24px;
            }

            .stat-item .number {
                font-size: 28px;
            }

            .stat-item .label {
                font-size: 10px;
            }

            .section-title {
                font-size: 24px;
            }

            .jurusan-program-title {
                font-size: 24px;
            }

            .jurusan-card {
                min-width: calc(100% - 30px);
                max-width: calc(100% - 30px);
                padding: 25px 18px;
            }

            .slider-nav {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }

            .slider-nav.prev {
                left: 5px;
            }

            .slider-nav.next {
                right: 5px;
            }

            .jurusan-motivasi {
                font-size: 15px;
                padding: 20px 15px;
            }
        }
    </style>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // ganti '/Web-Sekolah/service-worker.js' jika project ada di subfolder,
                // atau pakai '/service-worker.js' jika di root
                navigator.serviceWorker.register('/Web-Sekolah/service-worker.js', { scope: '/Web-Sekolah/' })
                    .then(reg => console.log('ServiceWorker registered with scope:', reg.scope))
                    .catch(err => console.error('ServiceWorker registration failed:', err));
            });
        }
    </script>
</head>

<body>
    <?php include 'include/nav.php'; ?>



    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="hero" id="beranda">


            <div class="hero-content">
                <h1>SMK TI GARUDA NUSANTARA</h1>
                <p>Sekolah Unggulan di bidang Teknologi Informasi dan Komunikasi<br>Membangun Generasi Digital yang
                    Kompeten
                    dan Berkarakter</p>
                <div class="hero-buttons">
                    <button class="btn-hero btn-primary" onclick="window.location.href='pendaftaran.php'">
                        <i class="fas fa-rocket"></i> Daftar Sekarang
                    </button>
                    <button class="btn-hero btn-secondary"
                        onclick="document.getElementById('profil').scrollIntoView({behavior: 'smooth'})">
                        <i class="fas fa-info-circle"></i> Pelajari Lebih Lanjut
                    </button>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats">
            <div class="stat-item fade-in">
                <i class="fas fa-chalkboard-teacher"></i>
                <span class="number" data-target="25" data-plus="true">0</span>
                <span class="label">Guru Profesional</span>
            </div>
            <div class="stat-item fade-in">
                <i class="fas fa-book-reader"></i>
                <span class="number" data-target="900" data-plus="true">0</span>
                <span class="label">Siswa Aktif</span>
            </div>
            <div class="stat-item fade-in">
                <i class="fas fa-graduation-cap"></i>
                <span class="number" data-target="6">0</span>
                <span class="label">Jurusan</span>
            </div>
        </section>

        <!-- Other sections -->
        <!-- About Section -->
        <section id="profil" class="fade-in">
            <h2 class="section-title">Tentang Sekolah</h2>
            <div class="about-content">
                <div class="about-text">
                    <p><strong>SMK TI Garuda Nusantara</strong> adalah Sekolah Menengah Kejuruan yang berfokus pada
                        pendidikan Teknologi Informasi dengan visi menciptakan lulusan yang unggul, kompeten, dan siap
                        bersaing di era digital.</p>
                    <p>Kami berkomitmen untuk memberikan pendidikan berkualitas dengan fasilitas modern, tenaga pengajar
                        profesional, dan kurikulum yang sesuai dengan kebutuhan industri. Setiap siswa kami dibimbing
                        untuk menjadi pribadi yang berkarakter, inovatif, dan mampu menghadapi tantangan masa depan.</p>
                    <p>Dengan pengalaman lebih dari 15 tahun dalam bidang pendidikan kejuruan, kami telah menghasilkan
                        ribuan alumni yang sukses berkarir di berbagai perusahaan teknologi terkemuka.</p>
                </div>
                <div class="about-image fade-in">
                    <img src="assets/tnt.png" alt="SMK TI Garuda Nusantara">
                </div>
            </div>
        </section>

        <!-- Sambutan Kepala Sekolah -->
        <section class="sambutan-section fade-in">
            <div class="sambutan-container">
                <div class="sambutan-img">
                    <img src="assets/kepsek.jpg" alt="Kepala Sekolah">
                    <div class="sambutan-badge">Kepala Sekolah</div>
                </div>
                <div class="sambutan-content">
                    <h3>Sambutan Kepala Sekolah</h3>
                    <p><strong>Assalamualaikum Wr. Wb.</strong></p>
                    <p>Salam sejahtera bagi kita semua.</p>
                    <p>Selamat datang di website resmi SMK TI Garuda Nusantara. Sebagai Kepala Sekolah, saya merasa
                        bangga dan bersyukur dapat memimpin lembaga pendidikan yang telah dipercaya oleh masyarakat
                        dalam mencetak generasi muda yang cerdas, terampil, dan berakhlak mulia.</p>
                    <p>Di era digital ini, kami berkomitmen penuh untuk terus berinovasi dalam metode pembelajaran,
                        meningkatkan kualitas fasilitas, dan menjalin kerjasama dengan industri untuk memberikan
                        pengalaman belajar yang terbaik bagi siswa-siswi kami.</p>
                    <p>Kami mengundang Anda untuk bergabung bersama kami dalam membangun masa depan yang gemilang
                        melalui pendidikan berkualitas.</p>
                    <div class="sambutan-signature">
                        <p><strong>Drs. Ridho, M.Pd</strong><br>Kepala Sekolah SMK TI Garuda Nusantara</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Jurusan -->
        <section id="jurusan-program" class="jurusan-program-section fade-in">
            <div class="jurusan-program-container">
                <div class="jurusan-program-header">
                    <h2 class="jurusan-program-title">Jurusan SMK TI Garuda Nusantara</h2>
                    <p class="jurusan-program-subtitle">Temukan jurusan yang sesuai dengan minat dan bakatmu</p>
                </div>

                <div class="jurusan-slider-wrapper">
                    <button class="slider-nav prev" id="jurusanPrev">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="jurusan-grid" id="jurusanGrid">
                        <!-- Card 1 - TKJ -->
                        <div class="jurusan-card">
                            <div class="jurusan-icon">
                                <img src="assets/tkj.png" alt="TKJ Icon">
                            </div>
                            <div class="jurusan-content">
                                <h3>Teknik Komputer dan Jaringan (TKJ)</h3>
                                <p>Belajar merancang, mengelola, dan memperbaiki jaringan komputer.</p>
                                <ul>
                                    <li>Jaringan LAN/WAN</li>
                                    <li>Server & Infrastruktur</li>
                                    <li>Keamanan Jaringan</li>
                                </ul>
                                <button class="btn-detail" onclick="window.location.href='jurusan-tkj.php'">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 2 - MP -->
                        <div class="jurusan-card">
                            <div class="jurusan-icon">
                                <img src="assets/mp.png" alt="MP Icon">
                            </div>
                            <div class="jurusan-content">
                                <h3>Manajemen Perkantoran (MP)</h3>
                                <p>Fokus pada administrasi modern dan teknologi perkantoran digital.</p>
                                <ul>
                                    <li>Administrasi Digital</li>
                                    <li>Manajemen Dokumen</li>
                                    <li>Teknologi Perkantoran</li>
                                </ul>
                                <button class="btn-detail" onclick="window.location.href='jurusan-mp.php'">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 3 - Animasi -->
                        <div class="jurusan-card">
                            <div class="jurusan-icon">
                                <img src="assets/animasi.png" alt="Animasi Icon">
                            </div>
                            <div class="jurusan-content">
                                <h3>Animasi</h3>
                                <p>Membuat animasi 2D dan 3D kreatif untuk media dan hiburan digital.</p>
                                <ul>
                                    <li>Animasi 2D/3D</li>
                                    <li>Desain Karakter</li>
                                    <li>Multimedia Kreatif</li>
                                </ul>
                                <button class="btn-detail" onclick="window.location.href='jurusan-animasi.php'">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 4 - RPL -->
                        <div class="jurusan-card">
                            <div class="jurusan-icon">
                                <img src="assets/rpl.png" alt="RPL Icon">
                            </div>
                            <div class="jurusan-content">
                                <h3>Rekayasa Perangkat Lunak (RPL)</h3>
                                <p>Mengembangkan aplikasi dan sistem berbasis web maupun mobile.</p>
                                <ul>
                                    <li>Pemrograman Web/Mobile</li>
                                    <li>Database & Backend</li>
                                    <li>UI/UX Design</li>
                                </ul>
                                <button class="btn-detail" onclick="window.location.href='jurusan-rpl.php'">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 5 - TJAT -->
                        <div class="jurusan-card">
                            <div class="jurusan-icon">
                                <img src="assets/tjat.png" alt="TJAT Icon">
                            </div>
                            <div class="jurusan-content">
                                <h3>Teknik Jaringan Akses Telekomunikasi (TJAT)</h3>
                                <p>Mempelajari sistem komunikasi jaringan dan teknologi telekomunikasi.</p>
                                <ul>
                                    <li>Jaringan Fiber Optik</li>
                                    <li>Teknologi Wireless</li>
                                    <li>Komunikasi Data</li>
                                </ul>
                                <button class="btn-detail" onclick="window.location.href='jurusan-tjat.php'">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Card 6 - DKV -->
                        <div class="jurusan-card">
                            <div class="jurusan-icon">
                                <img src="assets/dkv.png" alt="DKV Icon">
                            </div>
                            <div class="jurusan-content">
                                <h3>Desain Komunikasi Visual (DKV)</h3>
                                <p>Mengasah kreativitas dalam desain grafis, branding, dan multimedia visual.</p>
                                <ul>
                                    <li>Desain Grafis</li>
                                    <li>Branding & Identitas</li>
                                    <li>Multimedia Visual</li>
                                </ul>
                                <button class="btn-detail" onclick="window.location.href='jurusan-dkv.php'">
                                    Detail <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button class="slider-nav next" id="jurusanNext">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="slider-dots" id="sliderDots"></div>

                <div class="jurusan-motivasi">
                    <p>Bersama <span class="highlight-text">SMK TI Garuda Nusantara</span>, wujudkan masa depanmu di
                        bidang
                        teknologi dan kreativitas!</p>
                </div>
            </div>
        </section>


        <!-- About Section -->
        <section class="ekstrakurikuler-section" aria-labelledby="ekskulTitle">
    <div class="ekstrakurikuler-inner">
        <h2 id="ekskulTitle" class="ekstrakurikuler-title">Ekstrakurikuler Unggulan</h2>

        <div class="ekstrakurikuler-content">
            <div class="ekstrakurikuler-left">
                <div class="ekstrakurikuler-carousel" aria-hidden="false">
                    <div class="ekstrakurikuler-nav" aria-hidden="true">
                        <button id="ekskulPrev" aria-label="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
                        <button id="ekskulNext" aria-label="Berikutnya"><i class="fas fa-chevron-right"></i></button>
                    </div>

                    <div class="ekskul-logo" id="ekskulLogoWrap">
                        <img src="assets/pmr.jpg" alt="Logo PMR" id="ekskulLogo">
                    </div>
                </div>

                <div class="ekskul-meta" role="status" aria-live="polite">
                    <div class="name" id="ekskulName">PMR</div>
                    <div class="subtitle" id="ekskulSubtitle">ADHI WIRA BHAKTI</div>
                </div>
            </div>

            <div class="ekstrakurikuler-right">
                <img class="ekstrakurikuler-illustration" id="ekskulIllustration" src="assets/pmr.jpg" alt="Ilustrasi PMR"> 

                <div class="ekskul-description" id="ekskulDescription">
                    <h3>PMR (Palang Merah Remaja)</h3>
                    <p>Palang Merah Remaja (PMR) adalah ekstrakurikuler unggulan...</p>
                    <div class="ekskul-details">
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>

        <!-- Kembali ke Main -->
        <!-- Artikel Section -->
        <section id="artikel" class="artikel-container fade-in">
            <div class="artikel-header">
                <h2 class="section-title">Artikel & Berita Terbaru</h2>
                <button class="btn-view-all" onclick="window.location.href='article.php'">
                    View All Articles <i class="fas fa-arrow-right"></i>
                </button>
            </div>
            <div class="artikel-slider">
                <?php
                $query = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3";
                $result = mysqli_query($db, $query);
                while ($berita = mysqli_fetch_assoc($result)):
                    ?>
                    <article class="artikel-card">
                        <img src="berita/<?= htmlspecialchars($berita['gambar']) ?>"
                            alt="<?= htmlspecialchars($berita['judul']) ?>">
                        <div class="artikel-content">
                            <div class="artikel-meta">
                                <span><i class="far fa-calendar"></i>
                                    <?= date('d F Y', strtotime($berita['tanggal'])) ?></span>
                                <span><i class="far fa-user"></i> <?= htmlspecialchars($berita['penulis']) ?></span>
                            </div>
                            <h3><?= htmlspecialchars($berita['judul']) ?></h3>
                            <p>
                                <?php
                                $isi = $berita['isi'];
                                $isi_bersih = preg_replace('/^##.*$/m', '', $isi);
                                $isi_bersih = str_replace(["\r", "\n"], ' ', $isi_bersih);
                                $excerpt = mb_strimwidth(trim($isi_bersih), 0, 120, '...');
                                echo htmlspecialchars($excerpt);
                                ?>
                            </p>
                            <a href="artikel-detail.php?id=<?= $berita['id'] ?>" class="artikel-link">
                                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->


    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // ========================================
        // LOADING SCREEN
        // ========================================

        // ========================================
        // HEADER SCROLL EFFECT
        // ========================================
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // ========================================
        // ACTIVE NAVIGATION LINK
        // ========================================
        const sections = document.querySelectorAll('section[id], footer[id]'); // Tambahkan footer[id]
        const navLinks = document.querySelectorAll('header nav ul li a');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;

                // Perbaiki logika deteksi section yang aktif
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            // Perbaiki penanganan active state
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current)) {
                    link.classList.add('active');
                }
            });
        });

        // ========================================
        // COUNTER ANIMATION
        // ========================================
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const hasPlus = element.getAttribute('data-plus') === 'true';
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    element.textContent = Math.floor(current) + (hasPlus ? '+' : '');
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target + (hasPlus ? '+' : '');
                }
            };
            updateCounter();
        }

        // ========================================
        // INTERSECTION OBSERVER
        // ========================================
        const observerOptions = {
            threshold: 0.2,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');

                    // Animate counters when stats section is visible
                    if (entry.target.classList.contains('stat-item')) {
                        const number = entry.target.querySelector('.number');
                        if (number && !number.classList.contains('animated')) {
                            animateCounter(number);
                            number.classList.add('animated');
                        }
                    }
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in, .stat-item').forEach(el => {
            observer.observe(el);
        });

        // ========================================
        // SCROLL TO TOP BUTTON
        // ========================================
        const scrollTopBtn = document.getElementById('scrollTop');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // ========================================
        // RIPPLE EFFECT ON BUTTONS
        // ========================================
        function createRipple(event) {
            const button = event.currentTarget;
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        }

        // Add ripple effect to all buttons
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', createRipple);
        });

        // ========================================
        // SMOOTH SCROLL
        // ========================================
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // ========================================
        // LOGO ANIMATION
        // ========================================
        document.querySelector('.logo').addEventListener('mouseenter', function () {
            this.querySelector('.logo-img').style.transform = 'rotate(360deg) scale(1.1)';
            this.querySelector('.logo-img').style.transition = 'transform 0.6s ease';
        });

        document.querySelector('.logo').addEventListener('mouseleave', function () {
            this.querySelector('.logo-img').style.transform = 'rotate(0deg) scale(1)';
        });

        // ========================================
        // JURUSAN SLIDER - IMPROVED
        // ========================================
        let jurusanCurrentIndex = 0;
        const jurusanGrid = document.getElementById('jurusanGrid');
        const jurusanCards = Array.from(jurusanGrid.querySelectorAll('.jurusan-card'));
        const jurusanPrev = document.getElementById('jurusanPrev');
        const jurusanNext = document.getElementById('jurusanNext');
        const dotsContainer = document.getElementById('sliderDots');

        function getCardsPerView() {
            if (window.innerWidth <= 480) return 1;
            if (window.innerWidth <= 768) return 1;
            if (window.innerWidth <= 1024) return 2;
            return 3;
        }

        function getTotalSlides() {
            const perView = getCardsPerView();
            return Math.ceil(jurusanCards.length / perView);
        }

        function showJurusanSlide(slideIndex) {
            const perView = getCardsPerView();
            const startIndex = slideIndex * perView;

            jurusanCards.forEach((card, i) => {
                if (i >= startIndex && i < startIndex + perView) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });

            updateDots(slideIndex);
            updateNavigationButtons();
        }

        function updateDots(activeIndex) {
            const totalSlides = getTotalSlides();
            dotsContainer.innerHTML = '';

            for (let i = 0; i < totalSlides; i++) {
                const dot = document.createElement('button');
                dot.classList.add('slider-dot');
                if (i === activeIndex) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    jurusanCurrentIndex = i;
                    showJurusanSlide(jurusanCurrentIndex);
                });
                dotsContainer.appendChild(dot);
            }

            // Show dots only on mobile
            const isMobile = window.innerWidth <= 768;
            dotsContainer.style.display = isMobile ? 'flex' : 'none';
        }

        function updateNavigationButtons() {
            const totalSlides = getTotalSlides();
            const showNav = totalSlides > 1;

            jurusanPrev.style.display = showNav ? 'flex' : 'none';
            jurusanNext.style.display = showNav ? 'flex' : 'none';

            // Disable prev button on first slide
            jurusanPrev.disabled = jurusanCurrentIndex === 0;
            jurusanPrev.style.opacity = jurusanCurrentIndex === 0 ? '0.5' : '1';

            // Disable next button on last slide
            jurusanNext.disabled = jurusanCurrentIndex === totalSlides - 1;
            jurusanNext.style.opacity = jurusanCurrentIndex === totalSlides - 1 ? '0.5' : '1';
        }

        jurusanPrev.addEventListener('click', () => {
            if (jurusanCurrentIndex > 0) {
                jurusanCurrentIndex--;
                showJurusanSlide(jurusanCurrentIndex);
            }
        });

        jurusanNext.addEventListener('click', () => {
            const totalSlides = getTotalSlides();
            if (jurusanCurrentIndex < totalSlides - 1) {
                jurusanCurrentIndex++;
                showJurusanSlide(jurusanCurrentIndex);
            }
        });

        // Handle window resize

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                jurusanCurrentIndex = 0;
                showJurusanSlide(jurusanCurrentIndex);
            }, 250);
        });

        // Initialize slider
        showJurusanSlide(jurusanCurrentIndex);

        // Touch/Swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;

        jurusanGrid.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });

        jurusanGrid.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left - next
                    jurusanNext.click();
                } else {
                    // Swipe right - prev
                    jurusanPrev.click();
                }
            }
        }

        // ========================================
        // PREVENT FORM RESUBMISSION
        // ========================================
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // ========================================

        // ========================================
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');

                        }
                        observer.unobserve(img);
                    }
                });
            });
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            document.querySelectorAll('img[data-src]').forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });

        }


        // // ========================================
// EKSTRAKURIKULER CAROUSEL - FINAL REFINED CODE
// ========================================
(function () {
    const slides = [
        {
            src: 'assets/pmr.jpg',
            name: 'PMR',
            subtitle: 'ADHI WIRA BHAKTI',
            illustration: 'assets/pmr.jpg', // Tetap ada di data, tapi tidak dipakai di render
            title: 'PMR (Palang Merah Remaja)',
            description: 'Palang Merah Remaja (PMR) adalah organisasi kepemudaan yang bergerak di bidang kesehatan dan kemanusiaan. Melalui PMR, siswa belajar tentang pertolongan pertama, kesehatan remaja, dan pengabdian masyarakat.',
            details: [
                { icon: 'fas fa-users', text: '50+ Anggota Aktif' },
                { icon: 'fas fa-trophy', text: 'Berprestasi Tingkat Kota' },
                { icon: 'fas fa-briefcase-medical', text: 'Pelatihan Pertolongan Pertama' }
            ]
        },
        {
            src: 'assets/osis.jpg',
            name: 'OSIS',
            subtitle: 'KOMUNITAS SISWA',
            illustration: 'assets/osis.jpg',
            title: 'OSIS (Organisasi Siswa Intra Sekolah)',
            description: 'OSIS adalah wadah bagi siswa untuk mengembangkan kepemimpinan, kreativitas, dan partisipasi aktif dalam kegiatan sekolah. OSIS menjadi jembatan komunikasi antara siswa dan pihak sekolah.',
            details: [
                { icon: 'fas fa-crown', text: 'Pengembangan Kepemimpinan' },
                { icon: 'fas fa-bolt', text: 'Pelaksana Kegiatan Sekolah' },
                { icon: 'fas fa-comments', text: 'Representasi Suara Siswa' }
            ]
        },
        {
            src: 'assets/basket.jpg', 
            name: 'BASKET',
            subtitle: 'TIM OLAHRAGA',
            illustration: 'assets/basket.jpg',
            title: 'Basket (Tim Olahraga Unggulan)',
            description: 'Tim Ekstrakurikuler Basket kami fokus pada pengembangan fisik, teknik, dan kerjasama tim. Kami rutin mengikuti kompetisi tingkat kabupaten hingga provinsi untuk mengasah kemampuan bertanding dan meraih prestasi.',
            details: [
                { icon: 'fas fa-medal', text: 'Juara Turnamen Regional' },
                { icon: 'fas fa-dumbbell', text: 'Latihan Fisik Intensif' },
                { icon: 'fas fa-bullseye', text: 'Strategi dan Taktik Tim' }
            ]
        }
    ];

    let idx = 0;
    // Elemen Sisi Kiri
    const imgEl = document.getElementById('ekskulLogo');
    const nameEl = document.getElementById('ekskulName');
    const subtitleEl = document.getElementById('ekskulSubtitle');
    const prevBtn = document.getElementById('ekskulPrev');
    const nextBtn = document.getElementById('ekskulNext');

    // Elemen Sisi Kanan
    // HAPUS: const illustrationEl = document.getElementById('ekskulIllustration');
    const descriptionEl = document.getElementById('ekskulDescription'); 

    // Tambahkan transisi fade
    imgEl.parentElement.style.transition = 'opacity 0.3s ease-in-out';
    // HAPUS: illustrationEl.style.transition = 'opacity 0.3s ease-in-out';
    descriptionEl.style.transition = 'opacity 0.3s ease-in-out';

    function updateContent(s) {
        // Update Sisi Kiri
        imgEl.src = s.src;
        imgEl.alt = s.name + ' logo';
        nameEl.textContent = s.name;
        subtitleEl.textContent = s.subtitle;

        // Update Ilustrasi (Dihapus dari DOM, hanya hapus referensi render di sini)
        // HAPUS: illustrationEl.src = s.illustration;
        // HAPUS: illustrationEl.alt = `Ilustrasi ${s.name}`;

        // Update Deskripsi dan Detail (Sisi Kanan)
        descriptionEl.innerHTML = `
            <h3>${s.title}</h3>
            <p>${s.description}</p>
            <div class="ekskul-details">
                ${s.details.map(d => `
                    <div class="detail-item">
                        <i class="${d.icon}"></i>
                        <span>${d.text}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function render() {
        // Animasi fade out
        imgEl.parentElement.style.opacity = '0';
        // HAPUS: illustrationEl.style.opacity = '0';
        descriptionEl.style.opacity = '0'; 

        setTimeout(() => {
            // Update konten setelah fade out
            updateContent(slides[idx]);
            
            // Animasi fade in
            imgEl.parentElement.style.opacity = '1';
            // HAPUS: illustrationEl.style.opacity = '1';
            descriptionEl.style.opacity = '1'; 
        }, 300); // Durasi disesuaikan dengan transisi CSS (0.3s)
        
    }

    // Navigasi
    prevBtn.addEventListener('click', function () {
        idx = (idx - 1 + slides.length) % slides.length;
        render();
    });

    nextBtn.addEventListener('click', function () {
        idx = (idx + 1) % slides.length;
        render();
    });

    // Inisialisasi awal (tanpa fade di awal)
    updateContent(slides[idx]);
})();
    </script>

    <?php include 'include/footer.php'; ?>
</body>

</html>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'];
    unset($_SESSION['error']); ?></div>
<?php endif; ?>