<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'siswa') {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foto Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #my_camera { width: 320px; height: 240px; margin: 0 auto; }
        .capture-container { text-align: center; padding: 20px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Ambil Foto Kehadiran</h4>
            </div>
            <div class="card-body capture-container">
                <div id="my_camera"></div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="take_snapshot()">Ambil Foto</button>
                </div>
                <div id="results" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script>
        Webcam.set({
            width: 320,
            height: 240,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
        Webcam.attach('#my_camera');

        function take_snapshot() {
            Webcam.snap(function(data_uri) {
                document.getElementById('results').innerHTML = 
                    '<img src="'+data_uri+'"/>' +
                    '<form id="uploadForm" action="save_photo.php" method="post" class="mt-3">' +
                    '<input type="hidden" name="photo" value="'+data_uri+'"/>' +
                    '<button type="submit" class="btn btn-success">Simpan & Lanjutkan</button>' +
                    '</form>';
            });
        }
    </script>
</body>
</html>