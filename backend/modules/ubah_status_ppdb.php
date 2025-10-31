<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

$wa_response = null;
$wa_error = null;
$wa_sent = false;

if ($id && in_array($status, ['proses', 'diterima', 'ditolak'])) {
    $stmt = $db->prepare("UPDATE ppdb_pendaftar SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $success = $stmt->execute();

    // --- UBAH BAGIAN INI ---
    // Sekarang, logika ini berjalan jika statusnya 'diterima' ATAU 'ditolak'
    if ($success && ($status === 'diterima' || $status === 'ditolak')) {
    // --- AKHIR PERUBAHAN ---

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
                
                // --- TAMBAHKAN LOGIKA INI ---
                $pesan = ""; // Inisialisasi variabel pesan
                
                if ($status === 'diterima') {
    
    // --- Bagian Baru: Daftar Syarat ---
    // Kamu bisa ubah daftar ini sesuai kebutuhan proyekmu.
    $syarat_daftar_ulang = "Syarat Daftar Ulang (harap dibawa):\n"
                         . "1. Bukti Pendaftaran Online (Cetak)\n"
                         . "2. Fotokopi SKL/Ijazah (2 lembar)\n"
                         . "3. Fotokopi Akta Kelahiran (2 lembar)\n"
         . "4. Fotokopi Kartu Keluarga (2 lembar)\n"
                         . "5. Pas Foto 3x4 (3 lembar)\n"
                         . "6. Materai 10.000 (1 buah)\n"
                         . "7. Masukkan semua berkas ke dalam Map Biru.";

    // --- Pesan yang Dimodifikasi ---
    $pesan = "🎉 Selamat {$nama}!\n\n"
           . "Anda telah DITERIMA di PPDB SMK TI Garuda Nusantara Cimahi.\n\n"
           . "Silakan datang ke sekolah untuk melakukan daftar ulang.\n\n"
           . "{$syarat_daftar_ulang}\n\n"  // <-- Syaratnya dimasukkan di sini
           . "Terima kasih 🙏";

} elseif ($status === 'ditolak') {
    $pesan = "Mohon maaf, {$nama}.\n\n"
           . "Berdasarkan hasil seleksi, Anda dinyatakan TIDAK DITERIMA di PPDB SMK TI Garuda Nusantara Cimahi.\n\n"
           . "Tetap semangat dan jangan berkecil hati.\n\n"
           . "Terima kasih atas partisipasinya 🙏";
}
                // --- AKHIR TAMBAHAN ---

                $token = "BCNDtEC7orC9pDPDinKH";
                $client = new \GuzzleHttp\Client(['verify' => false]);
                try {
                    $response = $client->post('https://api.fonnte.com/send', [
                        'headers' => ['Authorization' => $token],
                        'form_params' => [
                            'target' => $nomor,
                            'message' => $pesan, // Pesan akan dikirim sesuai status
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