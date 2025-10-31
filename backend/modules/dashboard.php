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

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <div class="row">
            <!-- Siswa Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary h-100 py-2">
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success h-100 py-2">
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info h-100 py-2">
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
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning h-100 py-2">
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

        <!-- Content Row -->
        <div class="row">
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
                                    <h5><?= htmlspecialchars($berita['judul']) ?></h5>
                                </div>
                                <?php
                            endwhile;
                        else:
                            ?>
                            <p class="text-center">Belum ada berita</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Absensi -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold">Absensi Hari Ini</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT k.nama as kelas, a.hadir, a.sakit, a.izin, a.alpha 
     FROM absensi a 
     JOIN kelas k ON a.kelas_id = k.kelas_id 
     WHERE DATE(a.created_at) = CURDATE()";
                        $result = mysqli_query($db, $query);
                        if (mysqli_num_rows($result) > 0):
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Kelas</th>
                                            <th>Hadir</th>
                                            <th>Sakit</th>
                                            <th>Izin</th>
                                            <th>Alpha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['kelas']) ?></td>
                                                <td><?= $row['hadir'] ?></td>
                                                <td><?= $row['sakit'] ?></td>
                                                <td><?= $row['izin'] ?></td>
                                                <td><?= $row['alpha'] ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center">Belum ada data absensi hari ini</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
else:
    // Show table creation instructions
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