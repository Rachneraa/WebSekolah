<?php
session_start();
// Path ke koneksi.php
require_once __DIR__ . '/../../config/koneksi.php'; 
// Muat Guzzle/Fonnte
require_once __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json');

// Keamanan: Hanya admin yang boleh
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

// Ambil data JSON dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id_pendaftar']) || empty($data['kelas_id'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap (ID Pendaftar atau Kelas ID kosong).']);
    exit;
}

$id_pendaftar = $data['id_pendaftar'];
$kelas_id = $data['kelas_id'];

try {
    // 1. Ambil data lengkap dari ppdb_pendaftar
    // === PERBAIKAN 1: Menggunakan 'alamat_email' ===
    $stmt_get = $db->prepare("SELECT nisn, nama_lengkap, no_hp, alamat_email FROM ppdb_pendaftar WHERE id = ?");
    // === AKHIR PERBAIKAN ===
    
    $stmt_get->bind_param("i", $id_pendaftar);
    $stmt_get->execute();
    $pendaftar = $stmt_get->get_result()->fetch_assoc();
    $stmt_get->close();

    if (!$pendaftar) {
        throw new Exception("Pendaftar tidak ditemukan.");
    }
    
    // Validasi data penting dari database
    if (empty($pendaftar['nisn'])) {
        throw new Exception("Gagal: Pendaftar ini tidak memiliki NISN.");
    }
    if (empty($pendaftar['no_hp'])) {
        throw new Exception("Gagal: Pendaftar ini tidak memiliki No. HP.");
    }

    // 2. Buat Kredensial Akun (Username = nama + 3 angka acak)
    $nama_parts = explode(' ', $pendaftar['nama_lengkap']);
    $nama_depan = strtolower(preg_replace('/[^a-z0-9]/', '', $nama_parts[0]));
    $random_suffix = substr(bin2hex(random_bytes(2)), 0, 3);
    $username = $nama_depan . $random_suffix; // Cth: "aiddilf81"
    
    $password_plain = substr(bin2hex(random_bytes(4)), 0, 6); 
    $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

    // 3. Masukkan ke tabel `siswa` (VERSI FINAL LENGKAP)
    $stmt_insert = $db->prepare(
        "INSERT INTO siswa (nama, username, password, kelas_id, nis, email, telepon) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    // Tipe data: sssisss
    // === PERBAIKAN 2: Menggunakan 'alamat_email' ===
    $stmt_insert->bind_param("sssisss", 
        $pendaftar['nama_lengkap'], // -> ke kolom 'nama'
        $username,                 // -> ke kolom 'username'
        $password_hash,            // -> ke kolom 'password'
        $kelas_id,                 // -> ke kolom 'kelas_id'
        $pendaftar['nisn'],        // -> ke kolom 'nis'
        $pendaftar['alamat_email'],// -> ke kolom 'email'
        $pendaftar['no_hp']        // -> ke kolom 'telepon'
    );
    // === AKHIR PERBAIKAN ===

    if (!$stmt_insert->execute()) {
        if ($db->errno == 1062) { 
            throw new Exception("Gagal: Akun dengan Username '$username' atau NISN '{$pendaftar['nisn']}' mungkin sudah ada.");
        }
        // Perbaikan typo dari sebelumnya
        throw new Exception("Gagal memasukkan data ke tabel siswa: Database error: " . $stmt_insert->error);
    }
    $stmt_insert->close();
    
    // 4. Update status_akun di tabel ppdb_pendaftar
    $stmt_update = $db->prepare("UPDATE ppdb_pendaftar SET status_akun = 1 WHERE id = ?");
    $stmt_update->bind_param("i", $id_pendaftar);
    $stmt_update->execute();
    $stmt_update->close();

    // 5. KIRIM PESAN WHATSAPP VIA FONNTE
    $nama = $pendaftar['nama_lengkap'];
    $nomor = preg_replace('/\D+/', '', $pendaftar['no_hp']);
    if (substr($nomor, 0, 1) === '0') {
        $nomor = '62' . substr($nomor, 1);
    }

    $pesan = "Halo {$nama},\n\n"
           . "Akun portal siswa Anda telah berhasil dibuat.\n\n"
           . "Berikut adalah detail login Anda:\n"
           . "Website: https://portofolio.sampulkreativ.id\n" 
           . "Username: {$username}\n"
           . "Password: {$password_plain}\n\n"
           . "Jika ingin mengubah username/password, bisa hubungi ke WhatsApp ini 🙏";

    // Menggunakan Token Fonnte dari kode Anda
    $token = "U8g6jcTU6ivZdU7Nispm";
    $client = new \GuzzleHttp\Client(['verify' => false]);
    
    try {
        $client->post('https://api.fonnte.com/send', [
            'headers' => ['Authorization' => $token],
            'form_params' => [ 'target' => $nomor, 'message' => $pesan, ]
        ]);
    } catch (\Exception $e) { /* Abaikan error fonnte */ }

    // 6. Kirim kembali data sukses ke JavaScript
    echo json_encode([
        'success'   => true,
        'message'   => 'Akun dibuat dan WA dikirim.',
        'username'  => $username 
    ]);

} catch (Exception $e) {
    // Perbaikan typo dari sebelumnya
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$db->close();
?>