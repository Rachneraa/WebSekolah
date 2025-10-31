<?php
// Tidak perlu session_start atau require 'koneksi.php'
// File induk (admin.php) sudah menyediakannya.
// Variabel $db sudah ada.

// --- Logika untuk Form Dimulai ---

$is_edit = false;
$guru_data = [
    'id' => '',
    'nip' => '',
    'nama' => '',
    'jenis_kelamin' => 'L',
    'alamat' => '',
    'no_telp' => '',
    'foto' => '',
    'mapel_id' => ''
];
$card_title = "Tambah Guru Baru";
$form_action = "add"; // Default adalah 'add'

// 1. Cek apakah ini mode EDIT?
if (isset($_GET['id'])) {
    $is_edit = true;
    $guru_id = (int)$_GET['id'];
    
    // Ambil data guru yang ada
    $stmt = $db->prepare("SELECT * FROM guru WHERE id = ?");
    $stmt->bind_param("i", $guru_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $guru_data = $result->fetch_assoc();
        $card_title = "Edit Data Guru: " . htmlspecialchars($guru_data['nama']);
        $form_action = "edit";
    } else {
        $_SESSION['error'] = "Data guru tidak ditemukan.";
        echo "<script>window.location.href = '?page=guru';</script>";
        exit();
    }
}

// 2. Ambil daftar mapel untuk dropdown
try {
    $mapel_result_data = $db->query("SELECT id, nama FROM mapel ORDER BY nama");
    $mapel_options = $mapel_result_data->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Gagal memuat data mapel: " . $e->getMessage();
    $mapel_options = [];
}
// --- Akhir Logika ---
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $card_title ?></h3>
                </div>
                
                <form action="?page=guru" method="post" enctype="multipart/form-data">
                    
                    <input type="hidden" name="action" value="<?= $form_action ?>">
                    
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($guru_data['id']) ?>">
                        <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($guru_data['foto']) ?>">
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">NIP (akan jadi username)</label>
                            <input type="text" name="nip" class="form-control" 
                                   value="<?= htmlspecialchars($guru_data['nip']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" 
                                   value="<?= htmlspecialchars($guru_data['nama']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" <?= ($guru_data['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= ($guru_data['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran</label>
                            <select name="mapel_id" class="form-select" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php
                                foreach ($mapel_options as $mapel) {
                                    $selected = ($mapel['id'] == $guru_data['mapel_id']) ? 'selected' : '';
                                    echo "<option value='" . $mapel['id'] . "' $selected>" . htmlspecialchars($mapel['nama']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($guru_data['alamat']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" name="no_telp" class="form-control" 
                                   value="<?= htmlspecialchars($guru_data['no_telp']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="<?= $is_edit ? '(Kosongkan jika tidak diubah)' : 'Wajib diisi untuk guru baru' ?>"
                                   <?= !$is_edit ? 'required' : '' ?>>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= $is_edit ? 'Ganti Foto' : 'Foto' ?></label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                            <?php if ($is_edit && !empty($guru_data['foto'])): ?>
                                <small class="form-text">
                                    Foto saat ini: <?= htmlspecialchars($guru_data['foto']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end">
                        <a href="?page=guru" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Guru' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>