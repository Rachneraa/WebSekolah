-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Feb 2026 pada 11.49
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

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`u745165126_SMKTI`@`127.0.0.1` PROCEDURE `SyncAllAbsensi` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_kelas_id INT;
    DECLARE cur CURSOR FOR SELECT DISTINCT kelas_id FROM absensi_detail WHERE tanggal = '2025-11-01';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO v_kelas_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Panggil sync procedure untuk setiap kelas
        CALL SyncAbsensiSummary('2025-11-01', v_kelas_id);
    END LOOP;
    
    CLOSE cur;
END$$

DELIMITER ;

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
(9, '2025-10-24', 23, NULL, 0, 0, 0, 0, 0, '2025-10-24 08:50:43'),
(10, '2025-10-31', 23, NULL, 0, 0, 0, 0, 0, '2025-10-31 06:23:28'),
(13, '2025-11-01', 23, NULL, 2, 2, 0, 0, 0, '2025-11-01 08:56:00'),
(15, '2025-11-01', 28, NULL, 0, 1, 0, 0, 0, '2025-11-01 10:22:21'),
(18, '2025-11-02', 28, NULL, 0, 0, 1, 0, 0, '2025-11-02 01:51:23'),
(33, '2025-11-07', 23, NULL, 0, 1, 0, 0, 0, '2025-11-07 06:21:44'),
(34, '2025-11-07', 28, NULL, 0, 1, 0, 0, 0, '2025-11-07 09:43:04'),
(35, '2025-11-10', 23, NULL, 0, 1, 0, 0, 0, '2025-11-10 02:17:33'),
(40, '2025-11-13', 23, NULL, 0, 5, 0, 0, 0, '2025-11-13 02:18:07'),
(41, '2025-11-13', 28, NULL, 0, 4, 0, 0, 0, '2025-11-13 02:20:21'),
(49, '2025-11-22', 23, NULL, 0, 1, 0, 0, 0, '2025-11-22 03:47:18');

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
  `waktu_alasan` datetime DEFAULT NULL,
  `kelas_id` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `absensi_alasan`
--

INSERT INTO `absensi_alasan` (`id`, `absensi_id`, `alasan`, `dibuat_oleh`, `dibuat_pada`, `siswa_id`, `status`, `waktu_alasan`, `kelas_id`, `tanggal`) VALUES
(1, 7, 'w', 'admin', '2025-10-23 07:05:36', 39, 'Izin', NULL, 23, '2025-10-23'),
(3, 9, 'sakit', 'admin', '2025-10-24 09:51:27', 40, 'Izin', NULL, 23, '2025-10-24'),
(6, 0, 'batuk', 'admin', '2025-11-02 01:51:23', 40, 'Sakit', '2025-11-02 08:51:23', 28, '2025-11-02'),
(25, 48, 'sakit', 'guru', '2026-02-01 07:42:41', 6, 'Sakit', NULL, 28, '2026-02-01'),
(26, 49, 'tes', 'guru', '2026-02-13 18:02:48', 6, 'Izin', NULL, 28, '2026-02-14');

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
(1, 10, 36, 'Hadir', NULL, 23, '2025-10-31', '02:23:28', ''),
(12, 7, 36, 'Hadir', NULL, 23, '2025-10-23', '06:49:39', ''),
(15, 7, 40, 'Hadir', NULL, 23, '2025-10-23', '07:12:24', ''),
(17, 9, 36, 'Hadir', NULL, 23, '2025-10-24', '08:50:43', ''),
(18, 9, 39, 'Hadir', NULL, 23, '2025-10-24', '09:50:59', ''),
(21, 10, 40, 'Hadir', NULL, 23, '2025-10-31', '16:12:42', ''),
(23, 13, 36, 'Hadir', NULL, 23, '2025-11-01', '15:56:00', ''),
(24, 13, 44, 'Hadir', NULL, 23, '2025-11-01', '16:26:39', ''),
(25, 15, 40, 'Hadir', NULL, 28, '2025-11-01', '17:22:21', ''),
(28, 33, 36, 'Hadir', NULL, 23, '2025-11-07', '13:21:44', ''),
(29, 34, 49, 'Hadir', NULL, 28, '2025-11-07', '16:43:04', ''),
(30, 35, 36, 'Hadir', NULL, 23, '2025-11-10', '09:17:33', ''),
(32, 40, 36, 'Hadir', NULL, 23, '2025-11-13', '09:18:07', ''),
(33, 40, 39, 'Hadir', NULL, 23, '2025-11-13', '09:19:10', ''),
(34, 41, 49, 'Hadir', NULL, 28, '2025-11-13', '09:20:21', ''),
(35, 40, 56, 'Hadir', NULL, 23, '2025-11-13', '09:21:37', ''),
(36, 41, 57, 'Hadir', NULL, 28, '2025-11-13', '09:23:03', ''),
(37, 40, 40, 'Hadir', NULL, 23, '2025-11-13', '09:23:03', ''),
(39, 41, 59, 'Hadir', NULL, 28, '2025-11-13', '09:25:16', ''),
(41, 41, 6, 'Hadir', NULL, 28, '2025-11-13', '13:51:56', ''),
(42, 40, 13, 'Hadir', NULL, 23, '2025-11-13', '14:08:00', ''),
(46, 49, 73, 'Hadir', NULL, 23, '2025-11-22', '10:47:18', ''),
(48, NULL, 6, 'Sakit', NULL, 28, '2026-02-01', '14:42:41', 'Depita'),
(49, NULL, 6, 'Izin', NULL, 28, '2026-02-14', '01:02:48', 'Depita');

--
-- Trigger `absensi_detail`
--
DELIMITER $$
CREATE TRIGGER `after_absensi_detail_insert` AFTER INSERT ON `absensi_detail` FOR EACH ROW BEGIN
    -- Langsung update dengan count yang sederhana
    UPDATE absensi 
    SET hadir = (
        SELECT COUNT(*) FROM absensi_detail 
        WHERE kelas_id = NEW.kelas_id AND tanggal = NEW.tanggal AND status = 'Hadir'
    )
    WHERE tanggal = NEW.tanggal AND kelas_id = NEW.kelas_id;
END
$$
DELIMITER ;

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
  `penulis` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `views` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `berita`
--

INSERT INTO `berita` (`id`, `judul`, `isi`, `tags`, `gambar`, `tanggal`, `penulis`, `views`) VALUES
(12, 'Diklat 2025', 'HAHAHAHA', '#waspada #viral', '6924220f5fd76.png', '2025-11-24 16:14:55', '1', 0);

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
(46, '3', 'Pa ari', '$2y$10$RLC1dIqtkUgvwkR7AURA4eNdToHs7YpzBwtAt/Ykq3iW2OtsgN.Q.', NULL, '2025-11-24 09:00:00', 11, 'L', 'cimahi', '92817312', NULL),
(47, 'poly', 'sona andreas', '$2y$10$RLC1dIqtkUgvwkR7AURA4eNdToHs7YpzBwtAt/Ykq3iW2OtsgN.Q.', NULL, '2025-11-24 09:00:00', 6, 'L', 'bandoen', '0921813', NULL),
(48, '1234345', 'una', '$2y$10$RLC1dIqtkUgvwkR7AURA4eNdToHs7YpzBwtAt/Ykq3iW2OtsgN.Q.', NULL, '2025-11-24 09:00:00', 9, 'P', 'soreang', '92817312', NULL),
(49, '22', 'zildan', '$2y$10$RLC1dIqtkUgvwkR7AURA4eNdToHs7YpzBwtAt/Ykq3iW2OtsgN.Q.', NULL, '2025-11-24 09:00:00', 9, 'L', 'jalan jalan', '089767554227', NULL),
(50, '121', 'cece', '$2y$10$f8.yFOmCLQcvO97TN6QuaO7Mu49HLNaMQzV9KIeuRgthaDevmm99O', NULL, '2025-11-24 09:00:00', 6, 'L', 'langit', '09871', '697efa076f31f.webp'),
(52, '3214', 'kholik', '$2y$10$vmuUYuYeYcpz.x6ofkuUluHMwg7XlZH3RKzGquzXMUBe68d8dItS6', NULL, '2026-02-01 07:33:19', 6, 'P', 'Cigugur Tengah', '089667853243', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') COLLATE utf8mb4_general_ci NOT NULL,
  `jam` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `mapel_id` int DEFAULT NULL,
  `guru_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `kelas_id`, `hari`, `jam`, `mapel_id`, `guru_id`) VALUES
(25, 28, 'Kamis', '10:00-13:00', 6, 50);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kelas`
--

CREATE TABLE `kelas` (
  `kelas_id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kelas`
--

INSERT INTO `kelas` (`kelas_id`, `nama`, `qr_code`) VALUES
(23, '12 RPL 3', 'kelas_23.png'),
(28, '12 RPL 2', 'kelas_28.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontak_pesan`
--

CREATE TABLE `kontak_pesan` (
  `id` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subjek` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pesan` text COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Baru','Dibaca','Dibalas') COLLATE utf8mb4_general_ci DEFAULT 'Baru'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontak_pesan`
--

INSERT INTO `kontak_pesan` (`id`, `nama`, `email`, `telepon`, `subjek`, `pesan`, `tanggal`, `status`) VALUES
(5, 'haikal darunajmi', 'haikaldn02@gmail.com', '083114347263', 'Informasi Jurusan', 'test', '2025-11-07 13:46:19', 'Dibalas'),
(6, 'jaka', 'ahmadijaka89@gmail.com', '08211900646', 'Informasi PPDB', 'tos', '2025-11-07 16:31:17', 'Dibalas'),
(7, 'haikal darunajmi', 'haikaldarunnajmi01@gmail.com', '083114347263', 'Pengaduan', 'test', '2025-11-11 11:09:29', 'Dibalas'),
(8, 'wlee', 'HUJAN@gmail.com', '09823123', 'Kerja Sama', 'test', '2025-11-11 13:30:16', 'Dibalas'),
(10, 'haikal', 'cotoMakasar@gmail.com', '09231', 'Informasi PPDB', 'test', '2025-11-11 14:47:17', 'Baru'),
(11, 'aws', 'HUJAN@gmail.com', '098231287', 'Kunjungan Sekolah', 'test', '2025-11-11 15:18:45', 'Dibaca'),
(12, 'Zil', 'jayajaya@gmail.com', '08126634125', 'Informasi PPDB', 'Halo saya zildan', '2025-11-12 13:13:13', 'Dibaca'),
(14, 'Yuy', 'ayu@gmail.com', '08126634125', 'Kunjungan Sekolah', 'Mau donwload', '2025-11-13 09:05:57', 'Dibaca'),
(15, 'kholik', 'roufwanda@gmail.com', '089667853243', 'Informasi PPDB', 'tes', '2026-02-01 14:21:16', 'Baru');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_history`
--

CREATE TABLE `login_history` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Hadir','Tidak Hadir') COLLATE utf8mb4_general_ci DEFAULT 'Hadir',
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `login_history`
--

INSERT INTO `login_history` (`id`, `siswa_id`, `login_time`, `status`, `foto`, `ip_address`) VALUES
(10, 36, '2025-10-23 13:44:16', 'Hadir', NULL, NULL),
(11, 36, '2025-10-23 13:45:54', 'Hadir', NULL, NULL),
(12, 36, '2025-10-23 13:53:05', 'Hadir', NULL, NULL),
(14, 39, '2025-10-23 14:04:58', 'Hadir', NULL, NULL),
(15, 39, '2025-10-23 14:04:58', 'Hadir', NULL, NULL),
(16, 39, '2025-10-23 14:10:43', 'Hadir', NULL, NULL),
(17, 39, '2025-10-23 14:11:50', 'Hadir', NULL, NULL),
(18, 40, '2025-10-23 14:12:10', 'Hadir', NULL, NULL),
(19, 36, '2025-10-23 14:24:26', 'Hadir', NULL, NULL),
(20, 36, '2025-10-23 14:31:33', 'Hadir', NULL, NULL),
(21, 41, '2025-10-23 14:48:54', 'Hadir', NULL, NULL),
(22, 36, '2025-10-23 14:51:00', 'Hadir', NULL, NULL),
(23, 41, '2025-10-23 14:51:43', 'Hadir', NULL, NULL),
(24, 36, '2025-10-23 14:51:44', 'Hadir', NULL, NULL),
(25, 36, '2025-10-23 16:48:10', 'Hadir', NULL, NULL),
(26, 36, '2025-10-24 11:41:55', 'Hadir', NULL, NULL),
(27, 36, '2025-10-24 12:31:17', 'Hadir', NULL, NULL),
(28, 36, '2025-10-24 12:34:08', 'Hadir', NULL, NULL),
(29, 36, '2025-10-24 13:09:24', 'Hadir', NULL, NULL),
(30, 36, '2025-10-24 15:34:39', 'Hadir', NULL, NULL),
(31, 36, '2025-10-24 15:35:54', 'Hadir', NULL, NULL),
(32, 36, '2025-10-24 15:36:49', 'Hadir', NULL, NULL),
(33, 36, '2025-10-24 15:41:26', 'Hadir', NULL, NULL),
(34, 36, '2025-10-24 15:43:02', 'Hadir', NULL, NULL),
(35, 36, '2025-10-24 16:09:27', 'Hadir', NULL, NULL),
(36, 36, '2025-10-24 16:13:48', 'Hadir', NULL, NULL),
(37, 36, '2025-10-24 16:21:02', 'Hadir', NULL, NULL),
(38, 36, '2025-10-24 16:27:14', 'Hadir', NULL, NULL),
(39, 35, '2025-10-24 16:27:58', 'Hadir', NULL, NULL),
(40, 40, '2025-10-24 16:34:30', 'Hadir', NULL, NULL),
(41, 36, '2025-10-24 16:49:42', 'Hadir', NULL, NULL),
(42, 39, '2025-10-24 16:50:51', 'Hadir', NULL, NULL),
(43, 39, '2025-10-24 16:51:46', 'Hadir', NULL, NULL),
(44, 36, '2025-10-27 14:05:36', 'Hadir', NULL, NULL),
(45, 36, '2025-10-27 15:52:10', 'Hadir', NULL, NULL),
(46, 36, '2025-10-27 16:36:49', 'Hadir', NULL, NULL),
(47, 36, '2025-10-28 09:50:35', 'Hadir', NULL, NULL),
(48, 36, '2025-10-28 10:18:15', 'Hadir', NULL, NULL),
(49, 36, '2025-10-28 10:32:19', 'Hadir', NULL, NULL),
(50, 36, '2025-10-28 13:08:59', 'Hadir', NULL, NULL),
(51, 36, '2025-10-29 21:35:19', 'Hadir', NULL, NULL),
(52, 36, '2025-10-29 22:01:13', 'Hadir', NULL, NULL),
(53, 36, '2025-10-29 19:35:49', 'Hadir', NULL, NULL),
(54, 36, '2025-10-30 05:36:11', 'Hadir', NULL, NULL),
(55, 36, '2025-10-30 20:40:23', 'Hadir', NULL, NULL),
(56, 36, '2025-10-30 20:42:08', 'Hadir', NULL, NULL),
(57, 35, '2025-10-30 21:05:30', 'Hadir', NULL, NULL),
(58, 36, '2025-10-30 21:11:27', 'Hadir', NULL, NULL),
(59, 36, '2025-10-30 21:11:47', 'Hadir', NULL, NULL),
(60, 36, '2025-10-30 21:11:48', 'Hadir', NULL, NULL),
(61, 36, '2025-10-30 21:37:42', 'Hadir', NULL, NULL),
(62, 36, '2025-10-30 23:22:34', 'Hadir', NULL, NULL),
(63, 36, '2025-10-30 23:42:41', 'Hadir', NULL, NULL),
(64, 35, '2025-10-30 23:43:27', 'Hadir', NULL, NULL),
(65, 35, '2025-10-30 23:43:32', 'Hadir', NULL, NULL),
(66, 35, '2025-10-31 00:01:33', 'Hadir', NULL, NULL),
(67, 36, '2025-10-31 00:04:33', 'Hadir', NULL, NULL),
(68, 36, '2025-10-31 00:05:14', 'Hadir', NULL, NULL),
(69, 36, '2025-10-31 00:11:15', 'Hadir', NULL, NULL),
(70, 35, '2025-10-31 00:11:55', 'Hadir', NULL, NULL),
(71, 36, '2025-10-31 00:16:43', 'Hadir', NULL, NULL),
(72, 35, '2025-10-31 00:28:33', 'Hadir', NULL, NULL),
(73, 41, '2025-10-31 00:29:46', 'Hadir', NULL, NULL),
(74, 36, '2025-10-31 00:30:40', 'Hadir', NULL, NULL),
(75, 35, '2025-10-31 00:31:17', 'Hadir', NULL, NULL),
(76, 36, '2025-10-31 00:34:11', 'Hadir', NULL, NULL),
(77, 36, '2025-10-31 00:35:23', 'Hadir', NULL, NULL),
(78, 36, '2025-10-31 00:52:54', 'Hadir', NULL, NULL),
(79, 35, '2025-10-31 00:53:54', 'Hadir', NULL, NULL),
(80, 36, '2025-10-31 00:59:00', 'Hadir', NULL, NULL),
(81, 36, '2025-10-31 01:03:36', 'Hadir', NULL, NULL),
(82, 36, '2025-10-31 01:06:51', 'Hadir', NULL, NULL),
(83, 36, '2025-10-31 01:08:10', 'Hadir', NULL, NULL),
(84, 36, '2025-10-31 01:11:45', 'Hadir', NULL, NULL),
(85, 35, '2025-10-31 01:12:27', 'Hadir', NULL, NULL),
(86, 41, '2025-10-31 01:16:07', 'Hadir', NULL, NULL),
(87, 40, '2025-10-31 02:12:21', 'Hadir', NULL, NULL),
(88, 36, '2025-10-31 02:13:10', 'Hadir', NULL, NULL),
(89, 36, '2025-11-01 06:34:07', 'Hadir', NULL, NULL),
(90, 36, '2025-11-01 06:36:03', 'Hadir', NULL, NULL),
(91, 36, '2025-11-01 07:07:14', 'Hadir', NULL, NULL),
(92, 42, '2025-11-01 08:32:23', 'Hadir', NULL, NULL),
(93, 45, '2025-11-01 10:32:29', 'Hadir', NULL, NULL),
(94, 45, '2025-11-01 10:33:03', 'Hadir', NULL, NULL),
(95, 46, '2025-11-01 10:43:43', 'Hadir', NULL, NULL),
(96, 46, '2025-11-01 10:47:48', 'Hadir', NULL, NULL),
(97, 46, '2025-11-01 11:04:21', 'Hadir', NULL, NULL),
(98, 46, '2025-11-01 11:09:07', 'Hadir', NULL, NULL),
(99, 46, '2025-11-01 11:13:29', 'Hadir', NULL, NULL),
(100, 46, '2025-11-01 07:44:17', 'Hadir', NULL, NULL),
(101, 46, '2025-11-01 08:32:04', 'Hadir', NULL, NULL),
(102, 46, '2025-11-01 08:32:33', 'Hadir', NULL, NULL),
(103, 36, '2025-11-01 08:54:51', 'Hadir', NULL, NULL),
(104, 36, '2025-11-01 08:55:49', 'Hadir', NULL, NULL),
(105, 44, '2025-11-01 09:25:57', 'Hadir', NULL, NULL),
(106, 44, '2025-11-01 09:26:30', 'Hadir', NULL, NULL),
(107, 46, '2025-11-01 09:31:00', 'Hadir', NULL, NULL),
(108, 36, '2025-11-01 09:48:20', 'Hadir', NULL, NULL),
(109, 40, '2025-11-01 10:22:08', 'Hadir', NULL, NULL),
(110, 46, '2025-11-01 12:09:45', 'Hadir', NULL, NULL),
(111, 46, '2025-11-01 13:17:43', 'Hadir', NULL, NULL),
(112, 46, '2025-11-02 01:49:33', 'Hadir', NULL, NULL),
(113, 36, '2025-11-03 04:29:00', 'Hadir', NULL, NULL),
(114, 36, '2025-11-03 06:20:48', 'Hadir', NULL, NULL),
(115, 48, '2025-11-03 07:07:26', 'Hadir', NULL, NULL),
(116, 46, '2025-11-04 09:12:49', 'Hadir', NULL, NULL),
(117, 46, '2025-11-04 11:04:22', 'Hadir', NULL, NULL),
(118, 36, '2025-11-04 11:04:34', 'Hadir', NULL, NULL),
(119, 46, '2025-11-07 03:21:38', 'Hadir', NULL, NULL),
(120, 46, '2025-11-07 04:33:43', 'Hadir', NULL, NULL),
(121, 46, '2025-11-07 05:28:20', 'Hadir', NULL, NULL),
(122, 36, '2025-11-07 06:19:05', 'Hadir', NULL, NULL),
(123, 36, '2025-11-07 06:21:07', 'Hadir', NULL, NULL),
(124, 46, '2025-11-07 08:28:19', 'Hadir', NULL, NULL),
(125, 50, '2025-11-07 09:37:23', 'Hadir', NULL, NULL),
(126, 49, '2025-11-07 09:42:20', 'Hadir', NULL, NULL),
(127, 49, '2025-11-08 16:22:41', 'Hadir', NULL, NULL),
(128, 36, '2025-11-10 02:17:13', 'Hadir', NULL, NULL),
(129, 36, '2025-11-10 13:57:23', 'Hadir', NULL, NULL),
(130, 40, '2025-11-11 06:49:25', 'Hadir', NULL, NULL),
(131, 53, '2025-11-11 07:19:57', 'Hadir', NULL, NULL),
(132, 53, '2025-11-11 07:54:11', 'Hadir', NULL, NULL),
(133, 53, '2025-11-11 07:58:41', 'Hadir', NULL, NULL),
(134, 53, '2025-11-11 08:00:03', 'Hadir', NULL, NULL),
(135, 36, '2025-11-11 08:02:37', 'Hadir', NULL, NULL),
(136, 53, '2025-11-11 08:06:29', 'Hadir', NULL, NULL),
(137, 36, '2025-11-12 02:04:08', 'Hadir', NULL, NULL),
(138, 36, '2025-11-12 08:20:04', 'Hadir', NULL, NULL),
(139, 36, '2025-11-12 08:29:15', 'Hadir', NULL, NULL),
(140, 36, '2025-11-12 08:31:49', 'Hadir', NULL, NULL),
(141, 36, '2025-11-12 08:45:26', 'Hadir', NULL, NULL),
(142, 36, '2025-11-12 08:57:19', 'Hadir', NULL, NULL),
(143, 36, '2025-11-12 09:09:38', 'Hadir', NULL, NULL),
(144, 36, '2025-11-12 09:21:07', 'Hadir', NULL, NULL),
(145, 36, '2025-11-12 09:39:28', 'Hadir', NULL, NULL),
(146, 36, '2025-11-12 09:44:32', 'Hadir', NULL, NULL),
(147, 36, '2025-11-12 09:44:33', 'Hadir', NULL, NULL),
(148, 36, '2025-11-12 10:05:14', 'Hadir', NULL, NULL),
(149, 55, '2025-11-12 10:07:48', 'Hadir', NULL, NULL),
(150, 55, '2025-11-12 10:11:06', 'Hadir', NULL, NULL),
(151, 55, '2025-11-12 10:12:56', 'Hadir', NULL, NULL),
(152, 36, '2025-11-12 13:47:32', 'Hadir', NULL, NULL),
(153, 57, '2025-11-13 02:16:30', 'Hadir', NULL, NULL),
(154, 36, '2025-11-13 02:17:50', 'Hadir', NULL, NULL),
(155, 36, '2025-11-13 02:17:51', 'Hadir', NULL, NULL),
(156, 39, '2025-11-13 02:18:04', 'Hadir', NULL, NULL),
(157, 39, '2025-11-13 02:19:01', 'Hadir', NULL, NULL),
(158, 49, '2025-11-13 02:19:27', 'Hadir', NULL, NULL),
(159, 56, '2025-11-13 02:20:09', 'Hadir', NULL, NULL),
(160, 40, '2025-11-13 02:22:30', 'Hadir', NULL, NULL),
(161, 57, '2025-11-13 02:22:52', 'Hadir', NULL, NULL),
(162, 58, '2025-11-13 02:23:56', 'Hadir', NULL, NULL),
(163, 59, '2025-11-13 02:25:01', 'Hadir', NULL, NULL),
(164, 59, '2025-11-13 02:58:05', 'Hadir', NULL, NULL),
(165, 59, '2025-11-13 03:13:27', 'Hadir', NULL, NULL),
(166, 59, '2025-11-13 03:15:04', 'Hadir', NULL, NULL),
(167, 0, '2025-11-13 03:58:29', 'Hadir', NULL, NULL),
(168, 36, '2025-11-13 04:34:51', 'Hadir', NULL, NULL),
(169, 60, '2025-11-13 04:41:14', 'Hadir', NULL, NULL),
(170, 60, '2025-11-13 04:41:14', 'Hadir', NULL, NULL),
(171, 60, '2025-11-13 04:42:19', 'Hadir', NULL, NULL),
(172, 62, '2025-11-13 04:42:27', 'Hadir', NULL, NULL),
(173, 61, '2025-11-13 04:44:41', 'Hadir', NULL, NULL),
(174, 13, '2025-11-13 06:40:14', 'Hadir', NULL, NULL),
(175, 6, '2025-11-13 06:40:28', 'Hadir', NULL, NULL),
(176, 6, '2025-11-13 06:41:44', 'Hadir', NULL, NULL),
(177, 6, '2025-11-13 06:48:59', 'Hadir', NULL, NULL),
(178, 6, '2025-11-13 06:50:40', 'Hadir', NULL, NULL),
(179, 63, '2025-11-13 06:55:16', 'Hadir', NULL, NULL),
(180, 6, '2025-11-13 07:02:30', 'Hadir', NULL, NULL),
(181, 13, '2025-11-13 07:07:16', 'Hadir', NULL, NULL),
(182, 64, '2025-11-13 08:09:43', 'Hadir', NULL, NULL),
(183, 13, '2025-11-14 02:55:40', 'Hadir', NULL, NULL),
(184, 65, '2025-11-14 03:03:18', 'Hadir', NULL, NULL),
(185, 13, '2025-11-14 06:34:49', 'Hadir', NULL, NULL),
(186, 13, '2025-11-14 06:41:13', 'Hadir', NULL, NULL),
(187, 13, '2025-11-14 07:23:55', 'Hadir', NULL, NULL),
(188, 13, '2025-11-14 08:38:53', 'Hadir', NULL, NULL),
(189, 13, '2025-11-14 08:48:22', 'Hadir', NULL, NULL),
(190, 65, '2025-11-14 08:54:33', 'Hadir', NULL, NULL),
(191, 65, '2025-11-14 09:01:22', 'Hadir', NULL, NULL),
(192, 65, '2025-11-14 09:02:10', 'Hadir', NULL, NULL),
(193, 13, '2025-11-15 03:11:40', 'Hadir', NULL, NULL),
(194, 13, '2025-11-15 03:12:50', 'Hadir', NULL, NULL),
(195, 65, '2025-11-15 16:00:32', 'Hadir', NULL, NULL),
(196, 65, '2025-11-16 13:04:43', 'Hadir', NULL, NULL),
(197, 13, '2025-11-17 04:07:05', 'Hadir', NULL, NULL),
(198, 13, '2025-11-17 09:22:34', 'Hadir', NULL, NULL),
(199, 65, '2025-11-18 02:41:53', 'Hadir', NULL, NULL),
(200, 65, '2025-11-18 02:42:20', 'Hadir', NULL, NULL),
(201, 65, '2025-11-18 02:42:43', 'Hadir', NULL, NULL),
(202, 65, '2025-11-18 02:43:12', 'Hadir', NULL, NULL),
(203, 65, '2025-11-18 03:04:51', 'Hadir', NULL, NULL),
(204, 69, '2025-11-18 03:32:52', 'Hadir', NULL, NULL),
(205, 70, '2025-11-18 03:36:54', 'Hadir', NULL, NULL),
(206, 65, '2025-11-18 03:54:08', 'Hadir', NULL, NULL),
(207, 13, '2025-11-18 06:16:16', 'Hadir', NULL, NULL),
(208, 65, '2025-11-18 06:33:41', 'Hadir', NULL, NULL),
(209, 65, '2025-11-18 06:46:07', 'Hadir', NULL, NULL),
(210, 65, '2025-11-18 07:37:13', 'Hadir', NULL, NULL),
(211, 65, '2025-11-18 09:16:46', 'Hadir', NULL, NULL),
(212, 65, '2025-11-19 05:11:18', 'Hadir', NULL, NULL),
(213, 65, '2025-11-19 08:35:11', 'Hadir', NULL, NULL),
(214, 65, '2025-11-19 16:41:33', 'Hadir', NULL, NULL),
(215, 65, '2025-11-20 03:44:58', 'Hadir', NULL, NULL),
(216, 65, '2025-11-20 03:54:46', 'Hadir', NULL, NULL),
(217, 65, '2025-11-20 03:57:09', 'Hadir', NULL, NULL),
(218, 65, '2025-11-20 04:52:11', 'Hadir', NULL, NULL),
(219, 65, '2025-11-20 04:52:15', 'Hadir', NULL, NULL),
(220, 65, '2025-11-20 05:06:04', 'Hadir', NULL, NULL),
(221, 73, '2025-11-22 03:45:55', 'Hadir', NULL, NULL),
(222, 2, '2025-11-24 02:05:39', 'Hadir', NULL, NULL),
(223, 2, '2025-11-24 02:06:50', 'Hadir', NULL, NULL),
(224, 2, '2025-11-24 02:07:19', 'Hadir', NULL, NULL),
(225, 13, '2025-11-24 16:50:40', 'Hadir', NULL, NULL),
(226, 13, '2026-01-27 10:29:57', 'Hadir', NULL, NULL),
(227, 13, '2026-01-31 16:32:11', 'Hadir', NULL, NULL),
(228, 13, '2026-02-01 14:22:29', 'Hadir', NULL, NULL),
(229, 13, '2026-02-13 23:32:20', 'Hadir', NULL, NULL),
(230, 13, '2026-02-14 00:54:23', 'Hadir', NULL, NULL),
(231, 13, '2026-02-14 01:04:18', 'Hadir', NULL, NULL);

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
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mapel`
--

INSERT INTO `mapel` (`id`, `nama`) VALUES
(6, 'bahasa.indonesia'),
(9, 'Kimia'),
(11, 'Matematika lanjutan'),
(13, 'pemograman'),
(15, 'informatika');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai`
--

CREATE TABLE `nilai` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `mapel` varchar(100) NOT NULL,
  `tugas` int DEFAULT NULL,
  `uts` int DEFAULT NULL,
  `uas` int DEFAULT NULL,
  `rata_rata` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `nilai`
--

INSERT INTO `nilai` (`id`, `siswa_id`, `kelas_id`, `mapel`, `tugas`, `uts`, `uas`, `rata_rata`, `created_at`) VALUES
(1, 36, 23, 'Pemrograman Perangakat Bergerak', NULL, 100, 100, NULL, '2025-10-31 23:33:48'),
(2, 39, 23, 'Pemrograman Perangakat Bergerak', NULL, NULL, NULL, NULL, '2025-10-31 23:33:48'),
(3, 40, 23, 'Pemrograman Perangakat Bergerak', NULL, NULL, NULL, NULL, '2025-10-31 23:33:48'),
(4, 42, 23, 'Pemrograman Perangakat Bergerak', NULL, NULL, NULL, NULL, '2025-10-31 23:33:48'),
(5, 41, 24, 'Matematika', NULL, NULL, NULL, NULL, '2025-11-01 03:42:58'),
(6, 45, 24, 'Matematika', NULL, NULL, NULL, NULL, '2025-11-01 03:42:58'),
(7, 46, 24, 'Matematika', NULL, 100, 100, NULL, '2025-11-01 03:42:58'),
(8, 41, 24, 'bahasa.indonesia', NULL, 80, 90, NULL, '2025-11-01 13:17:28'),
(9, 45, 24, 'bahasa.indonesia', NULL, 90, 100, NULL, '2025-11-01 13:17:28'),
(10, 46, 24, 'bahasa.indonesia', NULL, 50, 50, NULL, '2025-11-01 13:17:28'),
(11, 46, 29, 'bahasa.indonesia', NULL, 90, 80, NULL, '2025-11-07 05:42:10'),
(12, 47, 24, 'bahasa.indonesia', NULL, 100, 80, NULL, '2025-11-07 06:03:44'),
(13, 51, 29, 'bahasa.indonesia', NULL, 1, 1, NULL, '2025-11-11 07:58:03'),
(14, 53, 31, 'bahasa.indonesia', NULL, 100, 90, NULL, '2025-11-11 07:59:50'),
(15, 65, 24, 'bahasa.indonesia', NULL, 90, 100, NULL, '2025-11-18 07:36:49'),
(16, 2, 29, 'bahasa.indonesia', NULL, 90, 90, NULL, '2025-11-20 03:38:18'),
(17, 71, 29, 'bahasa.indonesia', NULL, NULL, NULL, NULL, '2025-11-20 03:38:18'),
(18, 6, 28, 'bahasa.indonesia', NULL, NULL, NULL, NULL, '2026-02-13 18:02:02'),
(19, 64, 28, 'bahasa.indonesia', NULL, NULL, NULL, NULL, '2026-02-13 18:02:02'),
(20, 69, 28, 'bahasa.indonesia', NULL, NULL, NULL, NULL, '2026-02-13 18:02:02'),
(21, 70, 28, 'bahasa.indonesia', NULL, NULL, NULL, NULL, '2026-02-13 18:02:02'),
(22, 76, 28, 'bahasa.indonesia', NULL, NULL, NULL, NULL, '2026-02-13 18:02:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_tugas`
--

CREATE TABLE `nilai_tugas` (
  `id` int NOT NULL,
  `siswa_id` int NOT NULL,
  `kelas_id` int NOT NULL,
  `mapel_id` int NOT NULL,
  `tugas_ke` int NOT NULL,
  `nilai` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `nilai_tugas`
--

INSERT INTO `nilai_tugas` (`id`, `siswa_id`, `kelas_id`, `mapel_id`, `tugas_ke`, `nilai`) VALUES
(1, 36, 23, 3, 1, 100),
(2, 39, 23, 3, 1, NULL),
(3, 40, 23, 3, 1, NULL),
(4, 42, 23, 3, 1, NULL),
(5, 36, 23, 3, 2, 100),
(6, 39, 23, 3, 2, NULL),
(7, 40, 23, 3, 2, NULL),
(8, 42, 23, 3, 2, NULL),
(9, 36, 23, 3, 3, 100),
(10, 39, 23, 3, 3, NULL),
(11, 40, 23, 3, 3, NULL),
(12, 42, 23, 3, 3, NULL),
(13, 41, 24, 1, 1, NULL),
(14, 45, 24, 1, 1, NULL),
(15, 46, 24, 1, 1, 100),
(19, 41, 24, 1, 2, NULL),
(20, 45, 24, 1, 2, NULL),
(21, 46, 24, 1, 2, 100),
(22, 41, 24, 1, 3, NULL),
(23, 45, 24, 1, 3, NULL),
(24, 46, 24, 1, 3, 90),
(25, 41, 24, 1, 4, NULL),
(26, 45, 24, 1, 4, NULL),
(27, 46, 24, 1, 4, 82),
(31, 46, 29, 6, 1, 100),
(38, 41, 24, 6, 1, 100),
(39, 45, 24, 6, 1, 90),
(40, 47, 24, 6, 1, 80),
(41, 51, 29, 6, 1, 1),
(42, 53, 31, 6, 1, 100),
(43, 65, 24, 6, 1, 80),
(44, 2, 29, 6, 1, 90),
(45, 71, 29, 6, 1, NULL),
(46, 6, 28, 6, 1, NULL),
(48, 64, 28, 6, 1, NULL),
(50, 69, 28, 6, 1, NULL),
(52, 70, 28, 6, 1, NULL),
(54, 76, 28, 6, 1, NULL),
(56, 6, 28, 6, 2, NULL),
(58, 64, 28, 6, 2, NULL),
(60, 69, 28, 6, 2, NULL),
(62, 70, 28, 6, 2, NULL),
(64, 76, 28, 6, 2, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ppdb_pendaftar`
--

CREATE TABLE `ppdb_pendaftar` (
  `id` int NOT NULL,
  `nisn` varchar(20) DEFAULT NULL,
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
  `status_akun` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `ppdb_pendaftar`
--

INSERT INTO `ppdb_pendaftar` (`id`, `nisn`, `nama_lengkap`, `jenis_kelamin`, `agama`, `tempat_lahir`, `tanggal_lahir`, `alamat_email`, `no_hp`, `nama_sekolah`, `jurusan`, `status`, `status_akun`, `created_at`) VALUES
(2, '12345678', 'udin', 'perempuan', 'islam', 'Jksrta', '2016-05-05', 'hasan.farisi100@gmail.com', '082127662477', 'Smp bandung', 'rpl', 'diterima', 0, '2025-10-31 03:36:29'),
(3, '123456789', 'haikal', 'laki-laki', 'islam', 'jakarta', '2009-06-02', 'contact@gmail.com', '082115900646', 'smp 2 katapang', 'animasi', 'proses', 0, '2025-10-31 08:17:53'),
(4, '0', 'jaka', 'laki-laki', 'islam', 'banudng', '2019-03-04', 'contoh@gmail.com', '082115900646', 'smp 1 yadika', 'rpl', 'proses', 0, '2025-10-31 08:39:49'),
(5, '1234567890', 'jaka', 'laki-laki', 'islam', 'banudng', '2013-07-12', 'contoh@gmail.com', '085961403956', 'smp 1 yadika', 'tkj', 'proses', 0, '2025-10-31 08:42:29'),
(6, '0', 'jaka', 'laki-laki', 'buddha', 'banudng', '2010-07-15', 'contoh@gmail.com', '085961403956', 'smp 1 yadika', 'dkv', 'proses', 0, '2025-10-31 08:43:14'),
(7, '87654321', 'haokal', 'laki-laki', 'islam', 'bandung', '2012-08-08', 'contact@gmail.com', '083114347263', 'smp 2 katapang', 'dkv', 'proses', 0, '2025-10-31 09:02:31'),
(11, '876543211', 'haikal', 'laki-laki', 'islam', 'banudng', '2008-09-17', 'contoh@gmail.com', '082115900646', 'smp 1 yadika', 'rpl', 'proses', 0, '2025-11-01 06:37:31'),
(14, '1232123456', 'haikal', 'laki-laki', 'kristen', 'banudng', '2011-08-15', 'contoh@gmail.com', '082115900646', 'smp 1 yadika', 'rpl', 'proses', 0, '2025-11-01 07:15:07'),
(17, '1112', 'haye', 'laki-laki', 'hindu', 'jakarta', '2013-07-08', 'contact@gmail.com', '083114347263', 'smp 2 katapang', 'tjat', 'diterima', 0, '2025-11-01 13:14:39'),
(18, '97865463', 'Azhar Kautsar', 'laki-laki', 'buddha', 'Padalarang', '2011-09-13', 'kautsar@gmail.com', '085624201949', 'SMPN Hellprint 1', 'animasi', 'diterima', 0, '2025-11-03 06:34:24'),
(20, '7890', 'haikal darunnajmi', 'laki-laki', 'islam', 'jakarta', '2012-06-02', 'haikal@gmail.com', '085961403956', 'smp 2 katapang', 'rpl', 'diterima', 0, '2025-11-07 06:24:09'),
(23, '9877', 'wlee', 'laki-laki', 'katolik', 'Bandung', '2007-11-12', 'HUJAN@gmail.com', '098231287', 'smp 47', 'tkj', 'diterima', 0, '2025-11-11 06:39:35'),
(25, '82112', 'Zildan', 'laki-laki', 'islam', 'Bandung', '2008-03-10', 'yuyu@gmail.com', '081312663411', 'SMK TI ganuci', 'rpl', 'diterima', 1, '2025-11-13 01:52:31'),
(28, '273529372', 'Aiddil', 'laki-laki', 'islam', 'Cibandung', '2012-10-03', 'contoh@gmail.com', '08880451315', 'SMP malaysia', 'dkv', 'proses', 0, '2025-11-13 02:10:48'),
(31, '62828371', 'Rouf Kholik Wanda', 'laki-laki', 'islam', 'Cimahi', '2008-06-13', 'roufwanda@gmail.com', '089667853243', 'SMPN 16 CIMAHI', 'rpl', 'diterima', 1, '2025-11-13 02:16:57'),
(35, '91294321', 'Sawdsaw', 'laki-laki', 'islam', 'Cimahi', '2008-10-18', 'kiandra@gmail.com', '089667853243', 'SMPN 16 CIMAHI', 'rpl', 'proses', 0, '2025-11-24 09:17:23'),
(36, '23312334', 'kholik', 'laki-laki', 'islam', 'Cimahi', '2007-12-18', 'rouf@gmail.com', '089667853243', 'SMPN 16', 'tkj', 'proses', 0, '2026-02-01 07:07:25'),
(37, '2313123123', 'kholik', 'perempuan', 'islam', 'Cimahi', '2009-10-18', 'roufwanda@gmail.com', '089667853243', 'SMPN 16 CIMAHI', 'tkj', 'proses', 0, '2026-02-13 16:16:04'),
(38, '2121212121', 'kholik', 'laki-laki', 'islam', 'Cimahi', '2008-10-18', '', '089667853243', 'SMPN 16', 'mp', 'proses', 0, '2026-02-13 16:45:43'),
(39, '2345678912', 'kholik', 'laki-laki', 'kristen', 'Cimahi', '2009-10-15', 'roufwanda@gmail.com', '089667853243', 'SMPN 16', 'tjat', 'proses', 0, '2026-02-13 17:04:18'),
(40, '2313422131', 'kholik', 'laki-laki', 'buddha', 'Cimahi', '2008-11-18', 'roufwanda@gmail.com', '089667853243', 'SMPN 16', 'tjat', 'proses', 0, '2026-02-13 17:14:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` int NOT NULL,
  `nama` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kelas_id` int DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `login_status` enum('success','failed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_attendance` datetime DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`siswa_id`, `nama`, `username`, `password`, `kelas_id`, `last_login`, `login_time`, `login_status`, `login_photo`, `last_attendance`, `nis`, `email`, `telepon`) VALUES
(6, 'Depita', 'dep', '$2y$10$bpnMbTSaF8GPk5nPbdVLEuaUrzqHtHWGGmaPpELY3JPoVgVQF0hF2', 28, NULL, '2025-11-13 07:02:30', 'success', NULL, NULL, '008', 'lollol@haji.com', '09231'),
(13, 'Rouf', 'up', '$2y$10$WC0PZ/lkz3wLssF.c8YkWOhojJ4FLNdbTY7eRTZJ6dEVEhQfmrWeK', 23, NULL, '2026-02-14 01:04:18', 'success', NULL, NULL, '0010', 'Makasar@gmail.com', ''),
(64, 'aldi', 'aldie94', '$2y$10$2O4pTpx6b5d000tKV1Ri0.6Bcu8rObJYjpxrv5CBN2oNogO51Sy96', 28, NULL, '2025-11-13 08:09:43', 'success', NULL, NULL, '1234232', 'ahmadijaka89@gmail.com', '082115900646'),
(69, 'budi setiawan assidiq', 'budidc6', '$2y$10$OflmbXx4Buero2M6Y6.nE.ax624k5GFGcvolTJUT/88U5tZitqblW', 28, NULL, '2025-11-18 03:32:52', 'success', NULL, NULL, '2147483647', 'setiawan@gmail.com', '083122592672'),
(70, 'David Guntur', 'avida3a', '$2y$10$.W32kDdwuXHJW50wSaES1uyX49YH4RnWHoJy4ujOxa.YTStLs8Hxm', 28, NULL, '2025-11-18 03:36:54', 'success', NULL, NULL, '1000311007', 'DavidGunturuhiy@gmail.com', '085346612434'),
(73, 'Rouf Kholik', 'Ryach', '$2y$10$YamGqFuJs12H2sYvt1miFeuEenVEwunhIb22IRbEEHlLF1DOQVKbK', 23, NULL, '2025-11-22 03:45:55', 'success', NULL, NULL, '45678', 'roufwanda@gmail.com', '089667853243'),
(75, 'Rouf Kholik Wanda', 'oufb08', '$2y$10$7WPhogBCH2XyErm6NBW2BO2Bzf54w76hSqNyPvRrUQ3s8ZTpC6vXW', 23, NULL, NULL, NULL, NULL, NULL, '62828371', 'roufwanda@gmail.com', '089667853243'),
(76, 'Zildan', 'ildana58', '$2y$10$nIX3wYUbqW6y312RnIY0wueaqeqHcNBpTugPPD4CC4RaApTvaIlx2', 28, NULL, NULL, NULL, NULL, NULL, '82112', 'yuyu@gmail.com', '081312663411');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `level` enum('admin','guru') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mapel_ids` text COLLATE utf8mb4_general_ci,
  `mapel` text COLLATE utf8mb4_general_ci,
  `guru_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `created_at`, `mapel_ids`, `mapel`, `guru_id`) VALUES
(42, 'admin', '$2y$10$LkO19zzavYSQpI9xF7F/e.e/zhQthkElAIfeVn3tETcEJZtvTioSi', 'admin', '2026-02-16 05:19:14', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_absensi_summary`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_absensi_summary` (
`alpha` bigint
,`created_at` datetime
,`hadir` bigint
,`izin` bigint
,`kelas_id` int
,`sakit` bigint
,`tanggal` date
,`total_siswa` bigint
);

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`absensi_id`),
  ADD UNIQUE KEY `unique_absensi` (`tanggal`,`kelas_id`),
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
  ADD KEY `guru_id` (`guru_id`),
  ADD KEY `fk_jadwal_mapel` (`mapel_id`);

--
-- Indeks untuk tabel `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kelas_id`),
  ADD UNIQUE KEY `nama` (`nama`),
  ADD KEY `idx_kelas_nama` (`nama`);

--
-- Indeks untuk tabel `kontak_pesan`
--
ALTER TABLE `kontak_pesan`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `nilai_tugas`
--
ALTER TABLE `nilai_tugas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_id` (`siswa_id`,`kelas_id`,`mapel_id`,`tugas_ke`);

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
  MODIFY `absensi_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT untuk tabel `absensi_alasan`
--
ALTER TABLE `absensi_alasan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `absensi_detail`
--
ALTER TABLE `absensi_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT untuk tabel `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `guru`
--
ALTER TABLE `guru`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `kelas`
--
ALTER TABLE `kelas`
  MODIFY `kelas_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `kontak_pesan`
--
ALTER TABLE `kontak_pesan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT untuk tabel `mapel`
--
ALTER TABLE `mapel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `nilai_tugas`
--
ALTER TABLE `nilai_tugas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `ppdb_pendaftar`
--
ALTER TABLE `ppdb_pendaftar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswa_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

-- --------------------------------------------------------

--
-- Struktur untuk view `v_absensi_summary`
--
DROP TABLE IF EXISTS `v_absensi_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u745165126_SMKTI`@`127.0.0.1` SQL SECURITY DEFINER VIEW `v_absensi_summary`  AS SELECT `absensi_detail`.`tanggal` AS `tanggal`, `absensi_detail`.`kelas_id` AS `kelas_id`, count(0) AS `total_siswa`, count((case when (`absensi_detail`.`status` = 'Hadir') then 1 end)) AS `hadir`, count((case when (`absensi_detail`.`status` = 'Sakit') then 1 end)) AS `sakit`, count((case when (`absensi_detail`.`status` = 'Izin') then 1 end)) AS `izin`, count((case when (`absensi_detail`.`status` = 'Alpha') then 1 end)) AS `alpha`, now() AS `created_at` FROM `absensi_detail` GROUP BY `absensi_detail`.`tanggal`, `absensi_detail`.`kelas_id` ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
