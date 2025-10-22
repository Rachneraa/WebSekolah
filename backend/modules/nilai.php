<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Handle create
                break;
            case 'update':
                // Handle update
                break;
            case 'delete':
                // Handle delete
                break;
        }
    }
}

// Display berita list
$query = "SELECT * FROM berita ORDER BY tanggal DESC";
$result = mysqli_query($db, $query);
?>

<div class="card">
    <h2>Kelola Berita</h2>
    <button class="add-btn" onclick="openForm('berita')">Tambah Berita</button>
    
    <table>
        <thead>
            <tr>
                <th>Judul</th>
                <th>Tanggal</th>
                <th>Penulis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                    <td>
                        <button class="action-btn" onclick="editBerita(<?= $row['id'] ?>)">Edit</button>
                        <button class="delete-btn" onclick="deleteBerita(<?= $row['id'] ?>)">Hapus</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>