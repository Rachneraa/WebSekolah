<?php
/**
 * Halaman Admin untuk Mengelola Pesan Masuk (kontak_pesan)
 * Lokasi: backend/modules/kontak.php
 *
 * Halaman ini dipanggil oleh admin.php
 */

// Cek hak akses (double check)
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo "<div class='container-fluid mt-4'><div class='alert alert-danger'>Akses ditolak.</div></div>";
    exit(); 
}

// Ambil filter status dari URL, default-nya 'Baru' agar fokus
$filter_status = $_GET['status'] ?? 'Baru'; // Default ke 'Baru'

// --- LOGIKA AKSI (UPDATE STATUS & HAPUS) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $id = (int) $_POST['id'];
    $redirect_url = "admin.php?page=kontak&status=" . urlencode($filter_status);

    try {
        switch ($_POST['action']) {
            case 'hapus':
                // Hapus gambar (jika ada - sepertinya tidak ada di tabel ini)
                // ... (jika ada, tambahkan logika hapus file di sini) ...

                // Hapus data dari database
                $stmt = $db->prepare("DELETE FROM kontak_pesan WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $_SESSION['success'] = "Pesan berhasil dihapus.";
                break;

            case 'tandai_dibaca':
                $stmt = $db->prepare("UPDATE kontak_pesan SET status = 'Dibaca' WHERE id = ? AND status = 'Baru'");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                // Tidak perlu notifikasi, karena pesannya akan pindah filter
                break;

            case 'tandai_dibalas':
                $stmt = $db->prepare("UPDATE kontak_pesan SET status = 'Dibalas' WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                // Tidak perlu notifikasi, karena pesannya akan pindah filter
                break;
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
    
    header("Location: $redirect_url");
    exit();
}


// --- AMBIL DATA PESAN (DENGAN FILTER) ---

$params = [];
$types = '';
$where_sql = "";

if ($filter_status != 'Semua') {
    $where_sql = "WHERE status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

$query = "
    SELECT * FROM kontak_pesan 
    $where_sql
    ORDER BY 
        CASE status 
            WHEN 'Baru' THEN 1
            WHEN 'Dibaca' THEN 2
            WHEN 'Dibalas' THEN 3
        END, 
        tanggal DESC
";

$stmt = $db->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$pesan_result = $stmt->get_result();
$pesan_list = $pesan_result->fetch_all(MYSQLI_ASSOC);

// Set default timezone
date_default_timezone_set('Asia/Jakarta');
?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>


<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pesan Masuk</h1>
</div>

<p>Daftar pesan yang dikirim melalui formulir kontak website.</p>

<div class="card mb-3">
    <div class="card-body">
        <label class="form-label" style="font-weight: 500;">Filter Status Pesan:</label>
        <div class="d-flex flex-wrap gap-2">
            <?php
            $status_list = ['Semua', 'Baru', 'Dibaca', 'Dibalas'];
            foreach ($status_list as $status):
                $is_active = ($filter_status == $status);
            ?>
                <a href="admin.php?page=kontak&status=<?= urlencode($status) ?>" 
                   class="btn btn-sm <?= $is_active ? 'btn-primary' : 'btn-outline-primary' ?>">
                   <?= $status ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-white">Data Pesan</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pengirim</th>
                        <th>Kontak</th>
                        <th>Subjek</th>
                        <th>Status</th>
                        <th style="width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pesan_list) == 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                Tidak ada pesan masuk <?= ($filter_status != 'Semua') ? "dengan status '$filter_status'" : "" ?>.
                            </td>
                        </tr>
                    <?php else:
                        foreach ($pesan_list as $row): 
                        ?>
                        <tr>
                            <td><?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td>
                                <small>
                                    <i class="fas fa-envelope fa-fw"></i> <?= htmlspecialchars($row['email']) ?><br>
                                    <i class="fas fa-phone fa-fw"></i> <?= htmlspecialchars($row['telepon'] ? $row['telepon'] : '-') ?>
                                </small>
                            </td>
                            <td><?= htmlspecialchars($row['subjek']) ?></td>
                            <td>
                                <?php 
                                $status = $row['status'];
                                $badge_class = 'bg-secondary'; // Default
                                if ($status == 'Baru') $badge_class = 'bg-success';
                                if ($status == 'Dibaca') $badge_class = 'bg-info text-dark';
                                if ($status == 'Dibalas') $badge_class = 'bg-primary';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPesan<?= $row['id'] ?>" title="Lihat Detail Pesan">
                                    <i class="fas fa-eye"></i> Lihat
                                </button>
                                
                                <?php if ($row['status'] == 'Baru'): ?>
                                    <button onclick="submitKontakAction('tandai_dibaca', <?= $row['id'] ?>)" class="btn btn-sm btn-success" title="Tandai sudah dibaca">
                                        <i class="fas fa-check"></i>
                                    </button>
                                <?php elseif ($row['status'] == 'Dibaca'): ?>
                                    <button onclick="submitKontakAction('tandai_dibalas', <?= $row['id'] ?>)" class="btn btn-sm btn-info" title="Tandai sudah dibalas">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <button onclick="hapusPesan(<?= $row['id'] ?>)" class="btn btn-sm btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalPesan<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel<?= $row['id'] ?>">Pesan dari: <?= htmlspecialchars($row['nama']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Subjek:</strong> <?= htmlspecialchars($row['subjek']) ?></p>
                                        <p><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></p>
                                        <p><strong>Telepon:</strong> <a href="tel:<?= htmlspecialchars($row['telepon']) ?>"><?= htmlspecialchars($row['telepon'] ? $row['telepon'] : '-') ?></a></p>
                                        <p><strong>Tanggal:</strong> <?= date('d M Y, H:i', strtotime($row['tanggal'])) ?> WIB</p>
                                        <hr>
                                        <p><strong>Isi Pesan:</strong></p>
                                        <div style="white-space: pre-wrap; background: #f4f4f4; padding: 15px; border-radius: 5px;">
                                            <?= htmlspecialchars($row['pesan']) ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <?php if ($row['status'] == 'Baru'): ?>
                                            <button type="button" onclick="submitKontakAction('tandai_dibaca', <?= $row['id'] ?>)" class="btn btn-success" data-bs-dismiss="modal">
                                                Tandai Sudah Dibaca
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function submitKontakAction(action, id) {
        const form = document.createElement('form');
        form.method = 'POST';
        // Arahkan ke halaman saat ini, PHP akan menangani action-nya
        form.action = 'admin.php?page=kontak&status=<?= urlencode($filter_status) ?>'; 
        
        form.innerHTML = `
            <input type="hidden" name="action" value="${action}">
            <input type="hidden" name="id" value="${id}">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }

    function hapusPesan(id) {
        if (confirm('Anda yakin ingin menghapus pesan ini secara permanen?')) {
            submitKontakAction('hapus', id);
        }
    }
</script>