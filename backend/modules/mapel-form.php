<?php
// Cek login & hak akses (hanya admin)
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    header('Location: ../index.php');
    exit();
}

// --- Logika untuk Form Dimulai ---
$is_edit = false;
$edit_data = [
    'id' => '',
    'nama' => ''
];
$card_title = "Tambah Mapel Baru";
$form_action = "tambah";

// 1. Cek apakah ini mode EDIT?
if (isset($_GET['id'])) {
    $is_edit = true;
    $id = (int)$_GET['id'];
    
    // Ambil data mapel dengan PREPARED STATEMENT
    $stmt = $db->prepare("SELECT * FROM mapel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $card_title = "Edit Mapel: " . htmlspecialchars($edit_data['nama']);
        $form_action = "edit";
    } else {
        $_SESSION['error'] = "Data mapel tidak ditemukan.";
        echo "<script>window.location.href = '?page=mapel';</script>";
        exit();
    }
}
// --- Akhir Logika ---
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12"> <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $card_title ?></h3>
                </div>
                
                <form action="?page=mapel" method="post">
                    <input type="hidden" name="action" value="<?= $form_action ?>">
                    <?php if ($is_edit): ?>
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                    <?php endif; ?>

                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="nama" class="form-control" 
                                   value="<?= htmlspecialchars($edit_data['nama']) ?>" 
                                   placeholder="Contoh: Matematika" required>
                        </div>
                    </div>
                    
                    <div class="card-footer text-end">
                        <a href="?page=mapel" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> 
                            <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Mapel' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>