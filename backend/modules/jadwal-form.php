<?php
// Cek login & hak akses
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['level'], ['admin', 'guru'])) {
    header('Location: ../index.php');
    exit();
}

// --- Logika untuk Form Dimulai ---
$is_edit = false;
$edit_data = [
    'id_jadwal' => '',
    'kelas_id' => '',
    'hari' => 'Senin',
    'jam' => '',
    'mapel_id' => '',
    'guru_id' => ''
];
$card_title = "Tambah Jadwal Baru";

// 1. Cek apakah ini mode EDIT?
if (isset($_GET['id'])) {
    $is_edit = true;
    $id_jadwal = (int)$_GET['id'];
    
    $stmt = $db->prepare("SELECT * FROM jadwal WHERE id_jadwal = ?");
    $stmt->bind_param("i", $id_jadwal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $card_title = "Edit Jadwal";
    } else {
        $_SESSION['error'] = "Data jadwal tidak ditemukan.";
        echo "<script>window.location.href = '?page=jadwal';</script>";
        exit();
    }
}

// 2. Ambil data untuk dropdowns
$kelas_result = $db->query("SELECT kelas_id, nama FROM kelas ORDER BY nama ASC");
$guru_result = $db->query("SELECT id, nama FROM guru ORDER BY nama ASC");
$mapel_result = $db->query("SELECT id, nama FROM mapel ORDER BY nama ASC");
$hari_list = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $card_title ?></h3>
                </div>
                
                <form action="?page=jadwal" method="post">
                    <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'tambah' ?>">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id_jadwal" value="<?= $edit_data['id_jadwal'] ?>">
                    <?php endif; ?>

                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <select name="kelas_id" class="form-select" required>
                                    <option value="">Pilih Kelas</option>
                                    <?php while ($k = $kelas_result->fetch_assoc()): ?>
                                        <option value="<?= $k['kelas_id'] ?>" <?= ($edit_data['kelas_id'] == $k['kelas_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($k['nama']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mata Pelajaran</label>
                                <select name="mapel_id" class="form-select" required>
                                    <option value="">Pilih Mata Pelajaran</option>
                                    <?php while ($m = $mapel_result->fetch_assoc()): ?>
                                        <option value="<?= $m['id'] ?>" <?= ($edit_data['mapel_id'] == $m['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['nama']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hari</label>
                                <select name="hari" class="form-select" required>
                                    <?php foreach ($hari_list as $h): ?>
                                        <option value="<?= $h ?>" <?= ($edit_data['hari'] == $h) ? 'selected' : '' ?>>
                                            <?= $h ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam</label>
                                <input type="text" name="jam" class="form-control" placeholder="Contoh: 07:00-08:00"
                                       value="<?= htmlspecialchars($edit_data['jam']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Guru</label>
                                <select name="guru_id" class="form-select" required>
                                    <option value="">Pilih Guru</option>
                                    <?php while ($g = $guru_result->fetch_assoc()): ?>
                                        <option value="<?= $g['id'] ?>" <?= ($edit_data['guru_id'] == $g['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($g['nama']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end">
                        <a href="?page=jadwal" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Jadwal' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>