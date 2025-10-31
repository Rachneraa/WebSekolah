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
    <!-- Sidebar -->
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

    <!-- Page Content -->
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
        <?php include $page . '.php'; ?>
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
    });
  </script>
</body>

</html>