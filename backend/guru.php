<?php
session_start();
require_once "../config/koneksi.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// ambil kelas terurut
$kelas_query = "SELECT id, nama FROM kelas ORDER BY nama";
$kelas_result = mysqli_query($db, $kelas_query);
$kelas_data = [];
while ($row = mysqli_fetch_assoc($kelas_result)) {
  $kelas_data[] = $row;
}

// ambil siswa dengan kelas (urutan: kelas.nama, siswa.nama)
$siswa_query = "SELECT s.id, s.nama, s.kelas_id, s.nama_kelas, k.nama AS kelas_nama
               FROM siswa s
               LEFT JOIN kelas k ON s.kelas_id = k.id
               ORDER BY k.nama, s.nama";
$siswa_result = mysqli_query($db, $siswa_query);
$siswa_data = [];
while ($row = mysqli_fetch_assoc($siswa_result)) {
  $siswa_data[] = $row;
}

// ambil daftar pelajaran (mapel) dari DB jika ada, else fallback distinct dari nilai/jadwal
$mapel_data = [];
$mapel_table = '';
$checkTables = ['mapel', 'mata_pelajaran', 'pelajaran'];
foreach ($checkTables as $t) {
  $r = mysqli_query($db, "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $t) . "'");
  if ($r && mysqli_num_rows($r) > 0) {
    $mapel_table = $t;
    break;
  }
}
if ($mapel_table) {
  $cols = [];
  $colsRes = mysqli_query($db, "SHOW COLUMNS FROM `" . $mapel_table . "`");
  while ($c = mysqli_fetch_assoc($colsRes))
    $cols[] = $c['Field'];
  $id_col = null;
  $name_col = null;
  foreach ($cols as $c) {
    if (preg_match('/^id$/i', $c))
      $id_col = $c;
    if (preg_match('/(^nama$|nama_|nama_mapel|mapel|judul|title)/i', $c) && !$name_col)
      $name_col = $c;
  }
  if (!$name_col) {
    foreach ($cols as $c) {
      if ($c !== $id_col) {
        $name_col = $c;
        break;
      }
    }
  }
  if ($name_col) {
    $selectCols = ($id_col ? "`$id_col` as id, " : '') . "`$name_col` as nama";
    $resM = mysqli_query($db, "SELECT $selectCols FROM `$mapel_table` ORDER BY nama");
    while ($m = mysqli_fetch_assoc($resM))
      $mapel_data[] = $m;
  }
}
if (empty($mapel_data)) {
  // fallback -> ambil distinct nama mapel dari tabel nilai/jadwal (jika ada)
  $seen = [];
  $r1 = @mysqli_query($db, "SELECT DISTINCT mapel FROM nilai WHERE mapel IS NOT NULL AND mapel <> ''");
  if ($r1) {
    while ($rr = mysqli_fetch_assoc($r1)) {
      $n = trim($rr['mapel']);
      if ($n !== '' && !isset($seen[$n])) {
        $mapel_data[] = ['id' => null, 'nama' => $n];
        $seen[$n] = true;
      }
    }
  }
  $r2 = @mysqli_query($db, "SELECT DISTINCT mapel FROM jadwal WHERE mapel IS NOT NULL AND mapel <> ''");
  if ($r2) {
    while ($rr = mysqli_fetch_assoc($r2)) {
      $n = trim($rr['mapel']);
      if ($n !== '' && !isset($seen[$n])) {
        $mapel_data[] = ['id' => null, 'nama' => $n];
        $seen[$n] = true;
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Guru - SMK TI Garuda Nusantara</title>
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

    button.add-btn,
    button.action-btn {
      background-color: #1e88e5;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 8px;
      cursor: pointer;
      margin: 5px 5px 15px 0;
      font-size: 14px;
      transition: all 0.2s;
    }

    button.add-btn:hover,
    button.action-btn:hover {
      background-color: #1565c0;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    button.delete-btn {
      background-color: #ef5350;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
    }

    button.delete-btn:hover {
      background-color: #c62828;
    }

    /* new: edit button style */
    button.edit-btn {
      background-color: #ff9800;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
    }

    button.edit-btn:hover {
      background-color: #fb8c00;
    }

    .form-popup {
      display: none;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 400px;
      max-width: 90vw;
      max-height: 85vh;
      overflow-y: auto;
      z-index: 10;
    }

    .form-popup h3 {
      margin-top: 0;
      color: #1565c0;
    }

    .form-popup input,
    .form-popup select,
    .form-popup textarea {
      width: 100%;
      margin-bottom: 12px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: inherit;
    }

    .form-popup label {
      display: block;
      margin-bottom: 5px;
      color: #555;
      font-weight: 500;
    }

    .form-buttons {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .form-buttons button {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    .form-buttons .save-btn {
      background-color: #1e88e5;
      color: white;
    }

    .form-buttons .cancel-btn {
      background-color: #9e9e9e;
      color: white;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 5;
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

    .filter-box select,
    .filter-box input {
      padding: 8px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .absen-summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 10px;
      margin: 15px 0;
    }

    .absen-summary div {
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      font-weight: 600;
    }

    .hadir {
      background-color: #c8e6c9;
      color: #2e7d32;
    }

    .sakit {
      background-color: #fff9c4;
      color: #f57f17;
    }

    .izin {
      background-color: #b3e5fc;
      color: #01579b;
    }

    .alpha {
      background-color: #ffcdd2;
      color: #c62828;
    }

    .student-list {
      display: grid;
      gap: 8px;
      margin-top: 10px;
    }

    .student-item {
      padding: 10px;
      background: #f5f5f5;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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

    /* Responsive Navigation */
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      nav {
        width: 100%;
        padding: 10px;
      }

      nav ul {
        display: flex;
        justify-content: space-between;
        margin: 0;
      }

      nav li {
        margin: 0;
      }

      nav button {
        padding: 8px;
        font-size: 0;
        /* Hide text */
        text-align: center;
        border-radius: 50%;
        width: 45px;
        height: 45px;
      }

      nav button::before {
        font-size: 20px;
      }

      /* Icon-only buttons */
      nav button[onclick*="dashboard"]::before {
        content: "📊";
      }

      nav button[onclick*="absensi"]::before {
        content: "✅";
      }

      nav button[onclick*="jadwal"]::before {
        content: "📅";
      }

      nav button[onclick*="nilai"]::before {
        content: "📝";
      }

      /* Stats Grid */
      .stats-grid {
        grid-template-columns: 1fr;
        gap: 10px;
      }

      /* Tables */
      .card {
        padding: 10px;
        overflow-x: auto;
      }

      table {
        font-size: 14px;
      }

      th,
      td {
        padding: 8px;
      }

      /* Header */
      header h1 {
        font-size: 18px;
      }

      /* Forms */
      .form-popup {
        width: 95%;
        padding: 15px;
      }
    }

    /* Small Mobile */
    @media (max-width: 480px) {
      nav button {
        width: 40px;
        height: 40px;
      }

      .stat-card .number {
        font-size: 24px;
      }

      header h1 {
        font-size: 16px;
      }

      .logout-btn {
        padding: 6px 12px;
        font-size: 13px;
      }
    }

    .hide-mobile {
      display: block;
    }

    @media (max-width: 768px) {
      .hide-mobile {
        display: none;
      }
    }
  </style>
</head>

<body>
  <header>
    <h1>🎓 SMK TI Garuda Nusantara</h1>
    <button class="logout-btn" onclick="logout()">Logout</button>
  </header>

  <div class="container">
    <nav>
      <h2 class="hide-mobile">Panel Guru</h2>
      <ul>
        <li><button onclick="showSection('dashboard', this)" class="active">Dashboard</button></li>
        <li><button onclick="showSection('absensi', this)">Absensi</button></li>
        <li><button onclick="showSection('jadwal', this)">Jadwal</button></li>
        <li><button onclick="showSection('nilai', this)">Nilai</button></li>
      </ul>
    </nav>
    <main id="content"></main>
  </div>

  <div class="overlay" id="overlay"></div>
  <div class="form-popup" id="formPopup"></div>

  <script>
    // data inisialisasi dari server
    const data = {
      kelas: <?php echo json_encode($kelas_data); ?>,
      siswa: <?php echo json_encode($siswa_data); ?>,
      mapel: <?php echo json_encode($mapel_data); ?>, // <-- ditambahkan
      absensi: [], nilai: [], jadwal: []
    };

    const currentUser = <?php echo json_encode(['id' => $_SESSION['user_id'], 'level' => $_SESSION['level']]); ?>;
    data.mapel_user = []; // mapel yang boleh diakses oleh user ini

    async function fetchUserMapel() {
      try {
        const resp = await fetch('api.php?action=get_mapel_for_user', { credentials: 'same-origin' });
        const text = await resp.text();
        const result = JSON.parse(text);
        if (result.status === 'success') {
          data.mapel_user = result.data || [];
          return data.mapel_user;
        }
      } catch (e) {
        console.error('fetchUserMapel error', e);
      }
      data.mapel_user = [];
      return data.mapel_user;
    }

    // helper kecil untuk mencegah XSS saat menampilkan data
    function escapeHtml(unsafe) {
      if (unsafe === null || unsafe === undefined) return '';
      return String(unsafe).replace(/[&<>"']/g, function (m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
      });
    }

    // dipanggil ketika select kelas berubah di form absensi
    function loadSiswaForAbsen() {
      const kelasId = document.getElementById('kelas_id')?.value;
      const container = document.getElementById('siswaAbsenList');
      if (!container) return;
      if (!kelasId) {
        container.innerHTML = '<p>Pilih kelas untuk melihat daftar siswa.</p>';
        return;
      }

      const siswaList = data.siswa.filter(s => String(s.kelas_id) === String(kelasId));
      if (siswaList.length === 0) {
        container.innerHTML = '<p>Tidak ada siswa di kelas ini.</p>';
        return;
      }

      let html = '<h4>Daftar Siswa:</h4><div class="student-list">';
      siswaList.forEach(s => {
        html += `
        <div class="student-item" data-siswa-id="${s.id}">
          <span>${s.nama}</span>
          <select id="status-${s.id}">
            <option value="Hadir">Hadir</option>
            <option value="Sakit">Sakit</option>
            <option value="Izin">Izin</option>
            <option value="Alpha">Alpha</option>
          </select>
          <input type="text" id="ket-${s.id}" placeholder="Keterangan (opsional)">
        </div>
      `;
      });
      html += '</div>';
      container.innerHTML = html;
    }

    // fallback: jika select kelas dibuat dinamis, pastikan onchange memanggil loadSiswaForAbsen()
    document.addEventListener('change', (e) => {
      if (e.target && e.target.id === 'kelas_id') loadSiswaForAbsen();
    });

    // fetchData — sertakan cookies/session dan tangani response non-JSON
    async function fetchData(type) {
      try {
        const response = await fetch(`api.php?action=get_${type}`, { credentials: 'same-origin' });
        const text = await response.text();
        try {
          const result = JSON.parse(text);
          if (result.status === 'success') {
            data[type] = result.data || [];
            return data[type];
          }
          if (result.message === 'Unauthorized') {
            alert('Sesi berakhir — silakan login ulang.');
            window.location.href = 'login.php';
            return [];
          }
          throw new Error(result.message || 'Fetch failed');
        } catch (e) {
          console.error('Server response not JSON:', text);
          throw e;
        }
      } catch (err) {
        console.error(`Error fetching ${type}:`, err);
        return [];
      }
    }

    // dashboard: ambil data untuk menampilkan jumlah
    async function showSection(section, btn) {
      document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const main = document.getElementById('content');

      try {
        if (section === 'dashboard') {
          await Promise.all([fetchData('absensi'), fetchData('nilai'), fetchData('jadwal')]);
          main.innerHTML = `
              <div class="card">
                  <h2>Dashboard Guru</h2>
                  <div class="stats-grid">
                      <div class="stat-card">
                          <h3>Total Kelas</h3>
                          <div class="number">${data.kelas.length}</div>
                      </div>
                      <div class="stat-card">
                          <h3>Total Absensi</h3>
                          <div class="number">${data.absensi.length}</div>
                      </div>
                      <div class="stat-card">
                          <h3>Total Nilai</h3>
                          <div class="number">${data.nilai.length}</div>
                      </div>
                  </div>
              </div>
              <div class="card">
                  <h3>Selamat Datang di Dashboard Guru</h3>
                  <p>Gunakan menu di samping untuk mengelola data pengajaran.</p>
              </div>
          `;
          return;
        }

        if (section === 'absensi') {
          await fetchData('absensi');
          main.innerHTML = `
              <div class="card">
                  <h2>Rekap Absensi</h2>
                  <div class="filter-box">
                      <input type="date" id="filterTanggal" onchange="filterAbsensi()">
                      <select id="filterKelas" onchange="filterAbsensi()">
                          <option value="">Semua Kelas</option>
                          ${data.kelas.map(k => `<option value="${k.id}">${k.nama}</option>`).join('')}
                      </select>
                  </div>
                  <button class='add-btn' onclick="openForm('absensi')">+ Input Absensi</button>
                  ${renderTable('absensi')}
              </div>
          `;
          return;
        }

        if (section === 'nilai') {
          await fetchData('nilai');

          // Filter opsi mapel berdasarkan yang diajar guru
          let mapelOptions = '';
          if (currentUser.level === 'admin') {
            mapelOptions = data.mapel.map(m =>
              `<option value="${escapeHtml(m.nama)}">${escapeHtml(m.nama)}</option>`
            ).join('');
          } else {
            mapelOptions = data.mapel_user.map(m =>
              `<option value="${escapeHtml(m.nama)}">${escapeHtml(m.nama)}</option>`
            ).join('');

            // Filter nilai yang ditampilkan
            if (data.mapel_user.length > 0) {
              const allowedMapel = data.mapel_user.map(m => m.nama);
              data.nilai = data.nilai.filter(n => allowedMapel.includes(n.mapel));
            }
          }

          main.innerHTML = `
              <div class="card">
                  <h2>Input Nilai</h2>
                  <div class="filter-box">
                      <select id="filterMapel" onchange="filterNilai()">
                          <option value="">Semua Mapel</option>
                          ${mapelOptions}
                      </select>
                      <select id="filterKelasNilai" onchange="filterNilai()">
                          <option value="">Semua Kelas</option>
                          ${data.kelas.map(k => `<option value="${k.id}">${k.nama}</option>`).join('')}
                      </select>
                  </div>
                  <button class='add-btn' onclick="openForm('nilai')">+ Input Nilai</button>
                  ${renderTable('nilai')}
              </div>
          `;
          return;
        }

        if (section === 'jadwal') {
          await fetchData('jadwal');

          // Filter jadwal berdasarkan mapel guru
          if (currentUser.level !== 'admin' && data.mapel_user.length > 0) {
            const allowedMapel = data.mapel_user.map(m => m.nama);
            data.jadwal = data.jadwal.filter(j => allowedMapel.includes(j.mapel));
          }

          main.innerHTML = `
              <div class="card">
                  <h2>Jadwal Mengajar</h2>
                  <div class="filter-box">
                      <select id="filterHari" onchange="filterJadwal()">
                          <option value="">Semua Hari</option>
                          <option value="Senin">Senin</option>
                          <option value="Selasa">Selasa</option>
                          <option value="Rabu">Rabu</option>
                          <option value="Kamis">Kamis</option>
                          <option value="Jumat">Jumat</option>
                          <option value="Sabtu">Sabtu</option>
                      </select>
                      <select id="filterKelasJadwal" onchange="filterJadwal()">
                          <option value="">Semua Kelas</option>
                          ${data.kelas.map(k => `<option value="${k.id}">${k.nama}</option>`).join('')}
                      </select>
                      <select id="filterMapelJadwal" onchange="filterJadwal()">
                          <option value="">Semua Mapel</option>
                          ${currentUser.level === 'admin'
              ? data.mapel.map(m => `<option value="${m.nama}">${m.nama}</option>`).join('')
              : data.mapel_user.map(m => `<option value="${m.nama}">${m.nama}</option>`).join('')
            }
                      </select>
                  </div>
                  ${renderTable('jadwal')}
              </div>
          `;
          return;
        }

      } catch (error) {
        console.error('Error in showSection:', error);
        main.innerHTML = `<div class="card"><p>Error loading data: ${error.message}</p></div>`;
      }
    }

    // Update fungsi renderTable
    function renderTable(type) {
      console.log(`Rendering ${type} table with data:`, data[type]); // Debug log

      if (!Array.isArray(data[type])) {
        console.error(`Invalid data for ${type}:`, data[type]);
        return `<table><tbody><tr><td>Error loading data</td></tr></tbody></table>`;
      }

      let headers = '', rows = '';

      if (type === 'absensi') {
        headers = '<tr><th>Tanggal</th><th>Kelas</th><th>Total</th><th>Hadir</th><th>Sakit</th><th>Izin</th><th>Alpha</th><th>Aksi</th></tr>';
        rows = data[type].map(a => `
          <tr>
              <td>${a.tanggal || '-'}</td>
              <td>${a.kelas_nama || '-'}</td>
              <td>${a.total_siswa ?? 0}</td>
              <td>${a.hadir ?? 0}</td>
              <td>${a.sakit ?? 0}</td>
              <td>${a.izin ?? 0}</td>
              <td>${a.alpha ?? 0}</td>
              <td>
                  <button class="action-btn" onclick="showAbsenDetail(${a.id})">Detail</button>
                  <button class="edit-btn" onclick="openEditForm('absensi', ${a.id})">Edit</button>
              </td>
          </tr>
      `).join('');
      }

      if (type === 'nilai') {
        // Filter nilai berdasarkan mapel guru jika bukan admin
        let filteredData = data[type];
        if (currentUser.level !== 'admin' && data.mapel_user.length > 0) {
          const allowedMapel = data.mapel_user.map(m => m.nama);
          filteredData = filteredData.filter(n => allowedMapel.includes(n.mapel));
        }

        headers = '<tr><th>Nama Siswa</th><th>Kelas</th><th>Tugas</th><th>Mapel</th><th>UTS</th><th>UAS</th><th>Rata-rata</th><th>Aksi</th></tr>';
        rows = filteredData.map(n => {
          // Cari nama kelas dari data siswa
          const siswa = data.siswa.find(s => String(s.id) === String(n.siswa_id));
          const kelasNama = siswa ? siswa.nama_kelas : '-';

          const tugas = parseInt(n.tugas || 0);
          const uts = parseInt(n.uts || 0);
          const uas = parseInt(n.uas || 0);
          const avg = ((tugas + uts + uas) / 3).toFixed(1);
          return `<tr>
            <td>${n.siswa_nama || '-'}</td>
            <td>${kelasNama}</td>
            <td>${tugas}</td>
            <td>${n.mapel || '-'}</td>
            <td>${uts}</td>
            <td>${uas}</td>
            <td><strong>${avg}</strong></td>
            <td><button class="edit-btn" onclick="openEditForm('nilai', ${n.id})">Edit</button></td>
        </tr>`;
        }).join('');
      }

      if (type === 'jadwal') {
        // Filter jadwal berdasarkan mapel guru jika bukan admin
        let filteredData = data[type];
        if (currentUser.level !== 'admin' && data.mapel_user.length > 0) {
          const allowedMapel = data.mapel_user.map(m => m.nama);
          filteredData = filteredData.filter(j => allowedMapel.includes(j.mapel));
        }

        headers = '<tr><th>Kelas</th><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Aksi</th></tr>';
        rows = filteredData.map(j => `
          <tr>
              <td>${j.kelas_nama || '-'}</td>
              <td>${j.hari || '-'}</td>
              <td>${j.jam || '-'}</td>
              <td>${j.mapel || '-'}</td>
              <td>
                  <button class="edit-btn" onclick="openEditForm('jadwal', ${j.id})">Edit</button>
              </td>
          </tr>
      `).join('');
      }

      return `<table><thead>${headers}</thead><tbody>${rows || '<tr><td colspan="100%" style="text-align:center;">Belum ada data</td></tr>'}</tbody></table>`;
    }

    async function openForm(type) {
      const popup = document.getElementById('formPopup');  // Tambahkan ini
      const overlay = document.getElementById('overlay'); // Tambahkan ini

      if (type === 'nilai') {
        await fetchUserMapel();

        const userMapels = data.mapel_user;
        if (!userMapels.length) {
          alert('Anda belum memiliki mata pelajaran yang terdaftar. Hubungi admin.');
          return;
        }

        const mapelOptions = userMapels.map(m =>
          `<option value="${escapeHtml(m.nama)}">${escapeHtml(m.nama)}</option>`
        ).join('');

        const kelasOptions = data.kelas.map(k =>
          `<option value="${k.id}">${escapeHtml(k.nama)}</option>`
        ).join('');

        popup.innerHTML = `
            <h3>Input Nilai</h3>
            <form id="nilaiForm" onsubmit="addData('nilai'); return false;">
                <label>Kelas</label>
                <select id="kelas_select" onchange="loadSiswaForNilai()" required>
                    <option value="">Pilih Kelas</option>
                    ${kelasOptions}
                </select>

                <label>Siswa</label>
                <select id="siswa_id" required>
                    <option value="">Pilih Kelas Terlebih dahulu</option>
                </select>

                <label>Mata Pelajaran</label>
                <select id="mapel_select" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    ${mapelOptions}
                </select>

                <label>Tugas</label>
                <input type="number" id="tugas" required min="0" max="100">

                <label>UTS</label>
                <input type="number" id="uts" required min="0" max="100">

                <label>UAS</label>
                <input type="number" id="uas" required min="0" max="100">

                <div class="form-buttons">
                    <button type="submit" class="save-btn">Simpan</button>
                    <button type="button" class="cancel-btn" onclick="closeForm()">Batal</button>
                </div>
            </form>
        `;

        popup.style.display = 'block';
        overlay.style.display = 'block';
      }
      else if (type === 'absensi') {
        const kelasOptions = data.kelas.map(k =>
          `<option value="${k.id}">${k.nama}</option>`
        ).join('');

        const today = new Date().toISOString().split('T')[0];
        popup.innerHTML = `
            <h3>Input Absensi Harian</h3>
            <form id="absenForm" onsubmit="addData('absensi'); return false;">
                <label>Tanggal</label>
                <input type="date" id="tanggal" value="${today}" required>
                
                <label>Kelas</label>
                <select id="kelas_id" onchange="loadSiswaForAbsen()" required>
                    <option value="">Pilih Kelas</option>
                    ${kelasOptions}
                </select>
                
                <div id="siswaAbsenList"></div>
                
                <div class="form-buttons">
                    <button type="submit" class="save-btn">Simpan Absensi</button>
                    <button type="button" class="cancel-btn" onclick="closeForm()">Batal</button>
                </div>
            </form>
        `;

        popup.style.display = 'block';
        overlay.style.display = 'block';

        setTimeout(() => loadSiswaForAbsen(), 100);
      }
    }

    // Update fungsi addData
    async function addData(type) {
      if (type === 'nilai') {
        const formData = {
          action: 'add_nilai',
          siswa_id: document.getElementById('siswa_id').value,
          kelas_id: document.getElementById('kelas_select').value,
          mapel: document.getElementById('mapel_select').value,
          tugas: document.getElementById('tugas').value,
          uts: document.getElementById('uts').value,
          uas: document.getElementById('uas').value
        };
        try {
          const response = await fetch('api.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
          });
          const text = await response.text();
          let result;
          try { result = JSON.parse(text); } catch (e) { throw new Error('Server error (invalid JSON)'); }
          if (result.status === 'success') {
            alert('Data berhasil disimpan');
            closeForm();
            showSection(type, document.querySelector(`nav button[onclick*="${type}"]`));
          } else {
            if (result.message === 'Unauthorized') { window.location.href = 'login.php'; return; }
            throw new Error(result.message || 'Terjadi kesalahan');
          }
        } catch (error) {
          alert('Gagal menyimpan data: ' + error.message);
          console.error(error);
        }
      }
      else if (type === 'absensi') {
        const kelasId = document.getElementById('kelas_id').value;
        const siswaList = document.querySelectorAll('.student-item');
        const detail = [];

        siswaList.forEach(item => {
          const siswaId = item.getAttribute('data-siswa-id');
          detail.push({
            siswa_id: siswaId,
            status: document.getElementById(`status-${siswaId}`).value,
            keterangan: document.getElementById(`ket-${siswaId}`).value
          });
        });

        formData = {
          action: 'add_absensi',
          kelas_id: kelasId,
          tanggal: document.getElementById('tanggal').value,
          total: siswaList.length,
          hadir: detail.filter(d => d.status === 'Hadir').length,
          sakit: detail.filter(d => d.status === 'Sakit').length,
          izin: detail.filter(d => d.status === 'Izin').length,
          alpha: detail.filter(d => d.status === 'Alpha').length,
          detail: detail
        };

        try {
          const response = await fetch('api.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
          });
          const text = await response.text();
          let result;
          try { result = JSON.parse(text); } catch (e) { throw new Error('Server error (invalid JSON)'); }
          if (result.status === 'success') {
            alert('Data berhasil disimpan');
            closeForm();
            showSection(type, document.querySelector(`nav button[onclick*="${type}"]`));
          } else {
            if (result.message === 'Unauthorized') { window.location.href = 'login.php'; return; }
            throw new Error(result.message || 'Terjadi kesalahan');
          }
        } catch (error) {
          alert('Gagal menyimpan data: ' + error.message);
          console.error(error);
        }
      }
    }

    // replace deleteData block with edit handlers
    // ganti showAbsenDetail dengan versi yang toleran terhadap variasi nama field & tipe id
    function showAbsenDetail(id) {
      const lookup = String(id);
      const absen = (data.absensi || []).find(a => String(a.id) === lookup || String(Number(a.id)) === lookup);
      if (!absen) {
        console.warn('Absensi tidak ditemukan untuk id', id);
        return;
      }

      const popup = document.getElementById('formPopup');
      const overlay = document.getElementById('overlay');

      const detailsRaw = Array.isArray(absen.detail) ? absen.detail : [];
      const details = detailsRaw.map(d => {
        const siswaFromData = (data.siswa || []).find(s => String(s.id) === String(d.siswa_id));
        return {
          siswa_nama: d.siswa_nama ?? d.nama ?? siswaFromData?.nama ?? '—',
          status: d.status ?? '',
          keterangan: d.keterangan ?? d.ket ?? ''
        };
      });

      const hadir = ('hadir' in absen) ? absen.hadir : details.filter(d => String(d.status).toLowerCase() === 'hadir').length;
      const sakit = ('sakit' in absen) ? absen.sakit : details.filter(d => String(d.status).toLowerCase() === 'sakit').length;
      const izin = ('izin' in absen) ? absen.izin : details.filter(d => String(d.status).toLowerCase() === 'izin').length;
      const alpha = ('alpha' in absen) ? absen.alpha : details.filter(d => String(d.status).toLowerCase() === 'alpha').length;

      const absentList = details.filter(d => String(d.status).toLowerCase() !== 'hadir');

      let detailHtml = '';
      if (absentList.length === 0) {
        detailHtml = '<p>Semua siswa hadir!</p>';
      } else {
        detailHtml = '<h4>Siswa Tidak Hadir:</h4><div class="student-list">';
        absentList.forEach(d => {
          detailHtml += `
          <div class="student-item">
            <div>
              <strong>${escapeHtml(d.siswa_nama)}</strong><br>
              <small>Status: ${escapeHtml(d.status)}</small><br>
              ${d.keterangan ? `<small>Ket: ${escapeHtml(d.keterangan)}</small>` : ''}
            </div>
          </div>
        `;
        });
        detailHtml += '</div>';
      }

      const kelasName = absen.kelas_nama ?? absen.kelas ?? absen.kelas_id ?? '-';
      popup.innerHTML = `
      <h3>Detail Absensi - ${escapeHtml(absen.tanggal ?? '')}</h3>
      <p><strong>Kelas:</strong> ${escapeHtml(kelasName)}</p>
      <div class="absen-summary">
        <div class="hadir">Hadir: ${hadir}</div>
        <div class="sakit">Sakit: ${sakit}</div>
        <div class="izin">Izin: ${izin}</div>
        <div class="alpha">Alpha: ${alpha}</div>
      </div>
      ${detailHtml}
      <div class="form-buttons">
        <button class="cancel-btn" onclick="closeForm()">Tutup</button>
      </div>
    `;

      popup.style.display = 'block';
      overlay.style.display = 'block';
    }

    // Update fungsi logout
    function logout() {
      if (confirm('Yakin ingin logout?')) {
        // Langsung redirect ke halaman logout
        window.location.href = '../config/logout.php';
      }
    }

    // Fungsi filter absensi berdasarkan tanggal dan kelas
    function filterAbsensi() {
      const tanggal = document.getElementById('filterTanggal').value;
      const kelas = document.getElementById('filterKelas').value;
      let filtered = data.absensi;

      if (tanggal) {
        filtered = filtered.filter(a => a.tanggal === tanggal);
      }
      if (kelas) {
        filtered = filtered.filter(a => String(a.kelas_id) === String(kelas));
      }

      const tbody = document.querySelector('#content table tbody');
      if (tbody) {
        tbody.innerHTML = filtered.length ? filtered.map(a => `
                <tr>
                    <td>${a.tanggal || '-'}</td>
                    <td>${a.kelas_nama || '-'}</td>
                    <td>${a.total_siswa ?? 0}</td>
                    <td>${a.hadir ?? 0}</td>
                    <td>${a.sakit ?? 0}</td>
                    <td>${a.izin ?? 0}</td>
                    <td>${a.alpha ?? 0}</td>
                    <td>
                        <button class="action-btn" onclick="showAbsenDetail(${a.id})">Detail</button>
                        <button class="edit-btn" onclick="openEditForm('absensi', ${a.id})">Edit</button>
                    </td>
                </tr>
            `).join('') : '<tr><td colspan="100%" style="text-align:center;">Data tidak ditemukan</td></tr>';
      }
    }

    // Fungsi filter nilai berdasarkan mapel
    function filterNilai() {
      const mapel = document.getElementById('filterMapel').value;
      const kelas = document.getElementById('filterKelasNilai').value;
      let filtered = data.nilai;

      if (mapel) {
        filtered = filtered.filter(n => n.mapel === mapel);
      }
      if (kelas) {
        filtered = filtered.filter(n => {
          const siswa = data.siswa.find(s => String(s.id) === String(n.siswa_id));
          return String(siswa?.kelas_id) === String(kelas);
        });
      }

      if (currentUser.level !== 'admin' && data.mapel_user.length > 0) {
        const allowedMapel = data.mapel_user.map(m => m.nama);
        filtered = filtered.filter(n => allowedMapel.includes(n.mapel));
      }

      const tbody = document.querySelector('#content table tbody');
      if (tbody) {
        tbody.innerHTML = filtered.length ? filtered.map(n => {
          const siswa = data.siswa.find(s => String(s.id) === String(n.siswa_id));
          const kelasNama = siswa ? siswa.nama_kelas : '-';

          const tugas = parseInt(n.tugas || 0);
          const uts = parseInt(n.uts || 0);
          const uas = parseInt(n.uas || 0);
          const avg = ((tugas + uts + uas) / 3).toFixed(1);
          return `<tr>
                <td>${n.siswa_nama || '-'}</td>
                <td>${kelasNama}</td>
                <td>${tugas}</td>
                <td>${n.mapel || '-'}</td>
                <td>${uts}</td>
                <td>${uas}</td>
                <td><strong>${avg}</strong></td>
                <td><button class="edit-btn" onclick="openEditForm('nilai', ${n.id})">Edit</button></td>
            </tr>`;
        }).join('') : '<tr><td colspan="100%" style="text-align:center;">Data tidak ditemukan</td></tr>';
      }
    }

    // Tambahkan fungsi filter jadwal
    function filterJadwal() {
      const hari = document.getElementById('filterHari').value;
      const kelas = document.getElementById('filterKelasJadwal').value;
      const mapel = document.getElementById('filterMapelJadwal').value;
      let filtered = data.jadwal;

      // Filter berdasarkan level user dan mapel yang diajar
      if (currentUser.level !== 'admin' && data.mapel_user.length > 0) {
        const allowedMapel = data.mapel_user.map(m => m.nama);
        filtered = filtered.filter(j => allowedMapel.includes(j.mapel));
      }

      if (hari) {
        filtered = filtered.filter(j => j.hari === hari);
      }
      if (kelas) {
        filtered = filtered.filter(j => String(j.kelas_id) === String(kelas));
      }
      if (mapel) {
        filtered = filtered.filter(j => j.mapel === mapel);
      }

      const tbody = document.querySelector('#content table tbody');
      if (tbody) {
        tbody.innerHTML = filtered.length ? filtered.map(j => `
              <tr>
                  <td>${j.kelas_nama || '-'}</td>
                  <td>${j.hari || '-'}</td>
                  <td>${j.jam || '-'}</td>
                  <td>${j.mapel || '-'}</td>
                  <td>
                      <button class="edit-btn" onclick="openEditForm('jadwal', ${j.id})">Edit</button>
                  </td>
              </tr>
          `).join('') : '<tr><td colspan="100%" style="text-align:center;">Data tidak ditemukan</td></tr>';
      }
    }

    // Fungsi untuk memuat daftar siswa berdasarkan kelas yang dipilih
    function loadSiswaForNilai() {
      const kelasId = document.getElementById('kelas_select').value;
      const siswaSelect = document.getElementById('siswa_id');

      if (!kelasId) {
        siswaSelect.innerHTML = '<option value="">Pilih Kelas Terlebih dahulu</option>';
        return;
      }

      // Filter siswa berdasarkan kelas yang dipilih
      const siswaList = data.siswa.filter(s => String(s.kelas_id) === String(kelasId));

      if (siswaList.length === 0) {
        siswaSelect.innerHTML = '<option value="">Tidak ada siswa di kelas ini</option>';
        return;
      }

      // Urutkan siswa berdasarkan nama
      siswaList.sort((a, b) => (a.nama || '').localeCompare(b.nama || ''));

      siswaSelect.innerHTML = '<option value="">Pilih Siswa</option>' +
        siswaList.map(s => `<option value="${s.id}">${escapeHtml(s.nama)}</option>`).join('');
    }

    // Panggil saat halaman dimuat
    document.addEventListener('DOMContentLoaded', async function () {
      // Initialize dashboard
      showSection('dashboard', document.querySelector('nav button'));

      // Ambil daftar mapel guru
      if (currentUser.level !== 'admin') {
        await fetchUserMapel();
      }
    });

    // Pastikan event listener untuk nav buttons bekerja
    document.addEventListener('DOMContentLoaded', function () {
      // Initialize dashboard
      showSection('dashboard', document.querySelector('nav button'));

      // Add click handlers to nav buttons
      document.querySelectorAll('nav button').forEach(button => {
        button.addEventListener('click', function (e) {
          const section = this.getAttribute('onclick').match(/showSection\('(.+?)',/)[1];
          showSection(section, this);
        });
      });

      // Add click handler to logout button
      document.querySelector('.logout-btn').addEventListener('click', logout);
    });

    // Tambahkan fungsi closeForm
    function closeForm() {
      const popup = document.getElementById('formPopup');
      const overlay = document.getElementById('overlay');
      popup.style.display = 'none';
      overlay.style.display = 'none';
      popup.innerHTML = ''; // Clear form content
    }
  </script>
</body>

</html>