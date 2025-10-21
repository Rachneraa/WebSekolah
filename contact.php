<?php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Nanti bisa ditambahkan logic untuk menyimpan ke database atau kirim email
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $telepon = htmlspecialchars($_POST['telepon']);
    $subjek = htmlspecialchars($_POST['subjek']);
    $pesan = htmlspecialchars($_POST['pesan']);
    
    // Set success message
    $_SESSION['success_message'] = "Pesan Anda berhasil dikirim! Kami akan menghubungi Anda segera.";
    
    // Redirect to prevent form resubmission
    header("Location: kontak.php");
    exit();
}

$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kontak Kami - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="/pkl/manifest.json">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
            background: #f8f9fa;
        }

        /* Hero Section */
        .hero-kontak {
            position: relative;
            width: 100vw;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            min-height: 400px;
            background: url('assets/c.jpeg') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            overflow: hidden;
            margin-top: -80px;
            padding-top: 80px;
        }

        .hero-kontak::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.9), rgba(0, 51, 102, 0.85));
            z-index: 1;
        }

        .hero-kontak-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 40px 20px;
        }

        .hero-kontak h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
            animation: fadeInUp 0.8s ease;
        }

        .hero-kontak p {
            font-size: 18px;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            font-size: 14px;
            animation: fadeInUp 1.2s ease 0.4s both;
        }

        .breadcrumb a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: var(--primary-orange);
        }

        .breadcrumb span {
            opacity: 0.7;
        }

        /* Main Content */
        main {
            max-width: 1200px;
            margin: -60px auto 60px;
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        /* Contact Container */
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 60px;
        }

        /* Contact Form */
        .contact-form-section {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }

        .contact-form-section h2 {
            font-size: 28px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .contact-form-section p {
            color: var(--text-gray);
            margin-bottom: 30px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 14px;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--border-gray);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: white;
            box-shadow: 0 0 0 4px rgba(0, 73, 157, 0.1);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(255, 131, 3, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 131, 3, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Contact Info */
        .contact-info-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-info-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .contact-info-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-orange);
        }

        .contact-info-card h3 {
            font-size: 20px;
            color: var(--primary-blue);
            margin-bottom: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .contact-info-card h3 i {
            color: var(--primary-orange);
            font-size: 24px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-gray);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.1), rgba(255, 131, 3, 0.1));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-icon i {
            color: var(--primary-blue);
            font-size: 18px;
        }

        .info-content {
            flex: 1;
        }

        .info-content h4 {
            font-size: 14px;
            color: var(--text-gray);
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-content p {
            color: var(--text-dark);
            font-size: 15px;
            line-height: 1.6;
        }

        .info-content a {
            color: var(--primary-blue);
            text-decoration: none;
            transition: color 0.3s;
        }

        .info-content a:hover {
            color: var(--primary-orange);
        }

        /* Social Media */
        .social-media {
            display: flex;
            gap: 12px;
            margin-top: 15px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 18px;
        }

        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 73, 157, 0.3);
        }

        .social-link.facebook:hover {
            background: #1877f2;
        }

        .social-link.instagram:hover {
            background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045);
        }

        .social-link.twitter:hover {
            background: #1da1f2;
        }

        .social-link.youtube:hover {
            background: #ff0000;
        }

        .social-link.whatsapp:hover {
            background: #25d366;
        }

        /* Map Section */
        .map-section {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 60px;
        }

        .map-section h2 {
            font-size: 28px;
            color: var(--primary-blue);
            margin-bottom: 25px;
            font-weight: 700;
            text-align: center;
        }

        .map-container {
            width: 100%;
            height: 450px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* FAQ Section */
        .faq-section {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 60px;
        }

        .faq-section h2 {
            font-size: 28px;
            color: var(--primary-blue);
            margin-bottom: 30px;
            font-weight: 700;
            text-align: center;
        }

        .faq-item {
            border: 2px solid var(--border-gray);
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: var(--primary-orange);
        }

        .faq-question {
            padding: 20px 25px;
            background: #f9fafb;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: #f3f4f6;
        }

        .faq-question.active {
            background: var(--primary-blue);
            color: white;
        }

        .faq-icon {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .faq-question.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
            color: var(--text-gray);
            line-height: 1.8;
        }

        .faq-answer.active {
            padding: 20px 25px;
            max-height: 500px;
        }

        /* Success Message */
        .success-message {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 18px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            animation: slideInDown 0.5s ease;
        }

        .success-message i {
            font-size: 24px;
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

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Scroll to Top Button */
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

        /* Responsive Design */
        @media (max-width: 1024px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .hero-kontak {
                min-height: 300px;
                margin-top: -70px;
                padding-top: 70px;
            }

            .hero-kontak h1 {
                font-size: 32px;
            }

            .hero-kontak p {
                font-size: 16px;
            }

            main {
                margin: -40px auto 40px;
            }

            .contact-form-section,
            .contact-info-card,
            .map-section,
            .faq-section {
                padding: 25px 20px;
            }

            .contact-form-section h2,
            .map-section h2,
            .faq-section h2 {
                font-size: 24px;
            }

            .map-container {
                height: 300px;
            }

            .social-media {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .hero-kontak h1 {
                font-size: 28px;
            }

            .hero-kontak p {
                font-size: 14px;
            }

            .breadcrumb {
                flex-wrap: wrap;
                font-size: 12px;
            }

            .contact-form-section,
            .contact-info-card,
            .map-section,
            .faq-section {
                padding: 20px 15px;
            }
        }
    </style>
</head>

<body>
    <?php include 'include/nav.php'; ?>

    <!-- Hero Section -->
    <section class="hero-kontak">
        <div class="hero-kontak-content">
            <h1>Hubungi Kami</h1>
            <p>Kami siap membantu Anda! Jangan ragu untuk menghubungi kami</p>
            <div class="breadcrumb">
                <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
                <span>/</span>
                <span>Kontak</span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main>
        <!-- Contact Container -->
        <div class="contact-container fade-in">
            <!-- Contact Form -->
            <div class="contact-form-section">
                <h2>Kirim Pesan</h2>
                <p>Isi formulir di bawah ini dan kami akan merespons secepat mungkin</p>
                
                <?php if ($success_message): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success_message; ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" action="" id="contactForm">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap Anda">
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="contoh@email.com">
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor Telepon</label>
                        <input type="tel" id="telepon" name="telepon" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label for="subjek">Subjek <span class="required">*</span></label>
                        <select id="subjek" name="subjek" required>
                            <option value="">-- Pilih Subjek --</option>
                            <option value="Pendaftaran">Informasi Pendaftaran</option>
                            <option value="Jurusan">Informasi Jurusan</option>
                            <option value="Fasilitas">Informasi Fasilitas</option>
                            <option value="Umum">Pertanyaan Umum</option>
                            <option value="Kerjasama">Kerjasama & Partnership</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pesan">Pesan <span class="required">*</span></label>
                        <textarea id="pesan" name="pesan" required placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="contact-info-section">
                <!-- Alamat -->
                <div class="contact-info-card fade-in">
                    <h3><i class="fas fa-map-marker-alt"></i> Alamat Kami</h3>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="info-content">
                            <h4>Alamat Lengkap</h4>
                            <p>Jl. Pendidikan No. 123<br>Kelurahan Sukamaju, Kecamatan Cimanggis<br>Kota Depok, Jawa Barat 16451</p>
                        </div>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="contact-info-card fade-in">
                    <h3><i class="fas fa-phone-alt"></i> Kontak</h3>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h4>Telepon</h4>
                            <p><a href="tel:+622187654321">(021) 8765-4321</a></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="info-content">
                            <h4>WhatsApp</h4>
                            <p><a href="https://wa.me/628123456789" target="_blank">+62 812-3456-789</a></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h4>Email</h4>
                            <p><a href="mailto:info@smktigarudanusantara.sch.id">info@smktigarudanusantara.sch.id</a></p>
                        </div>
                    </div>
                </div>

                <!-- Jam Operasional -->
                <div class="contact-info-card fade-in">
                    <h3><i class="fas fa-clock"></i> Jam Operasional</h3>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="info-content">
                            <h4>Senin - Jumat</h4>
                            <p>07:00 - 16:00 WIB</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="info-content">
                            <h4>Sabtu</h4>
                            <p>07:00 - 14:00 WIB</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div class="info-content">
                            <h4>Minggu & Libur</h4>
                            <p>Tutup</p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="contact-info-card fade-in">
                    <h3><i class="fas fa-share-alt"></i> Media Sosial</h3>
                    <div class="info-content">
                        <p style="margin-bottom: 15px;">Ikuti kami di media sosial untuk update terbaru:</p>
                        <div class="social-media">
                            <a href="#" class="social-link facebook" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link instagram" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link twitter" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link youtube" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="social-link whatsapp" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <section class="map-section fade-in">
            <h2><i class="fas fa-map-marked-alt"></i> Lokasi Kami</h2>
            <div class="map-container">
                <!-- Ganti dengan Google Maps embed URL sekolah Anda -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.741887796676!2d106.82493931476898!3d-6.426198895362729!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69eb3e9c5c3c7b%3A0x3d6c3e8c2e8c3e8c!2sDepok%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1234567890123!5m2!1sen!2sid" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section fade-in">
            <h2><i class="fas fa-question-circle"></i> Pertanyaan yang Sering Diajukan (FAQ)</h2>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Bagaimana cara mendaftar ke SMK TI Garuda Nusantara?</span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Anda dapat mendaftar secara online melalui website kami di menu Pendaftaran atau datang langsung ke sekolah pada jam operasional. Persiapkan dokumen seperti ijazah SMP, kartu keluarga, dan foto.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Apa saja jurusan yang tersedia?</span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </div>
                <div class="faq-answer">
                    <p>Kami memiliki 6 jurusan unggulan: Teknik Komputer dan Jaringan (TKJ), Rekayasa Perangkat Lunak (RPL), Manajemen Perkantoran (MP), Animasi, Teknik Jaringan Akses Telekomunikasi (TJAT), dan Desain Komunikasi Visual (DKV).</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <span>Apakah ada biaya pendaftaran?</span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </div>
                <div class="faq-answer">
     