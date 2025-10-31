<?php
// Tidak perlu session_start atau require 'koneksi.php'
// File induk (admin.php) sudah menyediakannya.
// Variabel $db sudah ada.

// --- Logika untuk Form Dimulai ---

$is_edit = false;
$berita_data = [
    'id' => '',
    'judul' => '',
    'isi' => '',
    'tags' => '',
    'gambar' => ''
];
$card_title = "Tulis Berita Baru";
$form_action = "tambah"; // Default adalah 'tambah' (sesuai case Anda)

// 1. Cek apakah ini mode EDIT?
if (isset($_GET['id'])) {
    $is_edit = true;
    $berita_id = (int)$_GET['id'];
    
    // Ambil data berita yang ada
    $stmt = $db->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->bind_param("i", $berita_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $berita_data = $result->fetch_assoc();
        $card_title = "Edit Berita: " . htmlspecialchars($berita_data['judul']);
        $form_action = "edit"; // Ubah action ke 'edit'
    } else {
        $_SESSION['error'] = "Data berita tidak ditemukan.";
        echo "<script>window.location.href = '?page=berita';</script>";
        exit();
    }
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
                
                <form action="?page=berita" method="post" enctype="multipart/form-data">
                    
                    <input type="hidden" name="action" value="<?= $form_action ?>">
                    
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($berita_data['id']) ?>">
                        <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($berita_data['gambar']) ?>">
                    <?php endif; ?>

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" name="judul" 
                                   value="<?= htmlspecialchars($berita_data['judul']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Isi Berita</label>
                            <div class="text-muted small mb-2">
                                <strong>Panduan format:</strong><br>
                                1. Tulis paragraf pembuka<br>
                                2. Untuk sub judul, gunakan ## di awal baris<br>
                                3. Tulis konten sub judul di bawahnya<br>
                                <br>
                                Contoh:<br>
                                <pre class="bg-light p-2" style="border-radius: 4px;">
Ini adalah paragraf pembuka.
Bisa lebih dari satu baris.

## Sub Judul Pertama
Ini adalah isi dari sub judul pertama.

## Sub Judul Kedua
Ini adalah isi dari sub judul kedua.</pre>
                            </div>
                            <textarea class="form-control" name="isi" rows="15" required 
                                      style="font-family: monospace;"><?= htmlspecialchars($berita_data['isi']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-control" name="tags" 
                                   value="<?= htmlspecialchars($berita_data['tags']) ?>"
                                   placeholder="tag1, tag2, tag3">
                            <small class="text-muted">Pisahkan dengan koma</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= $is_edit ? 'Ganti Gambar (Opsional)' : 'Gambar (Opsional)' ?></label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
                            <small class="text-muted">
                                <?= $is_edit ? 'Biarkan kosong jika tidak ingin mengubah gambar' : '' ?>
                            </small>
                            
                            <?php if ($is_edit && !empty($berita_data['gambar'])): ?>
                                <div class="mt-2">
                                    <p class="small mb-1">Gambar saat ini:</p>
                                    <img src="../berita/<?= htmlspecialchars($berita_data['gambar']) ?>" 
                                         alt="Gambar saat ini" 
                                         style="max-height: 100px; border-radius: 4px; border: 1px solid #ddd;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end">
                        <a href="?page=berita" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?= $is_edit ? 'Simpan Perubahan' : 'Terbitkan Berita' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>