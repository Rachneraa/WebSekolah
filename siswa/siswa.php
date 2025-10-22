<?php
session_start();
require_once "../config/koneksi.php";

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['level'] !== 'siswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil data siswa dari session
$user_id = $_SESSION['user_id'];
$query = "SELECT s.id as siswa_id, s.nis, s.nama, s.nama_kelas, s.kelas_id, k.nama as kelas_nama
          FROM users u
          INNER JOIN siswa s ON u.siswa_id = s.id
          LEFT JOIN kelas k ON s.kelas_id = k.id
          WHERE u.id = ? AND u.level = 'siswa'";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$siswa_data = mysqli_fetch_assoc($result);

if (!$siswa_data) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Siswa - SMK TI Garuda Nusantara</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f8ff;
            color: #333;
        }

        header {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        header h1 {
            margin: 0;
            font-size: 22px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info span {
            font-size: 14px;
            opacity: 0.95;
        }

        .container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        nav {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }

        nav h2 {
            font-size: 16px;
            margin-top: 0;
            color: #1565c0;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        nav li {
            margin: 8px 0;
        }

        nav button {
            width: 100%;
            padding: 12px 15px;
            border: none;
            background: none;
            text-align: left;
            font-size: 15px;
            cursor: pointer;
            color: #555;
            border-radius: 8px;
            transition: all 0.2s;
        }

        nav button:hover {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        nav button.active {
            background-color: #1e88e5;
            color: white;
            font-weight: 600;
        }

        main {
            flex: 1;
            padding: 25px;
            overflow-x: auto;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(30, 136, 229, 0.3);
        }

        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
        }

        .stat-card.hadir {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
        }

        .stat-card.sakit {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        }

        .stat-card.izin {
            background: linear-gradient(135deg, #03a9f4 0%, #0288d1 100%);
        }

        .stat-card.alpha {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #e3f2fd;
            color: #1565c0;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f5f8ff;
        }

        .profile-card {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 15px;
            align-items: start;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            font-weight: bold;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-info-item {
            display: flex;
            gap: 10px;
        }

        .profile-info-item strong {
            min-width: 100px;
            color: #1565c0;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid white;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: white;
            color: #1565c0;
        }

        .filter-box {
            background: #f5f8ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-box input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge.hadir {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .badge.sakit {
            background-color: #fff9c4;
            color: #f57f17;
        }

        .badge.izin {
            background-color: #b3e5fc;
            color: #01579b;
        }

        .badge.alpha {
            background-color: #ffcdd2;
            color: #c62828;
        }

        .jadwal-grid {
            display: grid;
            gap: 10px;
        }

        .jadwal-item {
            background: #f5f8ff;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #1e88e5;
        }

        .jadwal-item h4 {
            margin: 0 0 8px 0;
            color: #1565c0;
        }

        .jadwal-item p {
            margin: 4px 0;
            font-size: 14px;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            nav {
                width: 100%;
                padding: 10px;
            }

            nav h2 {
                display: none;
            }

            nav ul {
                display: flex;
                justify-content: space-around;
                margin: 0;
            }

            nav li {
                margin: 0;
            }

            nav button {
                padding: 10px;
                font-size: 12px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .profile-card {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .profile-avatar {
                margin: 0 auto;
            }

            header h1 {
                font-size: 16px;
            }

            .user-info {
                display: none;
            }
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .empty-state img {
            width: 150px;
            opacity: 0.5;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header>
        <h1>🎓 SMK TI Garuda Nusantara</h1>
        <div class="user-info">
            <span><?php echo htmlspecialchars($siswa_data['nama']); ?> (<?php echo htmlspecialchars($siswa_data['nis']); ?>)</span>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </header>

    <div class="container">
        <nav>
            <h2>Panel Siswa</h2>
            <ul>
                <li><button onclick="showSection('dashboard', this)" class="active">Dashboard</button></li>
                <li><button onclick="showSection('absensi', this)">Absensi</button></li>
                <li><button onclick="showSection('jadwal', this)">Jadwal</button></li>
                <li><button onclick="showSection('profile', this)">Profile</button></li>
            </ul>
        </nav>
        <main id="content">
            <div class="loading">Memuat data...</div>
        </main>
    </div>

    <script>
        // Data siswa dari PHP
        const siswaData = <?php echo json_encode($siswa_data); ?>;
        
        // Storage untuk data yang diambil dari API
        const data = {
            absensi: [],
            statistik: null,
            jadwal: [],
            nilai: []
        };

        // Helper untuk escape HTML
        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe).replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[m];
            });
        }

        // Fetch data dari API
        async function fetchData(endpoint) {
            try {
                const response = await fetch(`../config/api.php?action=${endpoint}`, {
                    credentials: 'same-origin'
                });
                const text = await response.text();
                try {
                    const result = JSON.parse(text);
                    if (result.status === 'success') {
                        return result.data || [];
                    }
                    if (result.message === 'Unauthorized') {
                        alert('Sesi berakhir – silakan login ulang.');
                        window.location.href = '../login.php';
                        return null;
                    }
                    throw new Error(result.message || 'Fetch failed');
                } catch (e) {
                    console.error('Server response not JSON:', text);
                    throw e;
                }
            } catch (err) {
                console.error(`Error fetching ${endpoint}:`, err);
                return null;
            }
        }

        // Show section
        async function showSection(section, btn) {
            // Update active button
            document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const main = document.getElementById('content');
            main.innerHTML = '<div class="loading">Memuat data...</div>';

            try {
                if (section === 'dashboard') {
                    // Fetch statistik absensi
                    data.statistik = await fetchData('get_statistik_absensi');
                    
                    if (!data.statistik) {
                        main.innerHTML = '<div class="card"><p>Gagal memuat data</p></div>';
                        return;
                    }

                    main.innerHTML = `
                        <div class="card">
                            <h2>Dashboard</h2>
                            <p>Selamat datang, <strong>${escapeHtml(siswaData.nama)}</strong>!</p>
                            <p>Kelas: <strong>${escapeHtml(siswaData.nama_kelas)}</strong></p>
                        </div>
                        
                        <div class="card">
                            <h3>Statistik Kehadiran</h3>
                            <div class="stats-grid">
                                <div class="stat-card hadir">
                                    <h3>Hadir</h3>
                                    <div class="number">${data.statistik.hadir}</div>
                                </div>
                                <div class="stat-card sakit">
                                    <h3>Sakit</h3>
                                    <div class="number">${data.statistik.sakit}</div>
                                </div>
                                <div class="stat-card izin">
                                    <h3>Izin</h3>
                                    <div class="number">${data.statistik.izin}</div>
                                </div>
                                <div class="stat-card alpha">
                                    <h3>Alpha</h3>
                                    <div class="number">${data.statistik.alpha}</div>
                                </div>
                            </div>
                            <div style="margin-top: 20px; text-align: center;">
                                <h2 style="color: #1565c0;">Persentase Kehadiran: ${data.statistik.persentase_kehadiran}%</h2>
                            </div>
                        </div>
                    `;
                    return;
                }

                if (section === 'absensi') {
                    data.absensi = await fetchData('get_absensi_siswa');
                    
                    if (!data.absensi) {
                        main.innerHTML = '<div class="card"><p>Gagal memuat data absensi</p></div>';
                        return;
                    }

                    main.innerHTML = `
                        <div class="card">
                            <h2>Riwayat Absensi</h2>
                            <div class="filter-box">
                                <input type="month" id="filterBulan" onchange="filterAbsensi()" 
                                       value="${new Date().toISOString().slice(0, 7)}">
                            </div>
                            ${renderAbsensiTable()}
                        </div>
                    `;
                    return;
                }

                if (section === 'jadwal') {
                    data.jadwal = await fetchData('get_jadwal_siswa');
                    
                    if (!data.jadwal) {
                        main.innerHTML = '<div class="card"><p>Gagal memuat data jadwal</p></div>';
                        return;
                    }

                    main.innerHTML = `
                        <div class="card">
                            <h2>Jadwal Pelajaran</h2>
                            <p>Kelas: <strong>${escapeHtml(siswaData.nama_kelas)}</strong></p>
                            ${renderJadwal()}
                        </div>
                    `;
                    return;
                }

                if (section === 'profile') {
                    main.innerHTML = `
                        <div class="card">
                            <h2>Profile Siswa</h2>
                            <div class="profile-card">
                                <div class="profile-avatar">
                                    ${escapeHtml(siswaData.nama.charAt(0).toUpperCase())}
                                </div>
                                <div class="profile-info">
                                    <div class="profile-info-item">
                                        <strong>Nama:</strong>
                                        <span>${escapeHtml(siswaData.nama)}</span>
                                    </div>
                                    <div class="profile-info-item">
                                        <strong>NIS:</strong>
                                        <span>${escapeHtml(siswaData.nis)}</span>
                                    </div>
                                    <div class="profile-info-item">
                                        <strong>Kelas:</strong>
                                        <span>${escapeHtml(siswaData.nama_kelas)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    return;
                }

            } catch (error) {
                console.error('Error in showSection:', error);
                main.innerHTML = `<div class="card"><p>Error: ${error.message}</p></div>`;
            }
        }

        // Render tabel absensi
        function renderAbsensiTable() {
            if (!data.absensi || data.absensi.length === 0) {
                return '<div class="empty-state"><p>Belum ada data absensi</p></div>';
            }

            let html = '<table><thead><tr><th>Tanggal</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>';
            
            data.absensi.forEach(a => {
                const statusClass = a.status.toLowerCase();
                html += `
                    <tr>
                        <td>${a.tanggal}</td>
                        <td><span class="badge ${statusClass}">${escapeHtml(a.status)}</span></td>
                        <td>${escapeHtml(a.keterangan || '-')}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            return html;
        }

        // Filter absensi berdasarkan bulan
        function filterAbsensi() {
            const bulan = document.getElementById('filterBulan').value;
            let filtered = data.absensi;

            if (bulan) {
                filtered = filtered.filter(a => a.tanggal.startsWith(bulan));
            }

            const tbody = document.querySelector('#content table tbody');
            if (tbody) {
                if (filtered.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;">Tidak ada data</td></tr>';
                } else {
                    tbody.innerHTML = filtered.map(a => {
                        const statusClass = a.status.toLowerCase();
                        return `
                            <tr>
                                <td>${a.tanggal}</td>
                                <td><span class="badge ${statusClass}">${escapeHtml(a.status)}</span></td>
                                <td>${escapeHtml(a.keterangan || '-')}</td>
                            </tr>
                        `;
                    }).join('');
                }
            }
        }

        // Render jadwal
        function renderJadwal() {
            if (!data.jadwal || data.jadwal.length === 0) {
                return '<div class="empty-state"><p>Belum ada jadwal pelajaran</p></div>';
            }

            // Group by hari
            const jadwalByHari = {};
            data.jadwal.forEach(j => {
                if (!jadwalByHari[j.hari]) {
                    jadwalByHari[j.hari] = [];
                }
                jadwalByHari[j.hari].push(j);
            });

            const hariUrutan = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            let html = '<div class="jadwal-grid">';

            hariUrutan.forEach(hari => {
                if (jadwalByHari[hari]) {
                    html += `<div class="jadwal-item">
                        <h4>${hari}</h4>`;
                    
                    jadwalByHari[hari].forEach(j => {
                        html += `
                            <p><strong>${escapeHtml(j.mapel)}</strong></p>
                            <p>⏰ ${escapeHtml(j.jam)}</p>
                        `;
                    });
                    
                    html += '</div>';
                }
            });

            html += '</div>';
            return html;
        }

        // Logout
        function logout() {
            if (confirm('Yakin ingin logout?')) {
                window.location.href = '../config/logout.php';
            }
        }

        // Initialize dashboard saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            showSection('dashboard', document.querySelector('nav button'));
        });
    </script>
</body>

</html>