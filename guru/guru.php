<?php
session_start();
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Guru Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: #f5f8ff;
    }

    .wrapper {
      display: flex;
      width: 100%;
    }

    #sidebar {
      min-width: 220px;
      max-width: 220px;
      min-height: 100vh;
      background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
      color: #fff;
      transition: transform 0.3s ease;
      z-index: 1040;
    }

    #sidebar .sidebar-header {
      padding: 20px;
      text-align: center;
    }

    #sidebar ul {
      list-style: none;
      padding: 0;
    }

    #sidebar ul li a {
      display: block;
      color: #fff;
      padding: 15px 20px;
      text-decoration: none;
      border-radius: 8px;
      margin: 8px 0;
    }

    #sidebar ul li a.active,
    #sidebar ul li a:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    #content {
      flex: 1;
      padding: 30px;
      min-height: 100vh;
    }

    .logout-btn {
      background: rgba(255, 255, 255, 0.2);
      border: 1px solid white;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
      transition: all 0.2s;
      float: right;
      margin: 20px;
    }

    .logout-btn:hover {
      background: white;
      color: #1565c0;
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
  </style>
</head>

<body>
  <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
      <div class="sidebar-header">
        <h3>Guru Panel</h3>
      </div>
      <ul>
        <li><a href="guru.php?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>"><i
              class="fas fa-home"></i>
            Dashboard</a></li>
        <li><a href="guru.php?page=absensi" class="<?= $page == 'absensi' ? 'active' : '' ?>"><i
              class="fas fa-clipboard-list"></i> Absensi</a></li>
        <li><a href="guru.php?page=jadwal" class="<?= $page == 'jadwal' ? 'active' : '' ?>"><i
              class="fas fa-calendar-alt"></i> Jadwal</a></li>
        <li><a href="guru.php?page=nilai" class="<?= $page == 'nilai' ? 'active' : '' ?>"><i
              class="fas fa-file-alt"></i>
            Nilai</a></li>
      </ul>
      <a href="../config/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <!-- Content -->
    <div id="content">
      <?php include $page . '.php'; ?>
    </div>
  </div>
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('active');
    });
    // Optional: close sidebar when clicking outside (mobile)
    document.addEventListener('click', function (e) {
      if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
        if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
          sidebar.classList.remove('active');
        }
      }
    });
  </script>
</body>

</html>