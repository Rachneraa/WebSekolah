<?php include 'include/nav.php' ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPL - Rekayasa Perangkat Lunak</title>
    
    <!-- PWA META TAGS -->
    <meta name="theme-color" content="#00499D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC">
    
    <!-- ICONS -->
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.json">
    
    <style>
        :root {
            --primary-blue: #00499d;
            --primary-orange: #ff8303;
            --dark-blue: #003366;
            --light-blue: #e8f4ff;
            --text-dark: #222;
            --text-gray: #555;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.15);
            --section-white: #fff;
            --section-gray: #f5f5f5;
            --border-gray: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            opacity: 0;
            animation: fadeIn 0.3s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Hero Section */
        .hero {
            background: url('assets/7.png') center/cover no-repeat fixed;
            width: 100vw;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 80px 20px 40px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(45, 91, 74, 0.1) 0%, transparent 30%, rgba(246, 191, 30, 0.05) 70%, transparent 100%);
            animation: gradientShift 8s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(to top, #FEFCF8 0%, rgba(254, 252, 248, 0.95) 20%, rgba(254, 252, 248, 0.8) 40%, rgba(254, 252, 248, 0.5) 60%, rgba(254, 252, 248, 0.2) 80%, transparent 100%);
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .hero-content {
            max-width: 700px;
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease-out 0.3s both;
            top: -70px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 25px;
            font-weight: 800;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 35px;
            opacity: 0.95;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            line-height: 1.7;
            color: #e0e0e0;
        }

        /* Section Styling */
        section {
            padding: 4rem 5%;
            max-width: 100vw;
            margin: 0 calc(50% - 50vw);
            background: var(--section-white);
            box-sizing: border-box;
        }

        section:nth-of-type(2) { background: var(--section-white); }
        section:nth-of-type(3) { background: var(--section-gray); }
        section:nth-of-type(4) { background: var(--section-white); }
        section:nth-of-type(5) { background: var(--section-gray); }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            position: relative;
            padding-bottom: 1rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: #0066cc;
        }

        /* Tentang RPL */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        @media (max-width: 768px) {
            .about-content {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
            .about-image {
                width: 100%;
                height: 220px;
                margin-bottom: 0;
                order: 1;
            }
            .about-text { order: 2; }
        }

        .about-text {
            line-height: 1.8;
            color: #555;
        }

        .about-image {
            width: 100%;
            height: 350px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .about-img-fit {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
            display: block;
        }

        /* Kurikulum */
        .kurikulum-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .kurikulum-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .kurikulum-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .kurikulum-card h3 {
            color: #00bcd4;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #00bcd4;
        }

        .kurikulum-card ul {
            list-style: none;
            padding-left: 0;
        }

        .kurikulum-card li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .kurikulum-card li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #0066cc;
            font-weight: bold;
        }

        /* Kegiatan Slider */
        .kegiatan-slider {
            position: relative;
            overflow: hidden;
            padding: 2rem 0;
            background: #fff;
        }

        .slider-container {
            display: flex;
            gap: 2rem;
            transition: transform 0.3s ease;
            padding: 0 10px;
            will-change: transform;
        }

        .kegiatan-card {
            min-width: calc(33.333% - 1.5rem);
            max-width: calc(33.333% - 1.5rem);
            flex: 0 0 calc(33.333% - 1.5rem);
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
        }

        .kegiatan-card:hover {
            transform: translateY(-5px);
        }

        .kegiatan-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .kegiatan-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0;
        }

        .kegiatan-date {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: #00cc66;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: bold;
        }

        .kegiatan-content {
            padding: 1.5rem;
        }

        .kegiatan-content h3 {
            margin-bottom: 0.5rem;
            color: #333;
        }

        .kegiatan-content p {
            color: #666;
            font-size: 0.9rem;
        }

        .slider-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .slider-btn {
            background: #0066cc;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background 0.3s;
        }

        .slider-btn:hover {
            background: #0052a3;
        }

        @media (max-width: 1024px) {
            .kegiatan-card {
                min-width: calc(50% - 1rem);
                max-width: calc(50% - 1rem);
                flex: 0 0 calc(50% - 1rem);
            }
        }

        @media (max-width: 768px) {
            .kegiatan-card {
                min-width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            .slider-container { padding: 0 10px; }
        }

        /* FAQ */
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #333;
            transition: background 0.3s;
        }

        .faq-question:hover {
            background: #f5f5f5;
        }

        .faq-icon {
            font-size: 1.5rem;
            transition: transform 0.3s;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            padding: 0 1.5rem;
            color: #666;
            line-height: 1.8;
        }

        .faq-item.active .faq-answer {
            max-height: 500px;
            padding: 0 1.5rem 1.5rem 1.5rem;
        }

        @media (max-width: 480px) {
            .hero h1 { font-size: 1.8rem; }
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="badge-container"></div>
            <h1>Rekayasa Perangkat Lunak</h1>
            <p>Coding, Debugging, Let's Running</p>
        </div>
    </section>

    <!-- Tentang RPL -->
    <section id="tentang">
        <h2 class="section-title">Tentang RPL</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Jurusan <b>Rekayasa Perangkat Lunak (RPL)</b> di <b>SMK TI Garuda Nusantara Cimahi</b> berfokus pada
                    pengembangan perangkat lunak, aplikasi web, mobile, dan desktop. Siswa dibekali keterampilan dalam
                    pemrograman, database, dan desain antarmuka menggunakan berbagai bahasa seperti Java, PHP, HTML,
                    CSS, dan JavaScript. Pembelajaran didukung oleh guru profesional serta kegiatan organisasi
                    <b>CODE-X</b> yang mendorong kreativitas dan inovasi siswa. Lulusan RPL memiliki prospek kerja luas
                    sebagai <i>programmer</i>, <i>web developer</i>, <i>game developer</i>, atau <i>IT consultant</i>,
                    serta siap bersaing di dunia industri teknologi modern.
                </p>
            </div>
            <div class="about-image">
                <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
            </div>
        </div>
    </section>

    <!-- Kurikulum -->
    <section>
        <h2 class="section-title">Kurikulum</h2>
        <div class="kurikulum-grid">
            <div class="kurikulum-card">
                <h3>Tahun Pertama</h3>
                <ul>
                    <li>Dasar-dasar Pemrograman</li>
                    <li>Algoritma dan Struktur Data</li>
                    <li>Basis Data Fundamental</li>
                    <li>Web Design Dasar</li>
                </ul>
            </div>
            <div class="kurikulum-card">
                <h3>Tahun Kedua</h3>
                <ul>
                    <li>Pemrograman Berorientasi Objek</li>
                    <li>Database Management</li>
                    <li>Web Development</li>
                    <li>Mobile Programming</li>
                </ul>
            </div>
            <div class="kurikulum-card">
                <h3>Tahun Ketiga</h3>
                <ul>
                    <li>Software Engineering</li>
                    <li>UI/UX Design</li>
                    <li>Project Management</li>
                    <li>Kerja Praktek Industri</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Kegiatan -->
    <section id="kontak">
        <h2 class="section-title">Kegiatan</h2>
        <div class="kegiatan-slider">
            <div class="slider-container" id="kegiatanSliderContainer">
                <div class="kegiatan-card">
                    <div class="kegiatan-image">
                        <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
                        <div class="kegiatan-date">06</div>
                    </div>
                    <div class="kegiatan-content">
                        <h3>Homeschooling in...</h3>
                        <p>This article highlights some of the media coverag...</p>
                    </div>
                </div>
                <div class="kegiatan-card">
                    <div class="kegiatan-image">
                        <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
                        <div class="kegiatan-date">05</div>
                    </div>
                    <div class="kegiatan-content">
                        <h3>International Universities...</h3>
                        <p>Are you looking for international universities in Indonesia?...</p>
                    </div>
                </div>
                <div class="kegiatan-card">
                    <div class="kegiatan-image">
                        <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
                        <div class="kegiatan-date">22</div>
                    </div>
                    <div class="kegiatan-content">
                        <h3>Homeschooling International...</h3>
                        <p>Belajar banyak pilihan homeschooling di Jakarta dan Tangerang...</p>
                    </div>
                </div>
                <div class="kegiatan-card">
                    <div class="kegiatan-image">
                        <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
                        <div class="kegiatan-date">15</div>
                    </div>
                    <div class="kegiatan-content">
                        <h3>Workshop Coding</h3>
                        <p>Workshop intensif tentang pengembangan aplikasi mobile...</p>
                    </div>
                </div>
                <div class="kegiatan-card">
                    <div class="kegiatan-image">
                        <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
                        <div class="kegiatan-date">10</div>
                    </div>
                    <div class="kegiatan-content">
                        <h3>Kunjungan Industri</h3>
                        <p>Siswa RPL mengunjungi perusahaan teknologi terkemuka...</p>
                    </div>
                </div>
                <div class="kegiatan-card">
                    <div class="kegiatan-image">
                        <img src="assets/rpl_tentang.jpg" alt="Kegiatan Siswa RPL" class="about-img-fit">
                        <div class="kegiatan-date">06</div>
                    </div>
                    <div class="kegiatan-content">
                        <h3>Homeschooling in...</h3>
                        <p>This article highlights some of the media coverag...</p>
                    </div>
                </div>
            </div>
            <div class="slider-nav">
                <button class="slider-btn" onclick="slideLeft()">‹</button>
                <button class="slider-btn" onclick="slideRight()">›</button>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section>
        <h2 class="section-title">FAQ</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    Apa itu jurusan RPL?
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    RPL (Rekayasa Perangkat Lunak) adalah jurusan yang mempelajari tentang pengembangan perangkat lunak
                    termasuk pembuatan, pemeliharaan, manajemen organisasi dan pengembangan aplikasi.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    Apa saja yang dipelajari di RPL?
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    Di RPL, siswa akan mempelajari pemrograman web, mobile, desktop, database, algoritma, dan konsep
                    pengembangan perangkat lunak modern.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    Bagaimana prospek kerja lulusan RPL?
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    Lulusan RPL memiliki prospek kerja yang sangat luas, seperti Web Developer, Mobile Developer,
                    Software Engineer, System Analyst, dan berbagai posisi di bidang IT.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    Apakah siswa RPL mendapat sertifikasi?
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    Ya, siswa RPL akan mendapatkan kesempatan untuk mengikuti sertifikasi kompetensi yang diakui
                    industri seperti sertifikasi pemrograman dan pengembangan aplikasi.
                </div>
            </div>
        </div>
    </section>

    <script>
        let currentSlide = 0;
        
        function getCardsToShow() {
            return window.innerWidth <= 768 ? 1 : 3;
        }
        
        function slideRight() {
            const cards = document.querySelectorAll('#kegiatanSliderContainer .kegiatan-card');
            const cardsToShow = getCardsToShow();
            const maxSlide = cards.length - cardsToShow;
            if (currentSlide < maxSlide) {
                currentSlide++;
                updateSlider();
            }
        }
        
        function slideLeft() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSlider();
            }
        }
        
        function updateSlider() {
            const card = document.querySelector('#kegiatanSliderContainer .kegiatan-card');
            const sliderContainer = document.getElementById('kegiatanSliderContainer');
            if (!card || !sliderContainer) return;
            const sliderStyles = window.getComputedStyle(sliderContainer);
            const gap = parseInt(sliderStyles.gap) || 0;
            const cardWidth = card.offsetWidth;
            const offset = -(currentSlide * (cardWidth + gap));
            sliderContainer.style.transform = `translateX(${offset}px)`;
        }
        
        window.addEventListener('resize', () => {
            currentSlide = 0;
            updateSlider();
        });
        
        updateSlider();

        function toggleFaq(element) {
            const faqItem = element.parentElement;
            const faqAnswer = faqItem.querySelector('.faq-answer');

            // Close other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem && item.classList.contains('active')) {
                    item.classList.remove('active');
                }
            });

            // Toggle current FAQ
            faqItem.classList.toggle('active');
        }

        // SERVICE WORKER REGISTRATION
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('./sw.js')
                .then(registration => console.log('✅ SW Registered di RPL page'))
                .catch(error => console.log('❌ SW Registration failed:', error));
        }
    </script>
</body>
</html>
<?php include 'include/footer.php' ?>