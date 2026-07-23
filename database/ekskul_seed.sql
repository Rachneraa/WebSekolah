CREATE TABLE IF NOT EXISTS `ekstrakulikuler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar_hero` varchar(255) NOT NULL,
  `pembina_nama` varchar(100) NOT NULL,
  `pembina_role` varchar(100) NOT NULL,
  `pembina_foto` varchar(255) NOT NULL,
  `jadwal` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ekskul_prestasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ekskul_id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ekskul_id` (`ekskul_id`),
  CONSTRAINT `fk_ekskul_prestasi` FOREIGN KEY (`ekskul_id`) REFERENCES `ekstrakulikuler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ekskul_galeri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ekskul_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ekskul_id` (`ekskul_id`),
  CONSTRAINT `fk_ekskul_galeri` FOREIGN KEY (`ekskul_id`) REFERENCES `ekstrakulikuler` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clear old data if any (for idempotency)
DELETE FROM `ekstrakulikuler`;

-- Insert Data 1: Paskibra
INSERT INTO `ekstrakulikuler` (`id`, `slug`, `nama`, `kategori`, `deskripsi`, `gambar_hero`, `pembina_nama`, `pembina_role`, `pembina_foto`, `jadwal`) VALUES
(1, 'paskibra', 'Paskibra', 'Kepemimpinan & Kedisiplinan', 'Wadah pengembangan bakat, minat, dan potensi siswa di luar jam pelajaran kurikuler. Membentuk kedisiplinan, ketahanan fisik, serta jiwa patriotisme melalui latihan baris-berbaris dan upacara. Bergabunglah dan temukan passion Anda bersama kami.', 'https://images.unsplash.com/photo-1526976668912-1a811878dd37?auto=format&fit=crop&w=800&q=80', 'Susan S.T', 'Pembina Ekstrakulikuler Paskibra', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80', 'Setiap Selasa & Kamis (15:00 - 17:00 WIB)');

INSERT INTO `ekskul_prestasi` (`ekskul_id`, `judul`, `deskripsi`, `icon`) VALUES
(1, 'Juara Umum Paskibra', 'Tingkat Provinsi Jawa Barat 2023', 'fa-trophy'),
(1, 'Juara 1 Lomba Baris Berbaris', 'Tingkat Nasional 2023', 'fa-medal'),
(1, 'Formasi Pengibaran Terbaik', 'Festival Kemerdekaan RI 2022', 'fa-star');

INSERT INTO `ekskul_galeri` (`ekskul_id`, `gambar`) VALUES
(1, 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80'),
(1, 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80'),
(1, 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80'),
(1, 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80'),
(1, 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80');

-- Insert Data 2: Pramuka
INSERT INTO `ekstrakulikuler` (`id`, `slug`, `nama`, `kategori`, `deskripsi`, `gambar_hero`, `pembina_nama`, `pembina_role`, `pembina_foto`, `jadwal`) VALUES
(2, 'pramuka', 'Pramuka', 'Kepemimpinan & Kemandirian', 'Melatih kemandirian, kecintaan pada alam, kebersamaan, dan keterampilan kepanduan yang solid. Pramuka SMPN Cimahi aktif berpartisipasi dalam Jambore Daerah dan Nasional.', 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=800&q=80', 'Dra. Siti Rahma', 'Pembina Ekstrakulikuler Pramuka', 'https://images.unsplash.com/photo-1580894732444-8ecded7900cd?auto=format&fit=crop&w=600&q=80', 'Setiap Jumat (14:00 - 16:00 WIB)');

INSERT INTO `ekskul_prestasi` (`ekskul_id`, `judul`, `deskripsi`, `icon`) VALUES
(2, 'Regu Inti Terbaik', 'Jambore Daerah 2023', 'fa-campground'),
(2, 'Juara 1 Pionering', 'Lomba Tingkat Cabang 2022', 'fa-knot'),
(2, 'Penghargaan Pramuka Garuda', 'Kwartir Nasional 2023', 'fa-award');

INSERT INTO `ekskul_galeri` (`ekskul_id`, `gambar`) VALUES
(2, 'https://images.unsplash.com/photo-1519055548599-6d4d129508c4?auto=format&fit=crop&w=800&q=80'),
(2, 'https://images.unsplash.com/photo-1537225228614-56cc3556d7ed?auto=format&fit=crop&w=800&q=80'),
(2, 'https://images.unsplash.com/photo-1464207687429-750564f0ea53?auto=format&fit=crop&w=800&q=80'),
(2, 'https://images.unsplash.com/photo-1517760444937-f6397edcbbcd?auto=format&fit=crop&w=800&q=80'),
(2, 'https://images.unsplash.com/photo-1442115599026-6a4a625a297e?auto=format&fit=crop&w=800&q=80');

-- Insert Data 3: English Club
INSERT INTO `ekstrakulikuler` (`id`, `slug`, `nama`, `kategori`, `deskripsi`, `gambar_hero`, `pembina_nama`, `pembina_role`, `pembina_foto`, `jadwal`) VALUES
(3, 'english', 'English Club', 'Seni & Bahasa', 'Wadah berkomunikasi bahasa Inggris melalui speech, storytelling, debate, dan publikasi berbahasa asing. Kami menyiapkan siswa untuk siap bersaing dalam era globalisasi.', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80', 'Mr. John Smith, M.Ed', 'Pembina English Club', 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=600&q=80', 'Setiap Rabu (15:00 - 16:30 WIB)');

INSERT INTO `ekskul_prestasi` (`ekskul_id`, `judul`, `deskripsi`, `icon`) VALUES
(3, '1st Winner English Debate', 'National High School Competition 2023', 'fa-comments'),
(3, 'Best Speaker', 'Provincial English Olympic 2022', 'fa-microphone-lines'),
(3, 'Top 10 Storytelling', 'International Youth Fest', 'fa-book-open');

INSERT INTO `ekskul_galeri` (`ekskul_id`, `gambar`) VALUES
(3, 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80'),
(3, 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80'),
(3, 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80'),
(3, 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=800&q=80'),
(3, 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80');
