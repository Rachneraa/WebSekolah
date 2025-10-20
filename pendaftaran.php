<?php
session_start(); // Add this at the top
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir PPDB - SMK TI Garuda Nusantara</title>
    <link rel="icon" type="image/png" href="icons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="icons/favicon.svg" />
    <link rel="shortcut icon" href="icons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="SMK TI GNC" />
    <link rel="manifest" href="/pkl/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/pkl/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/pkl/icons/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/pkl/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#00499D">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #00499d;
            --primary-orange: #ff8303;
            --dark-blue: #003366;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Tambahkan agar konten di tengah */
            justify-content: flex-start;
        }

        html,
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Form Styles */
        .form-wrapper {
            max-width: 700px;
            width: 100%;
            margin: 60px auto 40px auto;
            padding: 0 32px;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Form di tengah */
        }

        .form-header {
            width: 100%;
            background: #fff;
            color: #1e5bb8;
            padding: 40px 0 30px 0;
            text-align: center;
            border-bottom: 3px solid #1e5bb8;
            border-radius: 30px 30px 0 0;
        }

        .form-content {
            width: 100%;
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 25px;
            display: grid;
            grid-template-columns: 180px 1fr;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .form-group.full-width {
            grid-template-columns: 1fr;
        }

        label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-align: right;
        }

        .full-width label {
            text-align: center;
            margin-bottom: 10px;
            font-size: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            outline: none;
            border-color: #2776d4;
            box-shadow: 0 0 0 3px rgba(39, 118, 212, 0.1);
        }

        .input-icon {
            position: relative;
        }

        .input-icon::before {
            content: '👤';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
        }

        .email-icon::before {
            content: '✉️';
        }

        .location-icon::before {
            content: '🏫';
        }

        .phone-icon::before {
            content: '📱';
        }

        .input-icon input {
            padding-left: 40px;
        }

        .date-group {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            grid-column: 2;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1e5bb8 0%, #2776d4 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 91, 184, 0.3);
        }

        .btn-kembali {
            position: absolute;
            left: 40px;
            top: 40px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #1e5bb8;
            border: 2px solid #1e5bb8;
            border-radius: 30px;
            padding: 8px 22px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .btn-kembali:hover {
            background: #1e5bb8;
            color: #fff;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .footer-bottom {
            background: linear-gradient(135deg, var(--dark-blue), #001a33);
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 40px;
            width: 100vw;
            box-sizing: border-box;
        }

        .footer-bottom p {
            opacity: 0.9;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .footer-bottom .fa-heart {
            color: #ff8303;
            animation: heartbeat 1.5s ease infinite;
        }

        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }


        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 900px) {
            .form-wrapper {
                max-width: 98vw;
                padding: 0 8px;
            }

            .form-header,
            .form-content {
                padding: 20px 0;
            }

            .btn-kembali {
                left: 10px;
                top: 20px;
                padding: 6px 16px;
                font-size: 14px;
            }
        }

        @media (max-width: 600px) {
            .form-group {
                grid-template-columns: 1fr;
            }

            label {
                text-align: left;
            }

            .form-header {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>


    <!-- Formulir PPDB -->
    <a href="javascript:history.back()" class="btn-kembali">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
    <div class="form-wrapper">
        <div class="form-header">
            <h1>Formulir Pendaftaran Peserta Didik Baru</h1>
        </div>
        <div class="form-content">
            <form id="ppdbForm">
                <div class="form-group">
                    <label for="namaLengkap">Nama Lengkap</label>
                    <div>
                        <div class="input-icon">
                            <input type="text" id="namaLengkap" placeholder="Nama lengkap Calon Siswa" required>
                        </div>
                        <div class="error-message">*Sesuai dengan Ijazah</div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="alamatEmail">Alamat Email</label>
                    <div class="input-icon email-icon">
                        <input type="email" id="alamatEmail" placeholder="Alamat Email (Boleh di kosongi)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="jenisKelamin">Jenis Kelamin</label>
                    <select id="jenisKelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki">Laki-laki</option>
                        <option value="perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tempatLahir">Tempat Kelahiran</label>
                    <div class="input-icon location-icon">
                        <input type="text" id="tempatLahir" placeholder="Tempat Kelahiran Calon Siswa" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tanggal Kelahiran</label>
                    <div class="date-group">
                        <select id="tanggal" required>
                            <option value="">Pilih tanggal</option>
                            <script>
                                for (let i = 1; i <= 31; i++) {
                                    document.write(`<option value="${i}">${i}</option>`);
                                }
                            </script>
                        </select>
                        <select id="bulan" required>
                            <option value="">Pilih Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <select id="tahun" required>
                            <option value="">Pilih Tahun Lahir</option>
                            <script>
                                const currentYear = new Date().getFullYear();
                                for (let i = currentYear - 5; i >= currentYear - 20; i--) {
                                    document.write(`<option value="${i}">${i}</option>`);
                                }
                            </script>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="noHp">No.Hp/WhatsApp Orang Tua</label>
                    <div class="input-icon phone-icon">
                        <input type="tel" id="noHp" placeholder="No HP/WhatsApp Orang Tua" required>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="namaSekolah">Nama Sekolah Asal</label>
                    <div class="input-icon location-icon">
                        <input type="text" id="namaSekolah" placeholder="Nama Asal Sekolah" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="namaJurusan">Nama Jurusan</label>
                    <select id="namaJurusan" required>
                        <option value="">Pilih Jurusan</option>
                        <option value="rpl">Rekayasa Perangkat Lunak</option>
                        <option value="tkj">Teknik Komputer dan Jaringan</option>
                        <option value="mm">Multimedia</option>
                        <option value="akuntansi">Akuntansi</option>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Daftar Sekarang</button>
            </form>
        </div>
    </div>

    <!-- Simplified Footer -->
    <div class="footer-bottom">
        <p>
            &copy; 2025 SMK TI Garuda Nusantara. All Rights Reserved. | Designed with
            <i class="fas fa-heart"></i>
            by Tim IT
        </p>
    </div>

    <script>

        // Keep only the form submit handler
        const form = document.getElementById('ppdbForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = {
                namaLengkap: document.getElementById('namaLengkap').value,
                alamatEmail: document.getElementById('alamatEmail').value,
                jenisKelamin: document.getElementById('jenisKelamin').value,
                tempatLahir: document.getElementById('tempatLahir').value,
                tanggalLahir: `${document.getElementById('tanggal').value}-${document.getElementById('bulan').value}-${document.getElementById('tahun').value}`,
                noHp: document.getElementById('noHp').value,
                namaSekolah: document.getElementById('namaSekolah').value,
                namaJurusan: document.getElementById('namaJurusan').value
            };
            console.log('Data Pendaftaran:', formData);
            alert('Pendaftaran berhasil! Data telah dikirim.\n\nTerima kasih telah mendaftar di SMK TI Garuda Nusantara.');
            form.reset();
        });
    </script>
</body>

</html>