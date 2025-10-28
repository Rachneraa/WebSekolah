<?php
session_start();
include '../../config/koneksi.php'; // Ganti 'konek' menjadi 'koneksi'

// Periksa apakah metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil semua data dari form (tambahkan escape string)
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $jenis_kelamin = mysqli_real_escape_string($db, $_POST['jenis_kelamin']);
    $agama = mysqli_real_escape_string($db, $_POST['agama']);
    $tempat_lahir = mysqli_real_escape_string($db, $_POST['tempat_lahir']);
    // Gabungkan tanggal lahir
    $tanggal_lahir = $_POST['tahun'] . '-' . $_POST['bulan'] . '-' . $_POST['tanggal'];
    $nisn = mysqli_real_escape_string($db, $_POST['nisn']);
    $alamat_email = mysqli_real_escape_string($db, $_POST['alamat_email']); // Pastikan kolom ini ada
    $no_hp = mysqli_real_escape_string($db, $_POST['no_hp']);
    $nama_sekolah = mysqli_real_escape_string($db, $_POST['nama_sekolah']); // Pastikan kolom ini ada
    $jurusan = mysqli_real_escape_string($db, $_POST['jurusan']);
    $status = 'proses'; // Status awal

    // Query INSERT ke tabel 'pendaftaran'
    // Pastikan semua nama kolom sesuai dengan database Anda
    $query = "INSERT INTO pendaftaran (nama_lengkap, jenis_kelamin, agama, tempat_lahir, tanggal_lahir, nisn, alamat_email, no_hp, nama_sekolah, jurusan, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = mysqli_prepare($db, $query)) {
        mysqli_stmt_bind_param($stmt, "sssssssssss", 
            $nama_lengkap, $jenis_kelamin, $agama, $tempat_lahir, $tanggal_lahir, $nisn, $alamat_email, $no_hp, $nama_sekolah, $jurusan, $status);

        // Eksekusi statement
        if (mysqli_stmt_execute($stmt)) {
            // === BERHASIL ===
            // Redirect kembali ke halaman pendaftaran dengan parameter sukses
            header("Location: ../../pendaftaran.php?sukses=1");
            exit();
        } else {
            // === GAGAL INSERT ===
            // Redirect kembali dengan parameter error
            // Tampilkan error SQL jika perlu untuk debugging: echo "Error: " . mysqli_stmt_error($stmt); exit();
            header("Location: ../../pendaftaran.php?error=1");
            exit();
        }
        mysqli_stmt_close($stmt);
    } else {
        // === GAGAL PREPARE STATEMENT ===
         // Redirect kembali dengan parameter error
         // Tampilkan error SQL jika perlu: echo "Error preparing statement: " . mysqli_error($db); exit();
        header("Location: ../../pendaftaran.php?error=1");
        exit();
    }
    mysqli_close($db);

} else {
    // Jika bukan metode POST, redirect ke halaman pendaftaran
    header("Location: ../../pendaftaran.php");
    exit();
}
?>

<script>
    document.querySelectorAll('.status-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const id = this.dataset.id;
            const statusBaru = this.value;
            Swal.fire({
                title: 'Konfirmasi Ubah Status',
                text: 'Yakin ingin mengubah status pendaftar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('backend/modules/ubah_status_ppdb.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'id=' + id + '&status=' + statusBaru
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil!', 'Status berhasil diubah.', 'success');
                            } else {
                                Swal.fire('Gagal!', 'Status gagal diubah.', 'error');
                            }
                        });
                } else {
                    this.value = this.getAttribute('data-old');
                }
            });
        });
        select.setAttribute('data-old', select.value);
    });
</script>