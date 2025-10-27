<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

$wa_response = null; // Tambahkan ini
$wa_error = null;    // Tambahkan ini
$wa_sent = false;    // Tambahkan ini

if ($id && in_array($status, ['proses', 'diterima', 'ditolak'])) {
    $stmt = $db->prepare("UPDATE ppdb_pendaftar SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $success = $stmt->execute();

    if ($success && $status === 'diterima') {
        $sel = $db->prepare("SELECT nama_lengkap, no_hp FROM ppdb_pendaftar WHERE id = ?");
        $sel->bind_param('i', $id);
        $sel->execute();
        $result = $sel->get_result();

        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $nama = $data['nama_lengkap'];
            $nomor = preg_replace('/\D+/', '', $data['no_hp']);
            if (substr($nomor, 0, 1) === '0') {
                $nomor = '62' . substr($nomor, 1);
            }

            if (!empty($nomor)) {
                $pesan = "🎉 Selamat {$nama}!\n\n"
                    . "Anda telah DITERIMA di PPDB SMK TI Garuda Nusantara Cimahi.\n\n"
                    . "Silakan datang ke sekolah untuk melakukan daftar ulang.\n\n"
                    . "Terima kasih 🙏";

                $token = "1rp3kEp5uTdMTodqHkc3";
                $client = new \GuzzleHttp\Client(['verify' => false]);
                try {
                    $response = $client->post('https://api.fonnte.com/send', [
                        'headers' => ['Authorization' => $token],
                        'form_params' => [
                            'target' => $nomor,
                            'message' => $pesan,
                        ]
                    ]);
                    $wa_sent = true;
                    $wa_response = $response->getBody()->getContents();
                } catch (\Exception $e) {
                    $wa_error = $e->getMessage();
                    $wa_response = null;
                }
            }
        }
        $sel->close();
    }

    echo json_encode([
        'success' => $success,
        'wa_sent' => $wa_sent,
        'wa_error' => $wa_error,
        'wa_response' => $wa_response
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid input'
    ]);
}
?>