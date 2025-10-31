<?php
// Function to safely get count from table
function getTableCount($db, $table)
{
    try {
        $query = "SELECT COUNT(*) as count FROM $table";
        $result = mysqli_query($db, $query);
        return mysqli_fetch_assoc($result)['count'] ?? 0;
    } catch (mysqli_sql_exception $e) {
        return 0;
    }
}

// Get summary counts with error handling
$siswaCount = getTableCount($db, 'siswa');
$guruCount = getTableCount($db, 'guru');
$kelasCount = getTableCount($db, 'kelas');
$beritaCount = getTableCount($db, 'berita');

// Check if required tables exist
$tablesExist = true;
$requiredTables = ['siswa', 'guru', 'kelas', 'berita', 'absensi'];
foreach ($requiredTables as $table) {
    $result = mysqli_query($db, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) == 0) {
        $tablesExist = false;
        echo "<div class='alert alert-danger'>Table '$table' does not exist!</div>";
    }
}

// Only show dashboard if tables exist
if ($tablesExist):
    ?>

    <!-- CSS untuk Responsive & Lazy Loading -->
    <style>
        /* Lazy Load Animation */
        .lazy-section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .lazy-section.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Responsive Banner */
        .help-banner {
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            min-height: 140px;
        }

        .help-banner .banner-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .help-banner .banner-text {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            min-width: 250px;
        }

        .help-banner .emoji {
            font-size: 48px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .help-banner {
                min-height: auto;
            }

            .help-banner .banner-content {
                flex-direction: column;
                text-align: center;
            }

            .help-banner .banner-text {
                flex-direction: column;
                text-align: center;
            }

            .help-banner .emoji {
                font-size: 36px;
            }

            .help-banner .btn-download {
                width: 100%;
            }

            /* Cards Mobile */
            .stat-card .card-body {
                padding: 1rem !important;
            }

            .stat-card .h5 {
                font-size: 1.25rem;
            }

            .stat-card i.fa-2x {
                font-size: 1.5rem !important;
            }
        }

        @media (max-width: 576px) {
            .help-banner h5 {
                font-size: 1rem;
            }

            .help-banner p {
                font-size: 0.85rem;
            }

            h1.h3 {
                font-size: 1.25rem;
            }
        }

        /* Card Hover Effect */
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Table Responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Help & Support Banner - Lazy Load -->
        <div class="row mb-4 lazy-section" data-lazy="banner">
            <div class="col-12">
                <div class="card bg-primary text-white help-banner">
                    <!-- Decorative Circles Background -->
                    <div
                        style="position: absolute; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -30px; right: 50px; z-index: 1;">
                    </div>
                    <div
                        style="position: absolute; width: 80px; height: 80px; background: rgba(255,255,255,0.08); border-radius: 50%; bottom: -20px; right: 20px; z-index: 1;">
                    </div>
                    <div
                        style="position: absolute; width: 60px; height: 60px; background: rgba(255,255,255,0.12); border-radius: 50%; top: 20px; left: -20px; z-index: 1;">
                    </div>

                    <div class="card-body p-3 p-md-4" style="position: relative; z-index: 2;">
                        <div class="banner-content">
                            <div class="banner-text">
                                <div class="emoji">👋</div>
                                <div>
                                    <h5 class="card-title mb-2 font-weight-bold">Butuh Bantuan Sistem?</h5>
                                    <p class="card-text mb-0">Kelola data sekolah kini lebih mudah. Unduh panduan lengkap
                                        penggunaan aplikasi di sini.</p>
                                </div>
                            </div>
                            <button class="btn btn-warning font-weight-bold btn-download"
                                style="padding: 12px 24px; border-radius: 8px; white-space: nowrap;"
                                onclick="openPanduanPDF()">
                                <i class="fas fa-download"></i> DOWNLOAD
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards - Lazy Load -->
        <div class="row lazy-section" data-lazy="stats">
            <!-- Siswa Card -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card border-left-primary h-100 py-2 stat-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Siswa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $siswaCount ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guru Card -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card border-left-success h-100 py-2 stat-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Guru</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $guruCount ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kelas Card -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card border-left-info h-100 py-2 stat-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Kelas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $kelasCount ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-school fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Berita Card -->
            <div class="col-6 col-lg-3 mb-4">
                <div class="card border-left-warning h-100 py-2 stat-card">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Total Berita</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $beritaCount ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-newspaper fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row - Lazy Load -->
        <div class="row lazy-section" data-lazy="content">
            <!-- Recent Berita -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Berita Terbaru</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT * FROM berita ORDER BY tanggal DESC LIMIT 5";
                        $result = mysqli_query($db, $query);
                        if (mysqli_num_rows($result) > 0):
                            while ($berita = mysqli_fetch_assoc($result)):
                                ?>
                                <div class="mb-3">
                                    <div class="small text-gray-500"><?= date('d/m/Y', strtotime($berita['tanggal'])) ?></div>
                                    <h6 class="mb-0"><?= htmlspecialchars($berita['judul']) ?></h6>
                                </div>
                                <?php
                            endwhile;
                        else:
                            ?>
                            <p class="text-center text-muted">Belum ada berita</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Absensi Hari Ini -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h6 class="m-0 font-weight-bold">Absensi Hari Ini</h6>
                            <span class="badge badge-light" style="font-size: 0.75rem;">
                                <?= date('d M Y') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php
                        $query = "SELECT k.kelas_id, k.nama as kelas, 
                                        COALESCE(SUM(CASE WHEN ad.status = 'Hadir' THEN 1 ELSE 0 END), 0) as hadir,
                                        COALESCE(SUM(CASE WHEN ad.status = 'Sakit' THEN 1 ELSE 0 END), 0) as sakit,
                                        COALESCE(SUM(CASE WHEN ad.status = 'Izin' THEN 1 ELSE 0 END), 0) as izin,
                                        COALESCE(SUM(CASE WHEN ad.status = 'Alpha' THEN 1 ELSE 0 END), 0) as alpha
                                 FROM kelas k
                                 LEFT JOIN absensi_detail ad ON k.kelas_id = ad.kelas_id AND ad.tanggal = CURDATE()
                                 GROUP BY k.kelas_id, k.nama
                                 ORDER BY k.nama ASC";
                        $result = mysqli_query($db, $query);

                        if (mysqli_num_rows($result) > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 table-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-top-0">Kelas</th>
                                            <th class="border-top-0 text-center" style="color: #28a745;" title="Hadir">H</th>
                                            <th class="border-top-0 text-center" style="color: #ffc107;" title="Sakit">S</th>
                                            <th class="border-top-0 text-center" style="color: #17a2b8;" title="Izin">I</th>
                                            <th class="border-top-0 text-center" style="color: #dc3545;" title="Alpha">A</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="text-truncate" style="max-width: 120px;">
                                                    <?= htmlspecialchars($row['kelas']) ?>
                                                </td>
                                                <td class="text-center" style="color: #28a745;">
                                                    <strong><?= $row['hadir'] ?></strong>
                                                </td>
                                                <td class="text-center" style="color: #ffc107;">
                                                    <strong><?= $row['sakit'] ?></strong>
                                                </td>
                                                <td class="text-center" style="color: #17a2b8;"><strong><?= $row['izin'] ?></strong>
                                                </td>
                                                <td class="text-center" style="color: #dc3545;">
                                                    <strong><?= $row['alpha'] ?></strong>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center p-3 text-muted">Belum ada data absensi hari ini</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lazy Loading Script -->
    <script>
        function openPanduanPDF() {
            const pdfPath = '/public_html/MANUAL,BOOK.pdf';
            const pdfUrl = window.location.origin + pdfPath;
            window.open(encodeURI(pdfUrl), '_blank');
        }

        // Lazy Loading dengan Intersection Observer
        document.addEventListener('DOMContentLoaded', function () {
            const lazyObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('loaded');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                rootMargin: '50px',
                threshold: 0.1
            });

            // Observe semua lazy sections
            document.querySelectorAll('.lazy-section').forEach(section => {
                lazyObserver.observe(section);
            });

            // Fallback untuk browser lama
            if (!('IntersectionObserver' in window)) {
                document.querySelectorAll('.lazy-section').forEach(section => {
                    section.classList.add('loaded');
                });
            }
        });
    </script>

    <?php
else:
    ?>
    <div class="alert alert-warning">
        <h4>Database Setup Required</h4>
        <p>Please import the database structure first. The following tables are required:</p>
        <ul>
            <?php foreach ($requiredTables as $table): ?>
                <li><?= $table ?></li>
            <?php endforeach; ?>
        </ul>
        <p>You can import the database structure using the provided SQL file.</p>
    </div>
    <?php
endif;
?>