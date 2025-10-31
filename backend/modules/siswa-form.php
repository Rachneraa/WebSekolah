<?php
// Tidak perlu ob_start(), session_start(), atau require_once koneksi.php
// File admin.php (induk) sudah menjalankannya.
// Variabel $db sudah tersedia dari file induk.

// --- Logika untuk Form Dimulai Langsung ---

// === Logika ini sudah benar dari sebelumnya (Biarkan saja) ===
// 1. Kita tentukan DULU semua field default
$data_default = [
    'siswa_id' => '',
    'nama' => '',
    'nis' => '',       // Kunci ini sekarang PASTI ada
    'email' => '',      // Kunci ini sekarang PASTI ada
    'telepon' => '',    // Kunci ini sekarang PASTI ada
    'username' => '',
    'kelas_id' => ''
];

// 2. Jadikan $siswa_data sebagai data default
$siswa_data = $data_default;
$is_edit = false;
$card_title = "Tambah Siswa Baru";
$form_action = "add"; // Default adalah 'add'

// 3. Cek apakah ini mode EDIT?
if (isset($_GET['id'])) {
    $is_edit = true;
    $siswa_id = (int)$_GET['id'];
    
    // Gunakan variabel $db yang sudah ada dari admin.php
    $stmt = $db->prepare("SELECT * FROM siswa WHERE siswa_id = ?");
    $stmt->bind_param("i", $siswa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data_dari_db = $result->fetch_assoc();
        
        // 4. GABUNGKAN data default dengan data dari DB
        $siswa_data = array_merge($data_default, $data_dari_db);
        
        $card_title = "Edit Data Siswa: " . htmlspecialchars($siswa_data['nama']);
        $form_action = "edit";
    } else {
        // Jika ID tidak ditemukan, arahkan kembali
        $_SESSION['error'] = "Data siswa tidak ditemukan.";
        echo "<script>window.location.href = '?page=siswa';</script>";
        exit();
    }
}
// === AKHIR DARI LOGIKA PHP ===

// 2. Ambil daftar kelas untuk dropdown (diperlukan di form)
$kelas_result = $db->query("SELECT * FROM kelas ORDER BY nama");
// --- Akhir Logika ---
?>

<div class="container-fluid">
    <div class="row"> 
        <div class="col-lg-12"> 
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $card_title ?></h3>
                </div>
                <form action="?page=siswa" method="post">
                    
                    <input type="hidden" name="action" value="<?= $form_action ?>">
                    
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($siswa_data['siswa_id']) ?>">
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" 
                                   value="<?= htmlspecialchars($siswa_data['nama']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nis" class="form-control" 
                                   value="<?= htmlspecialchars($siswa_data['nis'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($siswa_data['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="tel" name="telepon" class="form-control" 
                                   placeholder="Contoh: 08123456789"
                                   value="<?= htmlspecialchars($siswa_data['telepon'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($siswa_data['username']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" 
                                   placeholder="<?= $is_edit ? '(Kosongkan jika tidak diubah)' : '' ?>" 
                                   <?= !$is_edit ? 'required' : '' ?>>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                                <?php
                                if ($kelas_result && $kelas_result->num_rows > 0):
                                    $kelas_result->data_seek(0);
                                    while ($kelas = $kelas_result->fetch_assoc()):
                                        $selected = ($kelas['kelas_id'] == $siswa_data['kelas_id']) ? 'selected' : '';
                                ?>
                                        <option value="<?= $kelas['kelas_id'] ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($kelas['nama']) ?>
                                        </option>
                                <?php 
                                    endwhile;
                                endif; 
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="?page=siswa" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Siswa' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Tidak perlu ob_end_flush();
// File admin.php (induk) akan menanganinya.
?>