<?php
// WAJIB: Mulai session di baris paling atas
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMPN Cimahi - Membangun Generasi Cerdas & Berkarakter</title>
    <meta name="description" content="Website resmi SMPN Cimahi. Sekolah Menengah Pertama Terakreditasi A unggul dalam prestasi, teknologi, dan pembentukan karakter peserta didik.">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome 6 Free Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            navy: '#0F172A',
                            dark: '#0A1128',
                            blue: '#1E3A8A',
                            accent: '#0284C7',
                            lightBg: '#F8FAFC',
                            softBlue: '#E0F2FE',
                            mutedBlue: '#BAE6FD',
                            grayText: '#475569'
                        }
                    },
                    boxShadow: {
                        'polaroid': '0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.05)',
                        'card-hover': '0 20px 30px -10px rgba(2, 132, 199, 0.15)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom scrollbar and animations */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .hero-polaroid {
            transform: rotate(2deg);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        .hero-polaroid:hover {
            transform: rotate(0deg) scale(1.02);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        
        @keyframes top-ticker {
            0% { transform: translateX(0); }
            45% { transform: translateX(min(0px, calc(100vw - 100% - 24px))); }
            50% { transform: translateX(min(0px, calc(100vw - 100% - 24px))); }
            95% { transform: translateX(0); }
            100% { transform: translateX(0); }
        }

        @media (max-width: 639px) {
            .animate-ticker-mobile {
                display: inline-flex !important;
                white-space: nowrap !important;
                animation: top-ticker 10s ease-in-out infinite;
                width: max-content !important;
            }
        }
    </style>
</head>
<body class="font-sans bg-brand-lightBg text-slate-800 antialiased selection:bg-brand-softBlue selection:text-brand-blue flex flex-col min-h-screen">

    <!-- TOP ANNOUNCEMENT BAR -->
    <div class="bg-brand-navy text-white text-xs py-2 px-3 border-b border-slate-800 overflow-hidden">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="animate-ticker-mobile flex items-center justify-between w-full gap-5 text-slate-200">
                <div class="flex items-center gap-3 shrink-0">
                    <span class="inline-flex items-center gap-1.5 text-brand-softBlue font-semibold">
                        <i class="fa-solid fa-bullhorn text-xs"></i> PPDB Tahun Ajaran 2026/2027 Telah Dibuka!
                    </span>
                    <span class="text-slate-600">|</span>
                    <span class="inline-flex items-center gap-1 text-slate-300 font-medium">
                        <i class="fa-solid fa-graduation-cap text-xs"></i> Terakreditasi A Unggul
                    </span>
                </div>
                <div class="hidden lg:flex items-center gap-3 shrink-0 text-slate-300 font-medium">
                    <span class="text-slate-600">|</span>
                    <a href="tel:0226654321" class="hover:text-white transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-phone text-[10px] text-brand-softBlue"></i> (022) 6654321
                    </a>
                    <span class="text-slate-600">|</span>
                    <a href="mailto:info@smpncimahi.sch.id" class="hover:text-white transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-envelope text-[10px] text-brand-softBlue"></i> info@smpncimahi.sch.id
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN NAVBAR -->
    <header id="main-header" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- BRAND LOGO -->
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-brand-navy flex items-center justify-center text-white font-extrabold text-xl shadow-md group-hover:bg-brand-accent transition-colors duration-300">
                        <i class="fa-solid fa-school text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-lg sm:text-xl tracking-tight text-slate-900 leading-none">
                            SMPN CIMAHI
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium tracking-wide mt-1">Cerdas • Berkarakter • Unggul</span>
                    </div>
                </a>

                <!-- DESKTOP NAV LINKS -->
                <nav class="hidden lg:flex items-center gap-6 lg:gap-7 text-sm font-semibold text-slate-600">
                    <a href="index.php#beranda" class="hover:text-brand-accent transition-colors py-1">Beranda</a>
                    <a href="index.php#tentang" class="hover:text-brand-accent transition-colors py-1">Tentang</a>
                    <a href="index.php#ekstra" class="hover:text-brand-accent transition-colors py-1">Ekstrakulikuler</a>
                    <a href="index.php#berita" class="hover:text-brand-accent transition-colors py-1">Berita</a>
                    <a href="contact.php" class="hover:text-brand-accent transition-colors py-1">Kontak</a>
                </nav>

                <!-- HEADER ACTION BUTTONS -->
                <div class="hidden lg:flex items-center gap-3">
                    <a href="pendaftaran.php" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs sm:text-sm font-extrabold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 flex items-center gap-2 border border-amber-300/40">
                        <i class="fa-solid fa-file-pen text-xs"></i>
                        <span>PPDB ONLINE</span>
                    </a>

                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="backend/admin.php" title="Dashboard Admin" class="w-10 h-10 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all duration-200 active:scale-95 shadow-sm">
                            <i class="fa-solid fa-user-gear text-sm"></i>
                        </a>
                    <?php else: ?>
                        <button onclick="openLoginModal()" title="Login Portal Sekolah" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all duration-200 active:scale-95 border border-slate-200">
                            <i class="fa-solid fa-user text-sm"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- MOBILE MENU HAMBURGER BUTTON -->
                <button id="mobile-menu-btn" onclick="toggleMobileMenu()" aria-label="Buka Menu" class="lg:hidden p-2 text-slate-700 hover:text-brand-navy focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl" id="menu-icon"></i>
                </button>
        </div>
    </header>

    <!-- MOBILE MENU OVERLAY -->
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-slate-900/50 z-[50] hidden backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="toggleMobileMenu()"></div>

    <!-- MOBILE NAV DRAWER -->
    <div id="mobile-menu" class="fixed top-0 right-0 h-full w-[80vw] max-w-sm bg-white z-[60] transform translate-x-full transition-transform duration-300 shadow-2xl flex flex-col lg:hidden">
        <!-- Close Button -->
        <div class="flex justify-end p-5">
            <button onclick="toggleMobileMenu()" class="text-slate-700 hover:text-black focus:outline-none">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>
        </div>
        
        <!-- Links -->
        <div class="flex-1 overflow-y-auto px-6 py-2 flex flex-col space-y-4">
            <a href="index.php#beranda" onclick="toggleMobileMenu()" class="block text-lg font-semibold text-slate-600 hover:text-brand-accent pb-4 border-b border-slate-100 transition-colors">Beranda</a>
            <a href="index.php#tentang" onclick="toggleMobileMenu()" class="block text-lg font-semibold text-slate-600 hover:text-brand-accent pb-4 border-b border-slate-100 transition-colors">Tentang Kami</a>
            <a href="index.php#ekstra" onclick="toggleMobileMenu()" class="block text-lg font-semibold text-slate-600 hover:text-brand-accent pb-4 border-b border-slate-100 transition-colors">Ekstrakulikuler</a>
            <a href="index.php#berita" onclick="toggleMobileMenu()" class="block text-lg font-semibold text-slate-600 hover:text-brand-accent pb-4 border-b border-slate-100 transition-colors">Berita</a>
            <a href="../contact.php" onclick="toggleMobileMenu()" class="block text-lg font-semibold text-slate-600 hover:text-brand-accent pb-4 border-b border-slate-100 transition-colors">Kontak</a>
        </div>
        
        <!-- Action Buttons -->
        <div class="p-6 space-y-4 mb-4 mt-auto">
            <a href="pendaftaran.php" class="flex justify-center items-center w-full py-3.5 bg-[#ff7300] hover:bg-orange-600 text-white font-bold text-lg rounded-full shadow-md transition-colors">
                Daftar
            </a>
            
            <?php if (isset($_SESSION['username'])): ?>
                <a href="backend/admin.php" class="flex justify-center items-center w-full py-3.5 bg-[#004aad] hover:bg-[#003882] text-white font-bold text-lg rounded-full shadow-md gap-2 transition-colors">
                    <i class="fa-solid fa-user"></i> Dashboard
                </a>
            <?php else: ?>
                <button onclick="toggleMobileMenu(); openLoginModal();" class="flex justify-center items-center w-full py-3.5 bg-[#004aad] hover:bg-[#003882] text-white font-bold text-lg rounded-full shadow-md gap-2 transition-colors">
                    <i class="fa-solid fa-user"></i> Login
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL: LOGIN SYSTEM -->
    <div id="login-modal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative border border-slate-100 my-8">
            <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
            
            <div class="text-center space-y-2 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-brand-navy text-white flex items-center justify-center text-xl font-bold mx-auto mb-3 shadow-md">
                    <i class="fa-solid fa-school"></i>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900">Portal Login Sekolah</h3>
                <p class="text-slate-500 text-xs sm:text-sm">Silakan masuk menggunakan akun admin atau siswa SMPN Cimahi</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <form action="config/process_login.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username / NISN</label>
                    <input type="text" name="username" required placeholder="Masukkan Username / NISN" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-blue focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password" id="loginPassword" required placeholder="Masukkan Kata Sandi" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-brand-blue focus:outline-none pr-10">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-sm">
                            <i class="fa-solid fa-eye-slash" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="rememberMe" class="rounded text-brand-navy focus:ring-brand-blue">
                        <span>Ingat Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-navy hover:bg-brand-blue text-white font-bold rounded-xl shadow transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i>
                    <span>Masuk Ke Sistem</span>
                </button>
            </form>
        </div>
    </div>