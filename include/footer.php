<?php
// 1. Panggil file koneksi database
// Variabel koneksi ($db) akan tersedia setelah ini dipanggil.
require_once 'config/koneksi.php';

// 2. Query untuk mengambil 3 berita terbaru
// Mengambil ID, Judul, dan Tanggal dari tabel 'berita', diurutkan dari yang terbaru (DESC).
$sql_berita = "SELECT id, judul, tanggal FROM berita ORDER BY tanggal DESC LIMIT 3";
$result_berita = mysqli_query($db, $sql_berita);

// Cek jika query gagal (walaupun jarang terjadi jika koneksi berhasil)
if (!$result_berita) {
    // Anda bisa mengganti ini dengan logging error yang lebih aman di lingkungan produksi
    error_log("Gagal mengambil data berita: " . mysqli_error($db));
    // Set result menjadi array kosong agar footer tetap tampil
    $result_berita = [];
}
?>

<footer id="kontak" role="contentinfo">
    <div class="footer-content">
        <div class="footer-section">
            <div class="footer-logo">
                <img src="assets/logo.png" alt="Logo SMK" class="footer-logo-img">
                <span><b>SMK TI Garuda Nusantara</b></span>
            </div>
            <p>Sekolah Menengah Kejuruan unggulan di bidang Teknologi Informasi yang berkomitmen mencetak lulusan
                berkualitas dan siap kerja.</p>
            <div class="social-links">
                <a href="https://www.instagram.com/smktignc_?igsh=MTlzZmcyZ2U0Znk5aw==" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://youtube.com/@kesiswaangarnus?si=UbOoW0H8ELekvKHl" target="_blank" aria-label="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://www.tiktok.com/@officialsmktigarnus?_r=1&_t=ZS-912Ago5SlVQ" target="_blank" aria-label="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Kontak Kami</h3>
            <ul>
                <a href="https://maps.app.goo.gl/UtwVr95LEqjfE49z6">
                    <li><i class="fas fa-map-marker-alt"></i> Jl. Sangkuriang No.34-36, Cipageran, Kec. Cimahi Utara,
                        Kota Cimahi, Jawa Barat 40511</li>
                </a>
                <a href="https://wa.me/6287747045822">
                    <li><i class="fas fa-phone"></i>+62 877 4704 5822</li>
                </a>
                <li><i class="fas fa-envelope"></i> info@smktigaruda.sch.id</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Link Cepat</h3>
            <ul>
                <li><a href="./index.php#beranda"><i class="fas fa-angle-right"></i> Beranda</a></li>
                <li><a href="./index.php#jurusan-program"><i class="fas fa-angle-right"></i> Jurusan</a></li>
                <li><a href="./index.php#artikel"><i class="fas fa-angle-right"></i> Artikel</a></li> 
                <li><a href="ppdb.php"><i class="fas fa-angle-right"></i> Pendaftaran</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Postingan Terbaru</h3>
            <ul>
                <?php
                // Cek apakah query berhasil dan menghasilkan data
                if ($result_berita && mysqli_num_rows($result_berita) > 0) {
                    // Lakukan perulangan untuk menampilkan setiap berita terbaru
                    while ($row = mysqli_fetch_assoc($result_berita)) {
                        // Mengubah format tanggal dari YYYY-MM-DD menjadi DD Mmm YYYY (misal: 10 Okt 2025)
                        $tanggal_formatted = date('j M Y', strtotime($row['tanggal']));
                        
                        // Link dinamis: article.php akan menerima ID berita untuk menampilkan konten penuh
                        $link_artikel = "article.php?id=" . $row['id'];
                        
                        echo '<li class="recent-post-item">';
                        // Menampilkan judul sebagai link
                        echo '  <a href="' . htmlspecialchars($link_artikel) . '">' . htmlspecialchars($row['judul']) . '</a>';
                        // Menampilkan tanggal
                        echo '  <span class="recent-post-date"><i class="far fa-calendar"></i> ' . $tanggal_formatted . '</span>';
                        echo '</li>';
                    }
                    
                    // Bebaskan memori hasil query
                    mysqli_free_result($result_berita);
                    
                } else {
                    // Tampilkan pesan jika tidak ada postingan ditemukan
                    echo '<li class="recent-post-item">Belum ada postingan terbaru.</li>';
                }
                ?>
            </ul>
        </div>
        </div>

    <div class="footer-bottom">
        <p>© 2025 SMK TI Garuda Nusantara. All Rights Reserved.</p>
    </div>
</footer>

<div class="loader-wrapper" id="globalLoader">
    <div class="loader"></div>
</div>

<style>
    /* ... (CSS Anda) ... */
    :root {
        --primary-orange: #ff8303;
        --primary-blue: #00499d;
        --dark-blue: #003366;
        --text-dark: #222;
        --text-gray: #555;
        --border-gray: #e0e0e0;
    }

    footer {
        background: linear-gradient(135deg, var(--dark-blue), #001a33);
        color: white;
        padding: 60px 0 30px;
        width: 100%;
        box-sizing: border-box;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
        padding: 0 40px;
    }

    .footer-section {
        text-align: left;
    }

    .footer-section h3 {
        color: white;
        margin-bottom: 15px;
        font-size: 20px;
        text-align: left;

    }

    .footer-section p {
        text-align: left;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-section ul li {
        margin-bottom: 15px;
        color: #e0e0e0;
        font-size: 14px;
        text-align: left;
    }

    .footer-section ul li i {
        margin-right: 10px;
        color: var(--primary-orange);
        margin-top: 4px;
    }

    .footer-section ul li a {
        color: #e0e0e0;
        text-decoration: none;
        transition: color 0.3s;
        display: inline-flex;
        align-items: center;
        text-align: left;
        gap: 10px;
    }

    .footer-section ul li a:hover {
        color: var(--primary-orange);
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        justify-content: flex-start;
        text-align: left;
        width: 100%;
    }

    .footer-logo span {
        display: flex;
        align-items: center;
        height: 45px;
        text-align: left;
    }

    .footer-logo-img {
        width: 45px;
        height: 45px;
        transition: transform 0.3s;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-links a {
        color: white;
        font-size: 20px;
        transition: color 0.3s;
    }

    .social-links a:hover {
        color: var(--primary-orange);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 30px;
        margin-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
    }

    .recent-post-item {
        text-align: left;
        width: 100%;
    }

    .recent-post-item a {
        display: block;
        text-align: left;
        margin-bottom: 5px;
    }

    .recent-post-date {
        display: block;
        text-align: left;
    }

    .recent-post-date i {
        margin-right: 5px;
    }

    /* Loader styles */
    .loader-wrapper {
        position: fixed;
        inset: 0;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: opacity .3s ease, visibility .3s;
    }

    .loader-wrapper.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .loader {
        width: 60px;
        height: 60px;
        border: 6px solid #eee;
        border-top-color: var(--primary-orange);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 1024px) {
        .footer-content {
            grid-template-columns: 2fr 1fr 1fr;
            padding: 0 30px;
        }

        .footer-section:last-child {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 0 20px;
        }

        .footer-section {
            text-align: left;
        }

        .footer-section:first-child {
            grid-column: 1 / -1;
        }

        .footer-logo {
            justify-content: flex-start;
        }

        .footer-section ul li {
            text-align: left;
        }
    }

    @media (max-width: 480px) {
        .footer-content {
            grid-template-columns: 1fr;
            gap: 35px;
        }

        .footer-section {
            padding: 0 15px;
        }

        .footer-logo img {
            width: 35px;
            height: 35px;
        }
    }
</style>

<script>
// Register Service Worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then(registration => console.log('SW Registered'))
        .catch(error => console.log('SW Registration failed:', error));
}
    // loader hide
    (function () {
        try {
            var loader = document.getElementById('globalLoader') || document.querySelector('.loader-wrapper');
            if (!loader) return;
            document.addEventListener('DOMContentLoaded', function () { loader.classList.add('hidden'); });
            window.addEventListener('load', function () { loader.classList.add('hidden'); });
            setTimeout(function () { loader.classList.add('hidden'); }, 3000);
        } catch (e) { try { document.querySelector('.loader-wrapper').classList.add('hidden'); } catch (e2) { } }
    })();

    // footer logo hover / click behavior
    (function () {
        if (window._footerInit) return;
        window._footerInit = true;
        var footerLogo = document.querySelector('.footer-logo-img');
        if (!footerLogo) return;
        footerLogo.addEventListener('mouseenter', function () {
            footerLogo.style.transform = 'rotate(6deg) scale(1.05)';
            footerLogo.style.transition = 'transform 0.25s ease';
        });
        footerLogo.addEventListener('mouseleave', function () {
            footerLogo.style.transform = 'rotate(0deg) scale(1)';
        });
        footerLogo.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();
</script>