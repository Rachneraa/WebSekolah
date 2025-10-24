-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 24 Okt 2025 pada 08.32
-- Versi server: 8.4.3
-- Versi PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `sekolah_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `absensi_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `guru_id` int DEFAULT NULL,
  `total_siswa` int NOT NULL,
  `hadir` int NOT NULL,
  `sakit` int NOT NULL,
  `izin` int NOT NULL,
  `alpha` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`absensi_id`, `tanggal`, `kelas_id`, `guru_id`, `total_siswa`, `hadir`, `sakit`, `izin`, `alpha`, `created_at`) VALUES
(7, '2025-10-23', 23, NULL, 0, 0, 0, 0, 0, '2025-10-23 06:46:47'),
(8, '2025-10-23', 24, NULL, 0, 0, 0, 0, 0, '2025-10-23 07:46:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi_alasan`
--

CREATE TABLE `absensi_alasan` (
  `id` int NOT NULL,
  `absensi_id` int NOT NULL,
  `alasan` text,
  `dibuat_oleh` enum('admin','guru') NOT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `siswa_id` int DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `kelas_id` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `absensi_alasan`
--

INSERT INTO `absensi_alasan` (`id`, `absensi_id`, `alasan`, `dibuat_oleh`, `dibuat_pada`, `siswa_id`, `status`, `kelas_id`, `tanggal`) VALUES
(1, 7, 'w', 'admin', '2025-10-23 07:05:36', 39, 'Izin', 23, '2025-10-23'),
(2, 8, 'flu', 'admin', '2025-10-23 07:46:27', 35, 'Izin', 24, '2025-10-23');

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi_detail`
--

CREATE TABLE `absensi_detail` (
  `id` int NOT NULL,
  `absensi_id` int DEFAULT NULL,
  `siswa_id` int DEFAULT NULL,
  `status` enum('Hadir','Sakit','Izin','Alpha') COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `kelas_id` int NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_absen` time NOT NULL,
  `nama_siswa` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi_detail`
--

INSERT INTO `absensi_detail` (`id`, `absensi_id`, `siswa_id`, `status`, `keterangan`, `kelas_id`, `tanggal`, `waktu_absen`, `nama_siswa`) VALUES
(12, 7, 36, 'Hadir', NULL, 23, '2025-10-23', '06:49:39', ''),
(15, 7, 40, 'Hadir', NULL, 23, '2025-10-23', '07:12:24', ''),
(16, 8, 41, 'Hadir', NULL, 24, '2025-10-23', '07:51:56', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `berita`
--

CREATE TABLE `berita` (
  `id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `isi` text COLLATE utf8mb4_general_ci NOT NULL,
  `tags` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `penulis` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id`, `judul`, `isi`, `tags`, `gambar`, `tanggal`, `penulis`) VALUES
(7, 'Seekor monyet keluar dari kandangnya', '##tes\\r\\ntes\\r\\n##tes\\r\\ntes', '#waspada #viral', '68f88be8465ed.jpg', '2025-10-22 14:46:48', '1'),
(9, 'Durian musang king', '##SMK TI GARUDA NUSANTARA CIMAHI\\r\\nditemukan di sebelah gedung smk yang terletak di cimahi', 'Durian, SMK, Misteri', '68f9a11d523cb.jpg', '2025-10-23 10:29:33', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `guru`
--

CREATE TABLE `guru` (
  `id` int NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mata_pelajaran` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mapel_id` int DEFAULT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `no_telp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `guru`
--

INSERT INTO `guru` (`id`, `nip`, `nama`, `password`, `mata_pelajaran`, `created_at`, `mapel_id`, `jenis_kelamin`, `alamat`, `no_telp`, `foto`) VALUES
(11, '2488120', 'Imam ', '$2y$10$yMlGHLgxgatO2LZ86KHTteMSfN6zNAg.9MIgDkyW590pK2LXQ2k0m', NULL, '2025-10-24 08:23:17', 3, 'L', 'cimahi', '0888888888', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') COLLATE utf8mb4_general_ci NOT NULL,
  `jam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mapel` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `guru_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`id`, `nama`, `qr_code`) VALUES
(23, '12 RPL 3', 'kelas_23.png'),
(24, '12 DKV 1', 'kelas_24.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_history`
--

CREATE TABLE `login_history` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Hadir','Tidak Hadir') COLLATE utf8mb4_general_ci DEFAULT 'Hadir',
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `login_history`
--

INSERT INTO `login_history` (`id`, `siswa_id`, `login_time`, `status`, `foto`) VALUES
(10, 36, '2025-10-23 13:44:16', 'Hadir', NULL),
(11, 36, '2025-10-23 13:45:54', 'Hadir', NULL),
(12, 36, '2025-10-23 13:53:05', 'Hadir', NULL),
(14, 39, '2025-10-23 14:04:58', 'Hadir', NULL),
(15, 39, '2025-10-23 14:04:58', 'Hadir', NULL),
(16, 39, '2025-10-23 14:10:43', 'Hadir', NULL),
(17, 39, '2025-10-23 14:11:50', 'Hadir', NULL),
(18, 40, '2025-10-23 14:12:10', 'Hadir', NULL),
(19, 36, '2025-10-23 14:24:26', 'Hadir', NULL),
(20, 36, '2025-10-23 14:31:33', 'Hadir', NULL),
(21, 41, '2025-10-23 14:48:54', 'Hadir', NULL),
(22, 36, '2025-10-23 14:51:00', 'Hadir', NULL),
(23, 41, '2025-10-23 14:51:43', 'Hadir', NULL),
(24, 36, '2025-10-23 14:51:44', 'Hadir', NULL),
(25, 36, '2025-10-23 16:48:10', 'Hadir', NULL),
(26, 36, '2025-10-24 11:41:55', 'Hadir', NULL),
(27, 36, '2025-10-24 12:31:17', 'Hadir', NULL),
(28, 36, '2025-10-24 12:34:08', 'Hadir', NULL),
(29, 36, '2025-10-24 13:09:24', 'Hadir', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `activity` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `record_id` int DEFAULT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mapel`
--

CREATE TABLE `mapel` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mapel`
--

INSERT INTO `mapel` (`id`, `nama`) VALUES
(1, 'Matematika'),
(3, 'Pemrograman Perangakat Bergerak'),
(4, 'Kimia'),
(5, 'Pemrograman');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai`
--

CREATE TABLE `nilai` (
  `id` int NOT NULL,
  `siswa_id` int DEFAULT NULL,
  `kelas_id` int DEFAULT NULL,
  `mapel` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tugas` int NOT NULL,
  `uts` int NOT NULL,
  `uas` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rata-rata` decimal(3,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ppdb_pendaftar`
--

CREATE TABLE `ppdb_pendaftar` (
  `id` int NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') NOT NULL,
  `agama` varchar(20) NOT NULL,
  `tempat_lahir` varchar(50) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat_email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) NOT NULL,
  `nama_sekolah` varchar(100) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `status` enum('proses','diterima','ditolak') DEFAULT 'proses',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `ppdb_pendaftar`
--

INSERT INTO `ppdb_pendaftar` (`id`, `nisn`, `nama_lengkap`, `jenis_kelamin`, `agama`, `tempat_lahir`, `tanggal_lahir`, `alamat_email`, `no_hp`, `nama_sekolah`, `jurusan`, `status`, `created_at`) VALUES
(2, '20251230', 'fufu fafa', 'perempuan', 'islam', 'Cimahi', '2008-06-13', 'yuuitukeren@gmail.com', '089667853243', 'SMPN 16 CIMAHI', 'rpl', 'proses', '2025-10-24 03:36:13'),
(3, '20251230', 'fufu fafa', 'laki-laki', 'islam', 'Cimahi', '2008-06-13', 'yuuitukeren@gmail.com', '089667853243', 'SMPN 16 CIMAHI', 'rpl', 'proses', '2025-10-24 03:36:28'),
(4, '2025107', 'grace', 'perempuan', 'katolik', 'Cimindi', '2009-07-30', 'hantuapahayo@gmail.com', '0029192712', 'SMPN 10 Cimindi', 'dkv', 'diterima', '2025-10-24 03:37:33'),
(5, '345678', 'ohg', 'perempuan', 'katolik', 'pokjh', '2006-12-17', 'contojopj@gmail.com', '098765433456789', 'smpnjgjb', 'tkj', 'proses', '2025-10-24 05:59:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kelas_id` int DEFAULT NULL,
  `nama_kelas` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `login_status` enum('success','failed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_attendance` datetime DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`siswa_id`, `nama`, `username`, `password`, `kelas_id`, `nama_kelas`, `last_login`, `login_time`, `login_status`, `login_photo`, `last_attendance`, `nis`) VALUES
(35, 'Jessica', 'jeje', '$2y$10$ytWRUMklL6KaB8m1/4zkk.hxgFRDAB3/YkGQ2LR8foX8WaiqTVoTm', 24, '12 DKV 1', NULL, '2025-10-23 12:53:39', 'success', NULL, NULL, NULL),
(36, 'Rouf', 'up', '$2y$10$vyDkDs1K56wvM21GxMtKxuD1k7oKDTSxcytLE/jc6OcAlcp6egXO2', 23, '12 RPL 3', NULL, '2025-10-24 13:09:24', 'success', NULL, NULL, NULL),
(39, 'naafil', 'piu', '$2y$10$8aeilH8qNe04HS9oTaO.ZOf7ulUIx3OwudEpui1nlACjNm33ChL1i', 23, '12 RPL 3', NULL, '2025-10-23 14:11:50', 'success', NULL, NULL, NULL),
(40, 'grace', 'cece', '$2y$10$NBhvZe60Mp3WjxooIR0cqO4F7BSSgGTjVVJNiDYcpTgAl.VWQeX7e', 23, '12 RPL 3', NULL, '2025-10-23 14:12:10', 'success', NULL, NULL, NULL),
(41, 'pa hasan', 'siswa1', '$2y$10$8MluewbxRmBPE9SBp.5Hz.eeFd1rQoAM64PUvmmwCvVCoEmJs6rYy', 24, '12 DKV 1', NULL, '2025-10-23 14:51:43', 'success', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `siswa_per_kelas`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `siswa_per_kelas` (
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` enum('admin','guru') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mapel_ids` text COLLATE utf8mb4_general_ci,
  `mapel` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `created_at`, `mapel_ids`, `mapel`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-10-21 04:00:04', NULL, NULL),
(6, 'guru1', '$2y$10$Ym2zmy9u4Y9hLBAQZg.nRejO640/X3BfhMYK2JYunn9lGnxS/27Di', 'guru', '2025-10-21 08:49:02', NULL, 'Matematika'),
(7, 'guru2', '$2y$10$MSAK3aHGBvuJmre0girox.aRqkjjsA2IizjgZgSYqdClLkO.GmKni', 'guru', '2025-10-21 08:49:32', NULL, 'Matematika, Fisika'),
(10, '1', '$2y$10$t9B659Neim10sdQKtH4r4urY7OTmidiZrtDvRlR5cU1/JhsR8V4de', 'admin', '2025-10-24 07:22:41', NULL, NULL);

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`absensi_id`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `absensi_alasan`
--
ALTER TABLE `absensi_alasan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensi_id` (`absensi_id`);

--
-- Indeks untuk tabel `absensi_detail`
--
ALTER TABLE `absensi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `absensi_id` (`absensi_id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indeks untuk tabel `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `mapel_id` (`mapel_id`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `kelas_id` (`kelas_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama` (`nama`),
  ADD KEY `idx_kelas_nama` (`nama`);

--
-- Indeks untuk tabel `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`siswa_id`);

--
-- Indeks untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `mapel`
--
ALTER TABLE `mapel`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `ppdb_pendaftar`
--
ALTER TABLE `ppdb_pendaftar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`siswa_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `nis` (`nis`),
  ADD KEY `idx_siswa_kelas_id` (`kelas_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `absensi_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `absensi_alasan`
--
ALTER TABLE `absensi_alasan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `absensi_detail`
--
ALTER TABLE `absensi_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `ppdb_pendaftar`
--
ALTER TABLE `ppdb_pendaftar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswa_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------

--
-- Struktur untuk view `siswa_per_kelas`
--
DROP TABLE IF EXISTS `siswa_per_kelas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `siswa_per_kelas`  AS SELECT `k`.`id` AS `id_kelas`, `k`.`nama` AS `nama_kelas`, `s`.`id` AS `id_siswa`, `s`.`nama` AS `nama_siswa` FROM (`siswa` `s` join `kelas` `k` on((`s`.`kelas_id` = `k`.`id`))) ORDER BY `k`.`nama` ASC, `s`.`nama` ASC ;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `absensi_alasan`
--
ALTER TABLE `absensi_alasan`
  ADD CONSTRAINT `absensi_alasan_ibfk_1` FOREIGN KEY (`absensi_id`) REFERENCES `absensi` (`absensi_id`);

--
-- Ketidakleluasaan untuk tabel `absensi_detail`
--
ALTER TABLE `absensi_detail`
  ADD CONSTRAINT `absensi_detail_ibfk_1` FOREIGN KEY (`absensi_id`) REFERENCES `absensi` (`absensi_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `absensi_detail_ibfk_2` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `guru`
--
ALTER TABLE `guru`
  ADD CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`mapel_id`) REFERENCES `mapel` (`id`);

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`),
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `nilai`
--
ALTER TABLE `nilai`
  ADD CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`);

--
-- Ketidakleluasaan untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
