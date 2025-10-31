<?php
// simpan_alasan.php
header('Content-Type: application/json');
ob_start();

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}
// Panggil koneksi (yang SEKARANG SUDAH ADA fungsi 'updateAbsensiSummary')
require_once BASE_PATH . '/config/koneksi.php';

date_default_timezone_set('Asia/Jakarta');
$response = ['success' => false, 'message' => ''];

try {
    // 1. Ambil data dari POST
    $siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
    $kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $alasan = isset($_POST['alasan']) ? $_POST['alasan'] : '';
    $waktu_alasan = date('Y-m-d H:i:s');

    if (empty($siswa_id) || empty($kelas_id) || empty($tanggal) || empty($status)) {
        throw new Exception('Data tidak lengkap.');
    }

    // 2. Simpan atau Update data ke tabel 'absensi_alasan'
    $stmt_alasan = $db->prepare("
        INSERT INTO absensi_alasan (siswa_id, kelas_id, tanggal, status, alasan, waktu_alasan)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            status = VALUES(status), 
            alasan = VALUES(alasan), 
            waktu_alasan = VALUES(waktu_alasan)
    ");

    if (!$stmt_alasan)
        throw new Exception("Prepare statement (absensi_alasan) gagal: " . $db->error);
    $stmt_alasan->bind_param("iissss", $siswa_id, $kelas_id, $tanggal, $status, $alasan, $waktu_alasan);

    if (!$stmt_alasan->execute()) {
        throw new Exception("Eksekusi (absensi_alasan) gagal: " . $stmt_alasan->error);
    }
    $stmt_alasan->close();

    // 3. PANGGIL FUNGSI UPDATE RINGKASAN
    if (updateAbsensiSummary($db, $kelas_id, $tanggal)) {
        $response['success'] = true;
        $response['message'] = 'Alasan berhasil disimpan dan ringkasan diperbarui.';
    } else {
        throw new Exception('Gagal memperbarui ringkasan absensi.');
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);
?>