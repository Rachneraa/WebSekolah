<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ob_start();
session_start();
require_once '../config/koneksi.php';

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="theme-color" content="#00499D">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC">

    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <link rel="manifest" href="/manifest.json">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

        .wrapper {
            display: flex;
            width: 100%;
        }

        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            transition: all 0.3s;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 12px 15px;
            font-size: 15px;
            display: block;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 8px;
            transition: all 0.2s;
        }

        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        #sidebar ul li.active>a {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        #content {
            flex: 1;
            padding: 25px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px 20px;
        }

        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table th {
            background-color: #e3f2fd;
            color: #1565c0;
            font-weight: 600;
            border-bottom: 2px solid #1565c0;
        }

        .table td,
        .table th {
            padding: 12px;
            vertical-align: middle;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
        }

        .btn-danger {
            background: #ef5350;
            border: none;
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

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid white;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: white;
            color: #1565c0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -250px;
                position: fixed;
                height: 100%;
                z-index: 999;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                width: 100%;
                margin-left: 0;
            }

            #sidebarCollapse {
                display: block;
            }
        }

        @media (min-width: 769px) {
            #sidebarCollapse {
                display: none;
            }
        }

        /* Tambahkan style untuk tombol collapse */
        #sidebarCollapse {
            background: #1565c0;
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            margin-right: 15px;
            z-index: 1100;
            position: relative;
        }

        #sidebarCollapse:hover {
            background: #1976d2;
        }

        /* Tambahkan animasi untuk smooth transition */
        #sidebar,
        #content {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="active">
                    <a href="?page=dashboard">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="?page=siswa">
                        <i class="fas fa-user-graduate"></i> Data Siswa
                    </a>
                </li>
                <li>
                    <a href="?page=guru">
                        <i class="fas fa-chalkboard-teacher"></i> Data Guru
                    </a>
                </li>
                <li>
                    <a href="?page=kelas">
                        <i class="fas fa-school"></i> Data Kelas dan Absensi
                    </a>
                </li>
                <li>
                    <a href="?page=berita">
                        <i class="fas fa-newspaper"></i> Berita
                    </a>
                </li>
                <li>
                    <a href="?page=ppdb_admin">
                        <i class="fas fa-newspaper"></i> Pendaftaran Siswa Baru
                    </a>
                </li>

                <li>
                    <a href="?page=kontak">
                        <i class="fas fa-fw fa-envelope"></i> Pesan Masuk
                    </a>
                </li>
                <li>
                    <a href="?page=jadwal">
                        <i class="fas fa-calendar-alt"></i> Jadwal Pelajaran
                    </a>
                </li>
                <li>
                    <a href="?page=mapel">
                        <i class="fas fa-file-alt"></i> Mata Pelajaran
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
                $page = $_GET['page'] ?? 'dashboard';
                $file = "modules/$page.php";
                if (file_exists($file)) {
                    include $file;
                } else {
                    include "modules/dashboard.php";
                }
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
                $('#sidebar').toggleClass('active');
                $(this).toggleClass('active');
            });

            // Handle active menu
            const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
            $('#sidebar li').removeClass('active');
            $(`#sidebar a[href="?page=${currentPage}"]`).parent().addClass('active');

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
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
<form action="admin.php?page=jadwal" method="post"></form>
<?php ob_end_flush(); ?>