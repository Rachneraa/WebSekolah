<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/koneksi.php';

// Cek login dan level guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

// Routing halaman
$page = $_GET['page'] ?? 'dashboard';
$allowed = ['dashboard', 'absensi', 'jadwal', 'nilai'];
if (!in_array($page, $allowed))
    $page = 'dashboard';

// ===================================================================
// === LOGIKA PEMROSESAN FORM DIPINDAHKAN KE SINI (SEBELUM HTML) ===
// ===================================================================

$pesan_sukses = ''; // Variabel untuk pesan sukses

if ($page == 'nilai') {
    // Ambil guru_id yang benar dari session
    $guru_id = $_SESSION['guru_id']; 

    // === Logika Hapus Tugas (dari nilai.php) ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_tugas'])) {
        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $tugas_ke = intval($_POST['tugas_ke'] ?? 0);
        
        if ($kelas_id && $mapel_id && $tugas_ke) {
            $stmt = $db->prepare("DELETE FROM nilai_tugas WHERE kelas_id=? AND mapel_id=? AND tugas_ke=?");
            $stmt->bind_param("iii", $kelas_id, $mapel_id, $tugas_ke);
            $stmt->execute();
            $stmt->close();
            
            // Simpan pesan sukses di session untuk ditampilkan setelah redirect
            $_SESSION['pesan_sukses'] = "Tugas ke-$tugas_ke berhasil dihapus.";
            
            // Redirect sekarang aman karena ada di atas
            header("Location: guru.php?page=nilai&kelas=" . $kelas_id . "&mapel=" . $mapel_id);
            exit();
        }
    }

    // === Logika Simpan Semua Nilai (dari nilai.php) ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_semua'])) {
        $kelas_id = intval($_POST['kelas_id'] ?? 0);
        $mapel_id = intval($_POST['mapel_id'] ?? 0);
        $tugas_list = $_POST['tugas'] ?? [];
        $uts_list = $_POST['uts'] ?? [];
        $uas_list = $_POST['uas'] ?? [];
        $siswa_ids = $_POST['siswa_id'] ?? [];

        // Ambil nama mapel (hanya sekali)
        $stmt_mapel = $db->prepare("SELECT nama FROM mapel WHERE id = ?");
        $stmt_mapel->bind_param("i", $mapel_id);
        $stmt_mapel->execute();
        $stmt_mapel->bind_result($mapel_nama);
        $stmt_mapel->fetch();
        $stmt_mapel->close();

        if ($mapel_nama) {
            foreach ($siswa_ids as $siswa_id) {
                // Simpan nilai tugas
                if (!empty($tugas_list[$siswa_id])) {
                    foreach ($tugas_list[$siswa_id] as $tugas_ke => $nilai) {
                        $nilai = ($nilai === '' ? null : intval($nilai));
                        $stmt = $db->prepare("SELECT id FROM nilai_tugas WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=?");
                        $stmt->bind_param("iiii", $siswa_id, $kelas_id, $mapel_id, $tugas_ke);
                        $stmt->execute();
                        $stmt->store_result();
                        if ($stmt->num_rows > 0) {
                            $stmt->close();
                            $stmt2 = $db->prepare("UPDATE nilai_tugas SET nilai=? WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=?");
                            $stmt2->bind_param("iiiii", $nilai, $siswa_id, $kelas_id, $mapel_id, $tugas_ke);
                            $stmt2->execute();
                            $stmt2->close();
                        } else {
                            $stmt->close();
                            $stmt2 = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai) VALUES (?, ?, ?, ?, ?)");
                            $stmt2->bind_param("iiiii", $siswa_id, $kelas_id, $mapel_id, $tugas_ke, $nilai);
                            $stmt2->execute();
                            $stmt2->close();
                        }
                    }
                }

                // Simpan UTS & UAS
                $uts = (!empty($uts_list[$siswa_id]) && $uts_list[$siswa_id] !== '') ? intval($uts_list[$siswa_id]) : null;
                $uas = (!empty($uas_list[$siswa_id]) && $uas_list[$siswa_id] !== '') ? intval($uas_list[$siswa_id]) : null;

                // Cek & simpan ke tabel nilai
                $stmt = $db->prepare("SELECT id FROM nilai WHERE siswa_id = ? AND kelas_id = ? AND mapel = ?");
                $stmt->bind_param("iis", $siswa_id, $kelas_id, $mapel_nama);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $stmt->close();
                    $stmt2 = $db->prepare("UPDATE nilai SET uts = ?, uas = ? WHERE siswa_id = ? AND kelas_id = ? AND mapel = ?");
                    $stmt2->bind_param("iiiis", $uts, $uas, $siswa_id, $kelas_id, $mapel_nama);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $stmt->close();
                    $stmt2 = $db->prepare("INSERT INTO nilai (siswa_id, kelas_id, mapel, uts, uas) VALUES (?, ?, ?, ?, ?)");
                    $stmt2->bind_param("iisii", $siswa_id, $kelas_id, $mapel_nama, $uts, $uas);
                    $stmt2->execute();
                    $stmt2->close();
                }
            }
            // Tetapkan pesan sukses untuk ditampilkan di halaman
            $pesan_sukses = "Semua nilai berhasil disimpan.";
        } else {
            $_SESSION['error'] = "Gagal menyimpan nilai. Mapel tidak ditemukan.";
        }
    }
}

// Ambil pesan sukses dari session (jika ada dari redirect)
if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    unset($_SESSION['pesan_sukses']); // Hapus setelah diambil
}

// ===================================================================
// === BATAS AKHIR LOGIKA PEMROSESAN FORM ===
// ===================================================================
?>
<!DOCTYPE html>
<html lang="id">

<head>
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
  <meta charset="UTF-8">
  <title>Guru Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(135deg, #e3f0ff 0%, #f8fcff 100%);
      font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
    }

    .wrapper {
      display: flex;
      width: 100%;
    }

    #sidebar {
      min-width: 220px;
      max-width: 220px;
      min-height: 100vh;
      background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
      color: #fff;
      transition: transform 0.3s ease, box-shadow 0.3s;
      box-shadow: 2px 0 16px rgba(30, 136, 229, 0.08);
      z-index: 1040;
      border-top-right-radius: 24px;
      border-bottom-right-radius: 24px;
    }

    #sidebar .sidebar-header {
      padding: 28px 20px 18px 20px;
      text-align: center;
      background: rgba(255, 255, 255, 0.05);
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      border-top-right-radius: 24px;
    }

    #sidebar .sidebar-header h3 {
      font-weight: 700;
      letter-spacing: 1px;
      color: #fff;
      margin-bottom: 0;
      font-size: 1.6em;
      text-shadow: 0 2px 8px rgba(13, 71, 161, 0.12);
    }

    #sidebar ul {
      list-style: none;
      padding: 0;
      margin-top: 18px;
    }

    #sidebar ul li {
      margin-bottom: 8px;
    }

    #sidebar ul li a {
      display: flex;
      align-items: center;
      color: #e3f2fd;
      padding: 13px 24px;
      text-decoration: none;
      border-radius: 12px;
      font-size: 1.08em;
      font-weight: 500;
      letter-spacing: 0.2px;
      transition: background 0.2s, color 0.2s, box-shadow 0.2s;
      box-shadow: none;
      gap: 12px;
    }

    #sidebar ul li a.active,
    #sidebar ul li a:hover {
      background: linear-gradient(90deg, #64b5f6 0%, #1976d2 100%);
      color: #fff;
      box-shadow: 0 2px 8px rgba(25, 118, 210, 0.08);
      text-shadow: 0 1px 4px rgba(13, 71, 161, 0.10);
    }

    #sidebar ul li a i {
      font-size: 1.2em;
      margin-right: 8px;
    }

    #content {
      flex: 1;
      padding: 36px 32px 32px 32px;
      min-height: 100vh;
      background: transparent;
    }

    .logout-btn {
      background: linear-gradient(90deg, #e53935 0%, #b71c1c 100%);
      border: none;
      color: white;
      padding: 9px 22px;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.2s;
      float: right;
      margin: 20px;
      box-shadow: 0 2px 8px rgba(229, 57, 53, 0.08);
    }

    .logout-btn:hover {
      background: #fff;
      color: #b71c1c;
      border: 1px solid #b71c1c;
    }

    /* Hamburger button */
    .sidebar-toggle {
      display: none;
      position: fixed;
      top: 18px;
      right: 18px;
      left: auto;
      z-index: 1050;
      background: #1565c0;
      color: #fff;
      border: none;
      padding: 10px 12px;
      border-radius: 8px;
      font-size: 1.5em;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(21, 101, 192, 0.10);
    }

    @media (max-width: 768px) {
      .wrapper {
        flex-direction: column;
      }

      #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        transform: translateX(-100%);
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        z-index: 1040;
        border-radius: 0 24px 24px 0;
      }

      #sidebar.active {
        transform: translateX(0);
      }

      #content {
        padding: 16px;
      }

      .sidebar-toggle {
        display: block;
      }
    }

    @media (min-width: 769px) {
      #sidebar {
        display: block !important;
        position: relative;
        transform: none;
        box-shadow: none;
      }

      #sidebarCollapse {
        display: none !important;
      }
    }

    /* Navbar tweaks */
    .navbar {
      border-radius: 12px;
      margin-bottom: 18px;
      box-shadow: 0 2px 8px rgba(30, 136, 229, 0.06);
      background: #fff;
    }

    .navbar .btn-danger {
      font-weight: 600;
      border-radius: 8px;
      padding: 8px 18px;
      box-shadow: 0 2px 8px rgba(229, 57, 53, 0.08);
      transition: background 0.2s, color 0.2s;
    }

    .navbar .btn-danger:hover {
      background: #fff;
      color: #b71c1c;
      border: 1px solid #b71c1c;
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <nav id="sidebar">
      <div class="sidebar-header">
        <h3>Guru Panel</h3>
      </div>
      <ul class="list-unstyled components">
        <li>
          <a href="guru.php?page=dashboard">
            <i class="fas fa-home"></i> Dashboard
          </a>
        </li>
        <li>
          <a href="guru.php?page=absensi">
            <i class="fas fa-clipboard-list"></i> Absensi
          </a>
        </li>
        <li>
          <a href="guru.php?page=jadwal">
            <i class="fas fa-calendar-alt"></i> Jadwal
          </a>
        </li>
        <li>
          <a href="guru.php?page=nilai">
            <i class="fas fa-star"></i> Nilai
          </a>
        </li>
      </ul>
    </nav>

    <div id="content">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <button type="button" id="sidebarCollapse" class="btn">
            <i class="fas fa-bars"></i>
          </button>
          <div class="ms-auto">
            <a href="../config/logout.php" class="btn btn-danger">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </div>
      </nav>
      <div class="container-fluid">
        <?php 
          // Tampilkan pesan error global jika ada
          if (isset($_SESSION['error'])) {
              echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error']) . '
                    <button typebutton" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>';
              unset($_SESSION['error']);
          }
          // File include sekarang hanya berisi tampilan (SELECT dan HTML)
          include $page . '.php'; 
        ?>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      // Toggle sidebar
      $('#sidebarCollapse').on('click', function () {
        $('#sidebar').addClass('active');
      });

      // Close sidebar when clicking menu (mobile)
      $('#sidebar a').on('click', function () {
        if (window.innerWidth <= 768) {
          $('#sidebar').removeClass('active');
        }
      });

      // Handle active menu
      const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
      $('#sidebar li').removeClass('active');
      $(`#sidebar a[href="guru.php?page=${currentPage}"]`).parent().addClass('active');

      // Tambahan: Tutup sidebar jika klik di luar area sidebar (mobile)
      $(document).on('click', function(event) {
          if (window.innerWidth <= 768 && $('#sidebar').hasClass('active')) {
              if ($(event.target).closest('#sidebar').length === 0 && $(event.target).closest('#sidebarCollapse').length === 0) {
                  $('#sidebar').removeClass('active');
              }
          }
      });
    });
    // SERVICE WORKER REGISTRATION
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .then(registration => console.log('SW Registered'))
    .catch(error => console.log('SW Registration failed:', error));
}
  </script>
</body>

</html>