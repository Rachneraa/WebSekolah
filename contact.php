    <?php
    session_start();
    include 'config/koneksi.php';

    $success_message = '';
    $error_message = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_kontak'])) {
        $nama = mysqli_real_escape_string($db, trim($_POST['nama']));
        $email = mysqli_real_escape_string($db, trim($_POST['email']));
        $telepon = mysqli_real_escape_string($db, trim($_POST['telepon']));
        $subjek = mysqli_real_escape_string($db, $_POST['subjek']);
        $pesan = mysqli_real_escape_string($db, trim($_POST['pesan']));
        
        // Validate input
        if (!empty($nama) && !empty($email) && !empty($subjek) && !empty($pesan)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Insert into database
                $query = "INSERT INTO kontak_pesan (nama, email, telepon, subjek, pesan, tanggal, status) 
                        VALUES ('$nama', '$email', '$telepon', '$subjek', '$pesan', NOW(), 'Baru')";
                
                if (mysqli_query($db, $query)) {
                    $success_message = "Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda dalam 1x24 jam.";
                    
                    // Optional: Send email notification (uncomment if PHPMailer configured)
                    // sendEmailNotification($nama, $email, $subjek, $pesan);
                } else {
                    $error_message = "Terjadi kesalahan sistem. Silakan coba lagi atau hubungi kami langsung.";
                }
            } else {
                $error_message = "Format email tidak valid. Mohon periksa kembali.";
            }
        } else {
            $error_message = "Mohon lengkapi semua field yang wajib diisi (*)";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Kontak Kami - SMK TI Garuda Nusantara</title>
        <meta name="description" content="Hubungi SMK TI Garuda Nusantara melalui formulir, telepon, email, atau WhatsApp. Kami siap membantu informasi PPDB dan program sekolah.">
        <meta name="keywords" content="kontak SMK, hubungi sekolah, PPDB, SMK TI Garuda Nusantara">
        <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Schema Markup for SEO -->
        <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "School",
        "name": "SMK TI Garuda Nusantara",
        "image": "https://smktignc.sch.id/logo.png",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jl. Sangkuriang No.34-36",
            "addressLocality": "Cimahi",
            "addressRegion": "Jawa Barat",
            "postalCode": "40512",
            "addressCountry": "ID"
        },
        "telephone": "+62 22 1234 5678",
        "email": "info@smktignc.sch.id",
        "url": "https://smktignc.sch.id",
        "openingHoursSpecification": [
            {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "07:00",
            "closes": "16:00"
            },
            {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": "Saturday",
            "opens": "07:00",
            "closes": "14:00"
            }
        ]
        }
        </script>
        
        <style>
            /* CSS Variables */
            :root {
                --primary-orange: #ff8303;
                --primary-blue: #00499d;
                --dark-blue: #003366;
                --text-dark: #0f1724;
                --text-gray: #6b7280;
                --border-gray: #e5e7eb;
                --success-green: #10b981;
                --error-red: #ef4444;
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
                color: var(--text-dark);
                overflow-x: hidden;
            }

            /* Page Header with Background Image */
            .page-header {
                background-image: 
                    linear-gradient(135deg, rgba(0, 73, 157, 0.85), rgba(0, 51, 102, 0.85)),
                    url('assets/smk.png'); /* Ganti dengan path gambar Anda */
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                color: white;
                padding: 100px 20px 60px;
                text-align: center;
                position: relative;
                overflow: hidden;
                margin-top: 0;
            }

            /* Animated overlay pattern */
            .page-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-image: 
                    radial-gradient(circle at 20% 50%, rgba(255, 131, 3, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(255, 131, 3, 0.08) 0%, transparent 50%);
                animation: movePattern 20s ease-in-out infinite;
                pointer-events: none;
            }

            @keyframes movePattern {
                0%, 100% {
                    transform: translate(0, 0);
                }
                50% {
                    transform: translate(30px, -30px);
                }
            }

            .page-header h1 {
                font-size: 48px;
                font-weight: 800;
                margin-bottom: 15px;
                position: relative;
                z-index: 1;
                text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            }

            .page-header p {
                font-size: 18px;
                opacity: 0.95;
                position: relative;
                z-index: 1;
                text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
            }

            /* Main Container */
            .kontak-container {
                max-width: 1200px;
                margin: -40px auto 80px;
                padding: 0 20px;
                position: relative;
                z-index: 10;
            }

            .kontak-grid {
                display: grid;
                grid-template-columns: 1fr 1.2fr;
                gap: 40px;
                margin-bottom: 60px;
            }

            /* Contact Info Card */
            .contact-info-card {
                background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
                padding: 40px;
                border-radius: 20px;
                color: white;
                box-shadow: var(--shadow);
                height: fit-content;
            }

            .contact-info-card h2 {
                font-size: 28px;
                margin-bottom: 10px;
            }

            .contact-info-card .subtitle {
                opacity: 0.9;
                margin-bottom: 30px;
                font-size: 15px;
            }

            .info-item {
                display: flex;
                gap: 15px;
                margin-bottom: 20px;
                padding: 18px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                transition: all 0.3s ease;
            }

            .info-item:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateX(5px);
            }

            .info-icon {
                width: 45px;
                height: 45px;
                background: var(--primary-orange);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 18px;
            }

            .info-content h3 {
                font-size: 15px;
                margin-bottom: 5px;
                font-weight: 600;
            }

            .info-content p {
                font-size: 13px;
                opacity: 0.9;
                line-height: 1.6;
            }

            .info-content a {
                color: white;
                text-decoration: none;
                opacity: 0.9;
                transition: opacity 0.3s ease;
            }

            .info-content a:hover {
                opacity: 1;
                text-decoration: underline;
            }

            /* Social Media */
            .social-media {
                margin-top: 25px;
                padding-top: 25px;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .social-media h3 {
                font-size: 16px;
                margin-bottom: 15px;
            }

            .social-links {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
            }

            .social-links a {
                width: 42px;
                height: 42px;
                background: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-size: 16px;
                color: white;
                transition: all 0.3s ease;
            }

            .social-links a:hover {
                background: var(--primary-orange);
                transform: translateY(-3px);
            }

            /* Contact Form Card */
            .contact-form-card {
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: var(--shadow);
                border: 1px solid var(--border-gray);
            }

            .contact-form-card h2 {
                font-size: 28px;
                color: var(--primary-blue);
                margin-bottom: 10px;
            }

            .contact-form-card .subtitle {
                color: var(--text-gray);
                margin-bottom: 30px;
                font-size: 15px;
            }

            /* Alert Messages */
            .alert {
                padding: 15px 20px;
                border-radius: 12px;
                margin-bottom: 25px;
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 14px;
                animation: slideDown 0.4s ease;
            }

            .alert-success {
                background: #d1fae5;
                color: #065f46;
                border: 1px solid #6ee7b7;
            }

            .alert-error {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #fca5a5;
            }

            .alert i {
                font-size: 18px;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Form Styles */
            .form-group {
                margin-bottom: 20px;
            }

            .form-group label {
                display: block;
                font-weight: 600;
                color: var(--text-dark);
                margin-bottom: 8px;
                font-size: 14px;
            }

            .form-group label .required {
                color: var(--error-red);
            }

            .form-group input,
            .form-group textarea,
            .form-group select {
                width: 100%;
                padding: 12px 18px;
                border: 2px solid var(--border-gray);
                border-radius: 10px;
                font-size: 15px;
                transition: all 0.3s ease;
                font-family: 'Poppins', sans-serif;
                background: white;
            }

            .form-group input:focus,
            .form-group textarea:focus,
            .form-group select:focus {
                outline: none;
                border-color: var(--primary-blue);
                box-shadow: 0 0 0 3px rgba(0, 73, 157, 0.1);
            }

            .form-group textarea {
                resize: vertical;
                min-height: 120px;
            }

            .btn-submit {
                background: linear-gradient(135deg, var(--primary-orange), #ff6b00);
                color: white;
                border: none;
                padding: 15px 40px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 16px;
                cursor: pointer;
                transition: all 0.3s ease;
                width: 100%;
                box-shadow: 0 4px 15px rgba(255, 131, 3, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }

            .btn-submit:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(255, 131, 3, 0.4);
            }

            .btn-submit:active {
                transform: translateY(-1px);
            }

            .btn-submit.loading {
                pointer-events: none;
                opacity: 0.7;
            }

            /* Map Section */
            .map-section {
                background: white;
                padding: 40px;
                border-radius: 20px;
                box-shadow: var(--shadow);
                border: 1px solid var(--border-gray);
                margin-bottom: 40px;
            }

            .map-section h2 {
                font-size: 28px;
                color: var(--primary-blue);
                margin-bottom: 10px;
            }

            .map-section .subtitle {
                color: var(--text-gray);
                margin-bottom: 20px;
                font-size: 15px;
            }

            .map-container {
                width: 100%;
                height: 400px;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                position: relative;
            }

            .map-container iframe {
                width: 100%;
                height: 100%;
                border: none;
            }

            /* CTA Section */
            .cta-section {
                background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
                padding: 60px 20px;
                text-align: center;
                color: white;
                margin: 0;
            }

            .cta-content {
                max-width: 800px;
                margin: 0 auto;
            }

            .cta-section h2 {
                font-size: 36px;
                margin-bottom: 15px;
            }

            .cta-section p {
                font-size: 18px;
                opacity: 0.9;
                margin-bottom: 30px;
            }

            .cta-buttons {
                display: flex;
                gap: 20px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn-cta {
                padding: 15px 35px;
                border-radius: 50px;
                font-weight: 600;
                font-size: 16px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 10px;
            }

            .btn-cta-primary {
                background: var(--primary-orange);
                color: white;
                border: 2px solid var(--primary-orange);
            }

            .btn-cta-secondary {
                background: transparent;
                color: white;
                border: 2px solid white;
            }

            .btn-cta:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
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
                cursor: pointer;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                box-shadow: 0 4px 15px rgba(255, 131, 3, 0.3);
                transition: all 0.3s ease;
                z-index: 999;
            }

            .scroll-top:hover {
                transform: translateY(-5px);
                box-shadow: 0 6px 20px rgba(255, 131, 3, 0.4);
            }

            .scroll-top.show {
                display: flex;
            }

            /* Responsive Design */
            @media (max-width: 968px) {
                .kontak-grid {
                    grid-template-columns: 1fr;
                }
                
                .page-header {
                    background-attachment: scroll;
                }
            }

            @media (max-width: 768px) {
                .page-header {
                    padding: 80px 20px 40px;
                    background-attachment: scroll;
                }

                .page-header h1 {
                    font-size: 32px;
                }

                .page-header p {
                    font-size: 16px;
                }

                .contact-info-card,
                .contact-form-card,
                .map-section {
                    padding: 30px 20px;
                }

                .map-container {
                    height: 300px;
                }

                .cta-section h2 {
                    font-size: 28px;
                }

                .cta-buttons {
                    flex-direction: column;
                    align-items: stretch;
                }

                .btn-cta {
                    width: 100%;
                    justify-content: center;
                }
            }

            @media (max-width: 480px) {
                .page-header h1 {
                    font-size: 26px;
                }

                .contact-info-card h2,
                .contact-form-card h2,
                .map-section h2 {
                    font-size: 22px;
                }

                .map-container {
                    height: 250px;
                }

                .scroll-top {
                    width: 45px;
                    height: 45px;
                    bottom: 20px;
                    right: 20px;
                }
            }
        </style>
    </head>
    <body>
        <?php include 'include/nav.php'; ?>

        <!-- Page Header -->
        <section class="page-header">
            <h1>Hubungi Kami</h1>
            <p>Kami siap membantu Anda dengan informasi PPDB, program sekolah, dan kerja sama</p>
        </section>

        <!-- Contact Container -->
        <div class="kontak-container">
            <div class="kontak-grid">
                <!-- Contact Info -->
                <div class="contact-info-card">
                    <h2>Informasi Kontak</h2>
                    <p class="subtitle">Hubungi kami melalui berbagai saluran komunikasi</p>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Alamat Sekolah</h3>
                            <p>Jl. Sangkuriang No.34-36, Cimahi<br>Jawa Barat, Indonesia 40512</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h3>Telepon</h3>
                            <p>
                                <a href="tel:+622212345678">+62 22 1234 5678</a> (Kantor)<br>
                                <a href="tel:+6281234567890">+62 812 3456 7890</a> (Tata Usaha)
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Email</h3>
                            <p>
                                <a href="mailto:info@smktignc.sch.id">info@smktignc.sch.id</a>
                            </p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h3>Jam Operasional</h3>
                            <p>
                                <strong>Senin - Jumat:</strong> 07:00 - 16:00 WIB<br>
                                <strong>Sabtu:</strong> 07:00 - 14:00 WIB<br>
                                <strong>Minggu & Libur:</strong> Tutup
                            </p>
                        </div>
                    </div>

                    <div class="social-media">
                        <h3><i class="fas fa-share-alt"></i> Ikuti Media Sosial Kami</h3>
                        <div class="social-links">
                            <a href="#" target="_blank" title="Instagram" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" target="_blank" title="YouTube" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" target="_blank" title="TikTok" aria-label="TikTok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form-card">
                    <h2><i class="fas fa-paper-plane"></i> Kirim Pesan</h2>
                    <p class="subtitle">Isi formulir di bawah dan kami akan merespons dalam 1x24 jam</p>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span><?= $success_message ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span><?= $error_message ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="contactForm">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap Anda" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">Alamat Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="contoh@email.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="telepon">Nomor Telepon / WhatsApp</label>
                            <input type="tel" id="telepon" name="telepon" placeholder="08xxxxxxxxxx" value="<?= isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="subjek">Subjek Pesan <span class="required">*</span></label>
                            <select id="subjek" name="subjek" required>
                                <option value="">-- Pilih Subjek --</option>
                                <option value="Informasi PPDB" <?= (isset($_POST['subjek']) && $_POST['subjek'] == 'Informasi PPDB') ? 'selected' : '' ?>>Informasi PPDB</option>
                                <option value="Informasi Jurusan" <?= (isset($_POST['subjek']) && $_POST['subjek'] == 'Informasi Jurusan') ? 'selected' : '' ?>>Informasi Jurusan</option>
                                <option value="Kunjungan Sekolah" <?= (isset($_POST['subjek']) && $_POST['subjek'] == 'Kunjungan Sekolah') ? 'selected' : '' ?>>Kunjungan Sekolah</option>
                                <option value="Kerja Sama" <?= (isset($_POST['subjek']) && $_POST['subjek'] == 'Kerja Sama') ? 'selected' : '' ?>>Kerja Sama & Kemitraan</option>
                                <option value="Pengaduan" <?= (isset($_POST['subjek']) && $_POST['subjek'] == 'Pengaduan') ? 'selected' : '' ?>>Pengaduan</option>
                                <option value="Lainnya" <?= (isset($_POST['subjek']) && $_POST['subjek'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pesan">Pesan Anda <span class="required">*</span></label>
                            <textarea id="pesan" name="pesan" required placeholder="Tulis pesan Anda di sini..."><?= isset($_POST['pesan']) ? htmlspecialchars($_POST['pesan']) : '' ?></textarea>
                        </div>

                        <button type="submit" name="submit_kontak" class="btn-submit" id="submitBtn">
                            <i class="fas fa-paper-plane"></i>
                            <span>Kirim Pesan</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Map Section -->
            <div class="map-section">
                <h2><i class="fas fa-map-marked-alt"></i> Lokasi Kami</h2>
                <p class="subtitle">📍 Jl. Sangkuriang No.34-36, Cimahi, Jawa Barat 40512</p>
                <div class="map-container">
                    <!-- Google Maps dengan marker SMK TI Garuda Nusantara Cimahi -->
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3672.140180472476!2d107.53971757463397!3d-6.895364793103795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e59b48322cdb%3A0x10a755b12e9aef37!2sBITC%20(Baros%20Information%2C%20Technology%2C%20%26%20Creative%20Center!5e1!3m2!1sid!2sid!4v1761192164228!5m2!1sid!2sid" 
                        width="600" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi SMK TI Garuda Nusantara Cimahi">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="cta-content">
                <h2>🎓 Tertarik Bergabung dengan Kami?</h2>
                <p>Dapatkan informasi lengkap tentang program pendidikan dan proses pendaftaran di SMK TI Garuda Nusantara</p>
                <div class="cta-buttons">
                    <a href="ppdb.php" class="btn-cta btn-cta-primary">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </a>
                    <a href="Brosur.pdf" download class="btn-cta btn-cta-secondary">
                        <i class="fas fa-download"></i> Download Brosur
                    </a>
                </div>
            </div>
        </section>

        <!-- Scroll to Top Button -->
        <button class="scroll-top" id="scrollTop" aria-label="Scroll to top">
            <i class="fas fa-arrow-up"></i>
        </button>

        <?php include 'include/footer.php'; ?>

        <script>
            // Form submission with loading state
            const contactForm = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            
            contactForm.addEventListener('submit', function(e) {
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Mengirim...</span>';
                submitBtn.disabled = true;
            });

            // Auto hide alerts after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    alert.style.animation = 'slideUp 0.3s ease';
                    setTimeout(() => alert.remove(), 300);
                });
            }, 5000);

            // Scroll to top button
            const scrollTopBtn = document.getElementById('scrollTop');
            
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            });

            scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Smooth scroll for all anchor links
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

            // Form validation enhancement
            const emailInput = document.getElementById('email');
            const teleponInput = document.getElementById('telepon');

            teleponInput.addEventListener('input', function(e) {
                // Allow only numbers
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </body>
    </html>