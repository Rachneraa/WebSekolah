<?php
session_start();
include 'config/koneksi.php';

// 1. Ambil data pendaftar (yang diterima/proses) untuk tabel
$query_tabel = "SELECT id, nama_lengkap, jenis_kelamin, agama, jurusan, status, nisn FROM pendaftaran WHERE status IN ('diterima', 'proses') ORDER BY id DESC";
$result_tabel = mysqli_query($db, $query_tabel);
$rows = [];
if ($result_tabel) {
    while ($row = mysqli_fetch_assoc($result_tabel)) {
        $rows[] = $row;
    }
}

// 2. Hitung Statistik Total
$query_total = "SELECT COUNT(*) AS total FROM pendaftaran";
$result_total = mysqli_query($db, $query_total);
$total_pendaftar = ($result_total) ? mysqli_fetch_assoc($result_total)['total'] : 0;

// 3. Hitung Statistik Diterima
$query_diterima = "SELECT COUNT(*) AS total FROM pendaftaran WHERE status = 'diterima'";
$result_diterima = mysqli_query($db, $query_diterima);
$total_diterima = ($result_diterima) ? mysqli_fetch_assoc($result_diterima)['total'] : 0;

// 4. Hitung Statistik Proses
$query_proses = "SELECT COUNT(*) AS total FROM pendaftaran WHERE status = 'proses'";
$result_proses = mysqli_query($db, $query_proses);
$total_proses = ($result_proses) ? mysqli_fetch_assoc($result_proses)['total'] : 0;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPDB 2025 - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="//manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="//icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="//icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="//icons/apple-touch-icon.png">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variabel CSS, Reset, Base Styles */
        :root {
            --primary-orange: #ff8303;
            --primary-blue: #00499d;
            --dark-blue: #003366;
            --text-dark: #0f1724;
            --text-gray: #6b7280;
            --border-gray: #e5e7eb;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --light-blue: #e3f2fd; /* Warna biru muda untuk hover tabel */
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; line-height: 1.6; overflow-x: hidden; background: #f8f9fa; }
        button { cursor: pointer; border: none; background: none; font-family: inherit; }

        /* Hero Section */
        .hero-ppdb {
            background: linear-gradient(135deg, rgba(0, 73, 157, 0.95), rgba(0, 51, 102, 0.95)),
                url('assets/c.jpeg') center/cover no-repeat; /* Pastikan path gambar benar */
            padding: 80px 20px 60px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .hero-ppdb h1 { font-size: 48px; font-weight: 800; margin-bottom: 15px; text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3); position: relative; z-index: 1; }
        .hero-ppdb p { font-size: 20px; max-width: 700px; margin: 0 auto 30px; position: relative; z-index: 1; }

        /* === Wadah Baru untuk Tombol Hero === */
        .hero-buttons {
            margin-top: 30px; /* Jarak dari teks di atas */
            display: flex; /* Sejajarkan di desktop */
            justify-content: center; /* Tengahkan di desktop */
            align-items: center; /* Vertikal center di desktop */
            gap: 20px; /* Jarak antar tombol di desktop */
            flex-wrap: wrap; /* Izinkan wrap jika layar sempit */
        }
        /* === Akhir CSS Wadah === */

        .hero-ppdb-badge { display: inline-block; background: var(--primary-orange); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 700; font-size: 16px; box-shadow: 0 5px 20px rgba(255, 131, 3, 0.4); /* margin-top: 10px; Dihapus */ position: relative; z-index: 1; animation: pulseBadge 2s ease-in-out infinite; }
        .btn-cek-status {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-blue);
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 25px;
            margin-bottom: 25px;
            /* margin-left: 20px; Dihapus */
            border: 2px solid white;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
            cursor: pointer;
        }
        .btn-cek-status:hover { background: white; color: var(--primary-orange); transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }

        @keyframes pulseBadge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }

        /* Container Utama */
        .ppdb-container { max-width: 1200px; margin: -50px auto 80px; padding: 0 20px; position: relative; z-index: 10; }

        /* Search & Stats Bar */
        .search-stats-bar { background: white; padding: 25px 30px; border-radius: 15px; box-shadow: var(--shadow); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; }
        .search-bar { flex: 1; display: flex; align-items: center; gap: 15px; background: #f8f9fa; padding: 12px 20px; border-radius: 50px; border: 2px solid transparent; transition: all 0.3s ease; min-width: 300px; }
        .search-bar:focus-within { border-color: var(--primary-blue); }
        .search-bar i { color: #999; font-size: 18px; }
        .search-bar input { flex: 1; border: none; outline: none; background: transparent; font-size: 15px; font-family: 'Poppins', sans-serif; }
        .search-bar input::placeholder { color: #999; }

        /* Statistik Horizontal */
        .stat-tabs { display: flex; gap: 5px; background-color: #f0f3f7; padding: 5px; border-radius: 10px; flex-shrink: 0; }
        .stat-tab { padding: 10px 20px; border-radius: 8px; text-align: center; cursor: pointer; transition: background-color 0.3s ease, color 0.3s ease; display: flex; align-items: center; gap: 8px; background-color: transparent; border: 1px solid transparent; }
        .stat-tab .number { font-size: 18px; font-weight: 700; color: var(--primary-blue); background-color: white; padding: 2px 8px; border-radius: 4px; min-width: 25px; display: inline-block; line-height: 1.2; }
        .stat-tab .label { font-size: 13px; font-weight: 600; color: var(--text-gray); line-height: 1.2; }
        .stat-tab:hover { background-color: #e9ecef; }
        .stat-tab.active { background-color: white; border: 1px solid var(--border-gray); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .stat-tab.active .label { color: var(--primary-blue); }

        /* Data Table */
        .data-table-container { background: white; border-radius: 15px; box-shadow: var(--shadow); overflow: hidden; }
        .table-header { background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue)); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .table-header h3 { font-size: 20px; display: flex; align-items: center; gap: 10px; margin: 0; }
        .table-filter { display: flex; gap: 10px; }
        .filter-btn, select.filter-btn { background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 8px 18px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.3s ease; font-family: 'Poppins', sans-serif; appearance: none; -webkit-appearance: none; }
        select.filter-btn { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white' width='18px' height='18px'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 35px; }
        .filter-btn:hover { background: rgba(255, 255, 255, 0.3); }
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        thead { background: #f8f9fa; }
        thead th { padding: 18px 20px; text-align: left; font-weight: 700; color: var(--text-dark); font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e9ecef; white-space: nowrap; }
        tbody tr { border-bottom: 1px solid #e9ecef; transition: all 0.3s ease; }
        tbody tr:hover { background: var(--light-blue); }
        tbody td { padding: 18px 20px; color: var(--text-gray); font-size: 14px; vertical-align: middle; }
        tbody tr.no-data td { text-align: center; padding: 30px; color: #999; font-style: italic; }
        .student-name { display: flex; align-items: center; gap: 12px; min-width: 250px; }
        .student-avatar { width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-blue), var(--primary-orange)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; }
        .student-info .name { font-weight: 600; color: var(--text-dark); margin-bottom: 3px; }
        .student-info .nisn { font-size: 12px; color: #999; }
        .badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
        .badge-male { background: #e3f2fd; color: #1976d2; }
        .badge-female { background: #fce4ec; color: #c2185b; }
        .badge-agama { background: #f3e5f5; color: #7b1fa2; }
        .badge-jurusan { background: #e8f5e9; color: #388e3c; }
        .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; }
        .status-diterima { background: #d4edda; color: #155724; }
        .status-proses { background: #fff3cd; color: #856404; }
        /* Pagination */
        .pagination { display: flex; justify-content: center; align-items: center; gap: 10px; padding: 25px; border-top: 1px solid #e9ecef; flex-wrap: wrap; }
        .pagination button { padding: 10px 18px; border: 1px solid #e9ecef; background: white; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-family: 'Poppins', sans-serif; color: var(--text-dark); min-width: 45px; }
        .pagination button:hover:not(:disabled) { background: var(--light-blue); border-color: var(--primary-blue); color: var(--primary-blue); }
        .pagination button.active { background: var(--primary-blue); color: white; border-color: var(--primary-blue); }
        .pagination button:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-ppdb h1 { font-size: 32px; }
            .hero-ppdb p { font-size: 16px; }

            /* === Penyesuaian Tombol Hero di Mobile === */
             .hero-buttons {
                 flex-direction: column; /* Susun ke bawah */
                 align-items: center;  /* Rata tengah horizontal */
                 gap: 15px; /* Jarak antar tombol saat vertikal */
                 width: 100%; /* Ambil lebar penuh */
                 padding: 0 10px; /* Beri sedikit padding jika perlu */
                 margin-top: 20px; /* Kurangi jarak atas */
             }
             .hero-ppdb-badge,
             .btn-cek-status {
                  width: auto; /* Biarkan lebar sesuai konten */
                  max-width: 90%; /* Batasi lebar maksimum */
                  margin: 0; /* Hapus margin individual */
                  font-size: 14px;
                  padding: 10px 25px;
             }
            /* === Akhir Penyesuaian === */

            .ppdb-container { margin: -30px auto 50px; }
            .search-stats-bar { padding: 15px; gap: 15px; }
            .search-bar { min-width: 0; width: 100%; }
            .stat-tabs { width: 100%; justify-content: space-between; }
            .stat-tab { padding: 8px 10px; gap: 5px; flex-grow: 1; justify-content: center; }
            .stat-tab .number { font-size: 16px; padding: 2px 6px; min-width: 20px; }
            .stat-tab .label { font-size: 11px; }
            .table-header { flex-direction: column; align-items: flex-start; }
            thead th { font-size: 12px; padding: 12px 10px; }
            tbody td { font-size: 13px; padding: 12px 10px; }
            .student-name { min-width: 200px; gap: 10px;}
            .student-avatar { width: 35px; height: 35px; font-size: 14px; }
            .badge, .status-badge { padding: 5px 12px; font-size: 11px; }
        }
        @media (max-width: 480px) {
            .hero-ppdb h1 { font-size: 26px; }
            .stat-tab { flex-direction: column; gap: 3px; padding: 10px 5px; }
            .stat-tab .number { font-size: 18px; }
            .stat-tab .label { font-size: 10px; }
            table { min-width: 600px; }
        }

        /* === CSS MODAL === */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); display: flex; justify-content: center; align-items: center; z-index: 2000; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .login-modal { background: #fff; border-radius: 20px; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3); max-width: 420px; width: 90%; padding: 2.5rem; position: relative; transform: scale(0.9) translateY(-20px); transition: transform 0.3s ease; }
        .modal-overlay.active .login-modal { transform: scale(1) translateY(0); }
        .modal-close { position: absolute; top: 20px; right: 20px; width: 35px; height: 35px; border-radius: 50%; background: #f5f6fa; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; border: none; }
        .modal-close:hover { background: #e6e9ee; transform: rotate(90deg); }
        .modal-close i { color: var(--text-dark); font-size: 1.2rem; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-logo { width: 80px; height: 80px; margin: 0 auto 15px; display: block; }
        .login-header h2 { color: var(--primary-blue); font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .login-header p { color: var(--text-gray); font-size: 14px; line-height: 1.5; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; }
        .input-group input { width: 100%; padding: 12px 16px; border: 2px solid var(--border-gray); border-radius: 10px; font-size: 14px; transition: all 0.3s ease; font-family: 'Poppins', sans-serif; }
        .input-group input:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0, 73, 157, 0.1); }
        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--primary-blue), #003d7a); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 73, 157, 0.3); }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 73, 157, 0.4); }
        .login-error-message { padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: 500; line-height: 1.4; }
        @media (max-width: 768px) { .login-modal { padding: 2rem; } .login-header h2 { font-size: 20px; } }
        @media (max-width: 480px) { .login-modal { padding: 1.5rem; } .login-logo { width: 70px; height: 70px; } }
        /* === AKHIR CSS MODAL === */
    </style>
</head>

<body>
    <?php include 'include/nav.php'; // Pastikan nav.php sudah dibersihkan ?>

    <section class="hero-ppdb">
        <h1>Penerimaan Peserta Didik Baru</h1>
        <p>Data Pendaftar Tahun Ajaran 2025/2026</p>

        <div class="hero-buttons">
            <div class="hero-ppdb-badge">
                <i class="fas fa-users"></i> <?= $total_pendaftar ?> Pendaftar Terdaftar
            </div>
            <button class="btn-cek-status" id="cekStatusBtn">
                <i class="fas fa-search"></i> Cek Status Pendaftaran Anda
            </button>
        </div>
        </section>

    <div class="ppdb-container">

        <div class="search-stats-bar">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau NISN...">
            </div>

            <div class="stat-tabs">
                <div class="stat-tab active">
                    <span class="number"><?= $total_pendaftar ?></span>
                    <span class="label">Total Pendaftar</span>
                </div>
                <div class="stat-tab">
                    <span class="number"><?= $total_diterima ?></span>
                    <span class="label">Diterima</span>
                </div>
                <div class="stat-tab">
                    <span class="number"><?= $total_proses ?></span>
                    <span class="label">Proses</span>
                </div>
            </div>
        </div>

        <div class="data-table-container">
            <div class="table-header">
                <h3><i class="fas fa-table"></i> Data Pendaftar PPDB 2025</h3>
                <div class="table-filter">
                     <select id="jurusanSelect" class="filter-btn">
                         <option value="">Semua Jurusan</option>
                         <option value="Rekayasa Perangkat Lunak">RPL</option>
                         <option value="Teknik Komputer dan Jaringan">TKJ</option>
                         <option value="Animasi">Animasi</option>
                         <option value="Desain Komunikasi Visual">DKV</option>
                         <option value="Managemen Perkantoran">MP</option>
                         <option value="Teknik Jaringan Akses Telekomunikasi">TJAT</option>
                     </select>
                </div>
            </div>

            <div class="table-wrapper" id="tableWrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Agama</th>
                            <th>Jurusan</th>
                            <th>Status Proses</th>
                        </tr>
                    </thead>
                    <tbody id="ppdbTableBody">
                        <?php if (count($rows) === 0): ?>
                            <tr class="no-data">
                                <td colspan="6">Belum ada pendaftar yang statusnya Diterima atau Proses.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $i => $row): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <div class="student-name">
                                            <div class="student-avatar">
                                                <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) . strtoupper(substr(explode(' ', $row['nama_lengkap'])[1] ?? $row['nama_lengkap'], 0, 1)) ?>
                                            </div>
                                            <div class="student-info">
                                                <div class="name"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                                <div class="nisn">NISN: <?= htmlspecialchars($row['nisn']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $row['jenis_kelamin'] == 'laki-laki' ? 'male' : 'female' ?>">
                                            <i class="fas fa-<?= $row['jenis_kelamin'] == 'laki-laki' ? 'mars' : 'venus' ?>"></i>
                                            <?= ucwords($row['jenis_kelamin']) ?>
                                        </span>
                                    </td>
                                    <td>
                                         <span class="badge badge-agama">
                                            <i class="fas fa-<?= $row['agama'] == 'islam' ? 'mosque' : ($row['agama'] == 'kristen' || $row['agama'] == 'katolik' ? 'cross' : ($row['agama'] == 'hindu' ? 'om' : ($row['agama'] == 'buddha' ? 'dharmachakra' : 'yin-yang'))) ?>"></i>
                                            <?= ucwords($row['agama']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-jurusan"><?= ucwords($row['jurusan']) ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($row['status']) ?>">
                                            <i class="fas fa-<?= $row['status'] == 'diterima' ? 'check-circle' : 'clock' ?>"></i>
                                            <?= $row['status'] == 'diterima' ? 'Diterima' : 'Proses Seleksi' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                </div>
        </div>

    </div>

    <?php include 'include/footer.php'; ?>

    <div class="modal-overlay" id="statusModal">
        <div class="login-modal">
            <button class="modal-close" id="closeStatusModal">
                <i class="fas fa-times"></i>
            </button>
            <div class="login-header">
                <img src="assets/smk.png" alt="Logo Sekolah" class="login-logo">
                <h2>Cek Status Pendaftaran</h2>
                <p>Masukkan Nama Lengkap dan NISN Anda</p>
            </div>
            <?php if (isset($_SESSION['status_error'])): ?>
                <div class="login-error-message"><?= htmlspecialchars($_SESSION['status_error']) ?></div>
            <?php endif; ?>
            <form id="statusForm" action="config/process_cek_status.php" method="POST">
                <div class="input-group">
                    <label for="modal_nama_lengkap">Nama Lengkap</label> <input type="text" id="modal_nama_lengkap" name="nama_lengkap" placeholder="Masukkan Nama Lengkap Anda" required>
                </div>
                <div class="input-group">
                    <label for="modal_nisn">NISN</label> <input type="text" id="modal_nisn" name="nisn" placeholder="Masukkan NISN Anda" required>
                </div>
                <button type="submit" class="btn-login">Cek Status</button>
            </form>
        </div>
    </div>
    <style>
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); display: flex; justify-content: center; align-items: center; z-index: 2000; opacity: 0; visibility: hidden; transition: all 0.3s ease; }
        .modal-overlay.active { opacity: 1; visibility: visible; }
        .login-modal { background: #fff; border-radius: 20px; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3); max-width: 420px; width: 90%; padding: 2.5rem; position: relative; transform: scale(0.9) translateY(-20px); transition: transform 0.3s ease; }
        .modal-overlay.active .login-modal { transform: scale(1) translateY(0); }
        .modal-close { position: absolute; top: 20px; right: 20px; width: 35px; height: 35px; border-radius: 50%; background: #f5f6fa; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; border: none; }
        .modal-close:hover { background: #e6e9ee; transform: rotate(90deg); }
        .modal-close i { color: var(--text-dark); font-size: 1.2rem; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-logo { width: 80px; height: 80px; margin: 0 auto 15px; display: block; }
        .login-header h2 { color: var(--primary-blue); font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .login-header p { color: var(--text-gray); font-size: 14px; line-height: 1.5; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 8px; }
        .input-group input { width: 100%; padding: 12px 16px; border: 2px solid var(--border-gray); border-radius: 10px; font-size: 14px; transition: all 0.3s ease; font-family: 'Poppins', sans-serif; }
        .input-group input:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 3px rgba(0, 73, 157, 0.1); }
        .btn-login { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--primary-blue), #003d7a); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0, 73, 157, 0.3); }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 73, 157, 0.4); }
        .login-error-message { padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 10px; margin-bottom: 20px; text-align: center; font-size: 14px; font-weight: 500; line-height: 1.4; }
        @media (max-width: 768px) { .login-modal { padding: 2rem; } .login-header h2 { font-size: 20px; } }
        @media (max-width: 480px) { .login-modal { padding: 1.5rem; } .login-logo { width: 70px; height: 70px; } }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data table interaction
            let originalRows = Array.from(document.querySelectorAll('#ppdbTableBody tr:not(.no-data)'));
            const tableBody = document.getElementById('ppdbTableBody');
            const searchInput = document.getElementById('searchInput');
            const jurusanSelect = document.getElementById('jurusanSelect');
            const rowsPerPage = 10;
            let currentPage = 1;
            let currentFilteredRows = [...originalRows];

            function renderTable() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedJurusan = jurusanSelect.value;
                currentFilteredRows = originalRows.filter(row => {
                    const namaLengkapCell = row.cells[1];
                    const namaLengkap = namaLengkapCell ? namaLengkapCell.textContent.toLowerCase() : '';
                    const nisnElement = namaLengkapCell ? namaLengkapCell.querySelector('.nisn') : null;
                    const nisn = nisnElement ? nisnElement.textContent.toLowerCase().replace('nisn: ', '').trim() : '';
                    const jurusanCell = row.cells[4];
                    const jurusan = jurusanCell ? jurusanCell.textContent.trim() : '';
                    let show = true;
                    if (selectedJurusan && jurusan.toLowerCase() !== selectedJurusan.toLowerCase()) { show = false; }
                    if (searchTerm && !(namaLengkap.includes(searchTerm) || nisn.includes(searchTerm))) { show = false; }
                    return show;
                });
                if (currentFilteredRows.length === 0) {
                    tableBody.innerHTML = '<tr class="no-data"><td colspan="6">Data tidak ditemukan atau filter tidak cocok.</td></tr>';
                    renderPagination(0); return;
                }
                const totalPages = Math.ceil(currentFilteredRows.length / rowsPerPage);
                currentPage = Math.min(currentPage, totalPages) || 1;
                tableBody.innerHTML = '';
                const pageRows = currentFilteredRows.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage);
                pageRows.forEach((row, index) => {
                     const rowClone = row.cloneNode(true);
                     rowClone.cells[0].textContent = ((currentPage - 1) * rowsPerPage) + index + 1;
                     tableBody.appendChild(rowClone);
                 });
                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                const pag = document.querySelector('.pagination');
                if (!pag) return; pag.innerHTML = ''; if (totalPages <= 1) return;
                pag.innerHTML += `<button ${currentPage == 1 ? 'disabled' : ''} onclick="gotoPage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>`;
                let startPage = Math.max(1, currentPage - 2); let endPage = Math.min(totalPages, currentPage + 2);
                if (currentPage <= 3) { endPage = Math.min(totalPages, 5); } if (currentPage > totalPages - 2) { startPage = Math.max(1, totalPages - 4); }
                if (startPage > 1) { pag.innerHTML += `<button onclick="gotoPage(1)">1</button>`; if (startPage > 2) { pag.innerHTML += `<button disabled>...</button>`; } }
                for (let i = startPage; i <= endPage; i++) { pag.innerHTML += `<button class="${i == currentPage ? 'active' : ''}" onclick="gotoPage(${i})">${i}</button>`; }
                 if (endPage < totalPages) { if (endPage < totalPages - 1) { pag.innerHTML += `<button disabled>...</button>`; } pag.innerHTML += `<button onclick="gotoPage(${totalPages})">${totalPages}</button>`; }
                pag.innerHTML += `<button ${currentPage == totalPages ? 'disabled' : ''} onclick="gotoPage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>`;
            }

            window.gotoPage = function(page) {
                if (page < 1 || page > Math.ceil(currentFilteredRows.length / rowsPerPage)) return; currentPage = page; renderTable();
                 const tableContainer = document.querySelector('.data-table-container');
                 if (tableContainer) { tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
            }

            let searchTimeout;
            searchInput.addEventListener('input', () => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => { currentPage = 1; renderTable(); }, 300); });
            jurusanSelect.addEventListener('change', () => { currentPage = 1; renderTable(); });

            originalRows = Array.from(document.querySelectorAll('#ppdbTableBody tr:not(.no-data)'));
            currentFilteredRows = [...originalRows]; renderTable();

             // === KODE JAVASCRIPT MODAL STATUS ===
            const cekStatusBtn = document.getElementById('cekStatusBtn');
            const statusModal = document.getElementById('statusModal');
            const closeStatusModal = document.getElementById('closeStatusModal');
            if (cekStatusBtn) { cekStatusBtn.addEventListener('click', function(e) { e.preventDefault(); if (statusModal) statusModal.classList.add('active'); }); }
            if (closeStatusModal) { closeStatusModal.addEventListener('click', function() { if (statusModal) statusModal.classList.remove('active'); }); }
            if (statusModal) { statusModal.addEventListener('click', function(e) { if (e.target === statusModal) { statusModal.classList.remove('active'); } }); }
            <?php
            if (isset($_SESSION['status_error'])) {
                echo "if (statusModal) statusModal.classList.add('active');\n";
                unset($_SESSION['status_error']);
            }
            ?>
            // === AKHIR KODE JAVASCRIPT MODAL ===

        }); // Penutup DOMContentLoaded
    </script>
</body>

</html>