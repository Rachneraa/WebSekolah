<?php
ob_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

require_once BASE_PATH . '/config/koneksi.php';

// Ambil filter kelas dari GET
$kelas_filter = isset($_GET['kelas']) ? intval($_GET['kelas']) : 0;

// Ambil nama kelas
$kelas_nama = '';
if ($kelas_filter) {
    $kelas_stmt = $db->prepare("SELECT nama FROM kelas WHERE id = ?");
    $kelas_stmt->bind_param("i", $kelas_filter);
    $kelas_stmt->execute();
    $kelas_stmt->bind_result($kelas_nama);
    $kelas_stmt->fetch();
    $kelas_stmt->close();
}

// Ambil daftar siswa di kelas
$siswa_stmt = $db->prepare("SELECT siswa_id, nama FROM siswa WHERE kelas_id = ?");
$siswa_stmt->bind_param("i", $kelas_filter);
$siswa_stmt->execute();
$siswa_result = $siswa_stmt->get_result();

$tanggal = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Sidebar & content style, copy from admin.php */
    body {
        background-color: #f5f8ff;
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
        padding: 10px;
    }


    /* Batasi lebar card absensi agar selalu proporsional di desktop dan mobile */
    .card.shadow {
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
    }

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


        #sidebarCollapse {
            display: inline-block;
        }
    }

    @media (min-width: 769px) {
        #sidebarCollapse {
            display: none;
        }
    }

    #sidebarCollapse {
        background: #1565c0;
        border: none;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
        margin-right: 15px;
    }

    #sidebarCollapse:hover {
        background: #1976d2;
    }

    @media (max-width: 576px) {
        .modal-dialog {
            max-width: 95vw;
            margin: 0.5rem auto;
        }

        .modal-content {
            padding: 10px;
        }

        .modal-header,
        .modal-footer {
            padding: 10px 12px;
        }

        .modal-title {
            font-size: 1rem;
        }

        .form-control,
        textarea {
            font-size: 13px;
            padding: 0.3rem 0.6rem;
        }
    }

</style>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>Admin Panel</h3>
        </div>
        <ul class="list-unstyled components">
            <li><a href="../admin.php?page=dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="../admin.php?page=siswa"><i class="fas fa-user-graduate"></i> Data Siswa</a></li>
            <li><a href="../admin.php?page=guru"><i class="fas fa-chalkboard-teacher"></i> Data Guru</a></li>
            <li class="active"><a href="../admin.php?page=kelas"><i class="fas fa-school"></i> Data Kelas dan
                    Absensi</a></li>
            <li><a href="../admin.php?page=berita"><i class="fas fa-newspaper"></i> Berita</a></li>
            <li><a href="../admin.php?page=ppdb_admin"><i class="fas fa-user-plus"></i> Pendaftaran Siswa Baru</a></li>
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
                    <a href="../../config/logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </nav>
        <!-- Absensi Card & Modal -->
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Absensi Kelas <?= htmlspecialchars($kelas_nama) ?></h4>
                            <small><?= date('d M Y', strtotime($tanggal)) ?></small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <form method="get" class="row g-2 align-items-center">
                                    <input type="hidden" name="kelas" value="<?= $kelas_filter ?>">
                                    <div class="col-auto">
                                        <label for="filterTanggal" class="form-label mb-0">Tanggal Absensi:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" id="filterTanggal" name="date" class="form-control"
                                            value="<?= htmlspecialchars($tanggal) ?>">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Tampilkan
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Status</th>
                                            <th>Waktu Absen</th>
                                            <th>Alasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = $siswa_result->fetch_assoc()) {
                                            // Cek absensi
                                            $absen_stmt = $db->prepare("SELECT waktu_absen, status FROM absensi_detail WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?");
                                            $absen_stmt->bind_param("iis", $row['siswa_id'], $kelas_filter, $tanggal);
                                            $absen_stmt->execute();
                                            $absen_result = $absen_stmt->get_result();
                                            $absen = $absen_result->fetch_assoc();

                                            // Cek alasan di absensi_alasan
                                            $alasan_stmt = $db->prepare("SELECT alasan, status, waktu_alasan FROM absensi_alasan WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?");
                                            $alasan_stmt->bind_param("iis", $row['siswa_id'], $kelas_filter, $tanggal);
                                            $alasan_stmt->execute();
                                            $alasan_result = $alasan_stmt->get_result();
                                            $alasan_row = $alasan_result->fetch_assoc();

                                            $status_text = $alasan_row && !empty($alasan_row['status']) ? $alasan_row['status'] : ($absen ? $absen['status'] : 'Tidak Hadir');
                                            // Gunakan waktu dari absensi_alasan jika ada, jika tidak dari absensi_detail
                                            if ($alasan_row && !empty($alasan_row['waktu_alasan'])) {
                                                $waktu = date('H:i', strtotime($alasan_row['waktu_alasan']));
                                            } elseif ($absen && !empty($absen['waktu_absen'])) {
                                                $waktu = date('H:i', strtotime($absen['waktu_absen']));
                                            } else {
                                                $waktu = '-';
                                            }
                                            $alasan = $alasan_row && !empty($alasan_row['alasan']) ? htmlspecialchars($alasan_row['alasan']) : '-';
                                            $alasan_status = $alasan_row && !empty($alasan_row['status']) ? $alasan_row['status'] : $status_text;

                                            // Tampilkan tombol alasan jika status bukan Hadir
                                            $alasan_btn = '';
                                            if ($status_text != 'Hadir') {
                                                $btn_label = ($alasan == '-' ? 'Isi Alasan' : 'Edit Alasan');
                                                $alasan_btn = '<button class="btn btn-sm btn-warning" 
                                                    onclick="showAlasanModal(' . $row['siswa_id'] . ', \'' . addslashes($row['nama']) . '\', \'' . addslashes($alasan_status) . '\', \'' . addslashes($alasan) . '\')">' . $btn_label . '</button>';
                                            }

                                            echo "<tr>
                                                <td>{$no}</td>
                                                <td>" . htmlspecialchars($row['nama']) . "</td>
                                                <td><span class='badge " . ($status_text == 'Hadir' ? 'bg-success' : 'bg-danger') . "'>$status_text</span></td>
                                                <td>$waktu</td>
                                                <td>
                                                    $alasan
                                                    " . ($alasan_btn ? '<br>' . $alasan_btn : '') . "
                                                </td>
                                            </tr>";
                                            $no++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="../../backend/admin.php?page=kelas" class="btn btn-secondary mt-3"><i
                                    class="fas fa-arrow-left"></i> Kembali ke
                                Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="alasanModal" tabindex="-1">
            <div class="modal-dialog">
                <form class="modal-content" id="formAlasan">
                    <div class="modal-header">
                        <h5 class="modal-title">Isi/Edit Alasan Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="siswa_id" id="siswa_id">
                        <input type="hidden" name="kelas_id" id="kelas_id" value="<?= $kelas_filter ?>">
                        <input type="hidden" name="tanggal" id="tanggal" value="<?= $tanggal ?>">
                        <div class="mb-2">
                            <label>Nama Siswa</label>
                            <input type="text" class="form-control" id="nama_siswa" readonly>
                        </div>
                        <div class="mb-2">
                            <label>Status</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusIzin" value="Izin">
                                <label class="form-check-label" for="statusIzin">Izin</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusSakit"
                                    value="Sakit">
                                <label class="form-check-label" for="statusSakit">Sakit</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="statusAlpha"
                                    value="Alpha">
                                <label class="form-check-label" for="statusAlpha">Alpha</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label>Alasan</label>
                            <textarea class="form-control" name="alasan" id="alasan" rows="2" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function showAlasanModal(siswa_id, nama, status, alasan) {
                document.getElementById('siswa_id').value = siswa_id;
                document.getElementById('nama_siswa').value = nama;
                document.getElementById('alasan').value = alasan !== '-' ? alasan : '';
                // Set radio status
                document.getElementById('statusIzin').checked = (status === 'Izin');
                document.getElementById('statusSakit').checked = (status === 'Sakit');
                document.getElementById('statusAlpha').checked = (status === 'Alpha');
                new bootstrap.Modal(document.getElementById('alasanModal')).show();
            }

            document.getElementById('formAlasan').onsubmit = function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                // buat endpoint berdasarkan path saat ini: /.../backend/modules/absensi.php -> /.../backend/modules/simpan_alasan.php
                const endpoint = window.location.pathname.replace(/\/[^\/]+$/, '/simpan_alasan.php');

                fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            // gunakan notifikasi sederhana; reload untuk menampilkan perubahan
                            alert('Alasan berhasil disimpan!');
                            location.reload();
                        } else {
                            alert('Gagal menyimpan alasan: ' + (res.message || 'unknown error'));
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        alert('Terjadi kesalahan saat mengirim data. Cek console dan network.');
                    });
            };
        </script>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>