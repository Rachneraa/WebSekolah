<?php

require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = $_POST['nisn'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $agama = $_POST['agama'] ?? '';
    $tempat_lahir = $_POST['tempat_lahir'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $bulan = $_POST['bulan'] ?? '';
    $tahun = $_POST['tahun'] ?? '';
    $alamat_email = $_POST['alamat_email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $nama_sekolah = $_POST['nama_sekolah'] ?? '';
    $jurusan = $_POST['jurusan'] ?? '';

    // Gabungkan tanggal lahir
    $tanggal_lahir = "$tahun-$bulan-$tanggal";

    // Validasi sederhana
    if ($nisn && $nama_lengkap && $jenis_kelamin && $agama && $tempat_lahir && $tanggal && $bulan && $tahun && $no_hp && $nama_sekolah && $jurusan) {
        $stmt = $db->prepare("INSERT INTO ppdb_pendaftar (nisn, nama_lengkap, jenis_kelamin, agama, tempat_lahir, tanggal_lahir, alamat_email, no_hp, nama_sekolah, jurusan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $nisn, $nama_lengkap, $jenis_kelamin, $agama, $tempat_lahir, $tanggal_lahir, $alamat_email, $no_hp, $nama_sekolah, $jurusan);
        if ($stmt->execute()) {
            header("Location: ../../pendaftaran.php?sukses=1");
            exit();
        } else {
            header("Location: ../../pendaftaran.php?error=1");
            exit();
        }
    } else {
        header("Location: ../../pendaftaran.php?error=1");
        exit();
    }
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