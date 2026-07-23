<?php
require_once 'config/koneksi.php';

// Hapus data lama agar 9 berita baru bersih dan konsisten
mysqli_query($db, "DELETE FROM berita");
mysqli_query($db, "ALTER TABLE berita AUTO_INCREMENT = 1");

$berita_list = [
    [
        'judul' => 'Implementasi Kurikulum Merdeka Terpadu & Projek P5 Siswa SMPN Cimahi',
        'isi' => "SMPN Cimahi secara aktif mengimplementasikan Kurikulum Merdeka dengan fokus pada Projek Penguatan Profil Pelajar Pancasila (P5).\n\nKegiatan ini melibatkan partisipasi penuh seluruh siswa kelas VII, VIII, dan IX dalam membuat karya inovasi daur ulang sampah serta pameran seni budaya nusantara. Kepala sekolah menyampaikan bahwa program P5 berttujuan membentuk karakter siswa yang kreatif, mandiri, dan bergotong royong.",
        'tags' => 'Akademik, Kurikulum',
        'gambar' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-05-12 09:00:00',
        'penulis' => 'Humas SMPN Cimahi'
    ],
    [
        'judul' => 'Siswa SMPN Cimahi Meraih Medali Emas FLS2N Tingkat Kota',
        'isi' => "Prestasi membanggakan kembali diraih oleh kontingen Festival dan Lomba Seni Siswa Nasional (FLS2N) SMPN Cimahi.\n\nTim seni musik akustik dan duet seni tari kreasi berhasil membawa pulang piala Juara 1 tingkat Kota Cimahi. Keberhasilan ini menghantarkan SMPN Cimahi untuk mewakili kota ke tingkat Provinsi Jawa Barat.",
        'tags' => 'Prestasi, Seni',
        'gambar' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-05-08 14:30:00',
        'penulis' => 'Tim Kesiswaan'
    ],
    [
        'judul' => 'Semarak Pekan Kebudayaan & Jalan Sehat Bersama Warga Sekolah',
        'isi' => "Dalam rangka meningkatkan rasa kebersamaan dan kecintaan pada budaya lokal, SMPN Cimahi menggelar acara Jalan Sehat Kebudayaan.\n\nPara siswa dan guru tampil anggun mengenakan pakaian adat nusantara. Acara juga dimeriahkan dengan pembagian doorprize menarik dan pertunjukan tari tradisional daerah Jawa Barat.",
        'tags' => 'Kegiatan, Kebudayaan',
        'gambar' => 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-05-02 10:15:00',
        'penulis' => 'Panitia OSIS'
    ],
    [
        'judul' => 'Penerimaan Peserta Didik Baru (PPDB) TA 2026/2027 Resmi Dibuka',
        'isi' => "SMPN Cimahi secara resmi membuka pendaftaran Penerimaan Peserta Didik Baru (PPDB) untuk tahun ajaran 2026/2027.\n\nProses pendaftaran dilaksanakan secara online melalui portal resmi sekolah. Terdapat 4 jalur penerimaan utama yaitu Jalur Zonasi, Prestasi Academic/Non-Academic, Afirmasi, serta Perpindahan Tugas Orang Tua.",
        'tags' => 'Pengumuman, PPDB',
        'gambar' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-04-28 08:00:00',
        'penulis' => 'Panitia PPDB'
    ],
    [
        'judul' => 'Pelatihan Literasi Digital & Keamanan Siber Bagi Pelajar',
        'isi' => "SMPN Cimahi bekerja sama dengan Dinas Komunikasi dan Informatika menggelar workshop literasi digital.\n\nKegiatan ini diikuti oleh 150 perwakilan siswa guna memberikan edukasi tentang etika bermedia sosial, bahaya cyberbullying, serta tips menjaga keamanan akun pribadi di internet.",
        'tags' => 'Teknologi, Edukasi',
        'gambar' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-04-20 11:00:00',
        'penulis' => 'Pembina Laboratorium Komputer'
    ],
    [
        'judul' => 'Tim Pramuka SMPN Cimahi Raih Juara Umum Kemah Bakti Penggalang',
        'isi' => "Regu Pramuka Penggalang SMPN Cimahi berhasil menorehkan prestasi gemilang dengan meraih gelar Juara Umum pada ajang Kemah Bakti Penggalang.\n\nPenilaian meliputi ketangkasan pionering, sandi morse, pertolongan pertama (P3K), dan penjelajahan medan.",
        'tags' => 'Ekstrakulikuler, Pramuka',
        'gambar' => 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-04-15 16:00:00',
        'penulis' => 'Pembina Pramuka'
    ],
    [
        'judul' => 'Kunjungan Edukasi Ke Museum & Pusat Sains Teknologi Bandung',
        'isi' => "Sebanyak 200 siswa kelas VIII SMPN Cimahi mengikuti kegiatan study tour edukatif ke Museum Geologi dan Sains Center Bandung.\n\nKegiatan ini bertujuan untuk memperdalam pemahaman siswa tentang sejarah geologi bumi, energi terbarukan, dan eksperimen fisika interaktif.",
        'tags' => 'Kegiatan, Study Tour',
        'gambar' => 'https://images.unsplash.com/photo-1569336415962-a4bd9f69cd83?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-04-05 13:20:00',
        'penulis' => 'Guru IPA'
    ],
    [
        'judul' => 'Penyuluhan Kesehatan Remaja & Gerakan Sekolah Sehat Bebas Plastik',
        'isi' => "Puskesmas setempat bersama tim PMR SMPN Cimahi mengadakan sosialisasi pentingnya gizi seimbang dan kebersihan diri bagi remaja.\n\nPada saat yang sama, sekolah mencanangkan gerakan pembatasan penggunaan kantong plastik sekali pakai di lingkungan kantin sekolah.",
        'tags' => 'Kesehatan, Lingkungan',
        'gambar' => 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-03-25 09:45:00',
        'penulis' => 'Pembina PMR'
    ],
    [
        'judul' => 'Pentas Seni & Bazar Kewirausahaan Siswa Meriahkan Akhir Semester',
        'isi' => "Suasana meriah menyelimuti lapangan SMPN Cimahi dalam penutupan kegiatan tengah semester.\n\nSiswa-siswi menampilkan berbagai kebolehan di panggung pertunjukan drama, musik band, tari kreasi, serta mengoperasikan stan bazar makanan sehat olahan sendiri.",
        'tags' => 'Kreativitas, Kewirausahaan',
        'gambar' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80',
        'tanggal' => '2026-03-18 15:00:00',
        'penulis' => 'Pengurus OSIS'
    ]
];

$stmt = mysqli_prepare($db, "INSERT INTO berita (judul, isi, tags, gambar, tanggal, penulis) VALUES (?, ?, ?, ?, ?, ?)");

$inserted = 0;
foreach ($berita_list as $b) {
    mysqli_stmt_bind_param($stmt, "ssssss", $b['judul'], $b['isi'], $b['tags'], $b['gambar'], $b['tanggal'], $b['penulis']);
    if (mysqli_stmt_execute($stmt)) {
        $inserted++;
    }
}

echo "Berhasil memasukkan $inserted data berita dummy ke database!";
