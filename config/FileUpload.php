<?php
class FileUpload
{
    private $uploadDir;
    private $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    private $maxSize = 5242880; // 5MB

    public function __construct($uploadDir = null)
    {
        $this->uploadDir = $uploadDir ?? dirname(__DIR__) . '/berita/';
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($file)
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            throw new Exception('Tidak ada file yang diupload');
        }

        $fileInfo = pathinfo($file['name']);
        $ext = strtolower($fileInfo['extension']);

        // Validasi tipe file
        if (!in_array($ext, $this->allowedTypes)) {
            throw new Exception('Tipe file tidak diizinkan');
        }

        // Validasi ukuran
        if ($file['size'] > $this->maxSize) {
            throw new Exception('Ukuran file terlalu besar (max 5MB)');
        }

        // Generate nama file unik
        $newName = uniqid() . '.' . $ext;
        $destination = $this->uploadDir . $newName;

        // Pindahkan file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Gagal mengupload file');
        }

        return $newName;
    }

    public function delete($filename)
    {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}