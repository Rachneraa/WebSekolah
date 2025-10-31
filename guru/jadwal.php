<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

// Cek login guru
if (!isset($_SESSION['user_id']) || $_SESSION['level'] != 'guru') {
    header('Location: ../index.php');
    exit();
}

$guru_id = $_SESSION['guru_id'];

// Mapping hari
$hari_map = [
    'Senin',
    'Selasa',
    'Rabu',
    'Kamis',
    'Jumat',
    'Sabtu'
];

// Filter hari
$filter_hari = isset($_GET['hari']) && in_array($_GET['hari'], $hari_map) ? $_GET['hari'] : '';

// Ambil jadwal guru
$params = [$guru_id];
$sql = "SELECT j.*, k.nama AS kelas_nama, m.nama AS mapel_nama 
        FROM jadwal j
        JOIN kelas k ON j.kelas_id = k.kelas_id
        JOIN mapel m ON j.mapel_id = m.id
        WHERE j.guru_id = ?";
if ($filter_hari) {
    $sql .= " AND j.hari = ?";
    $params[] = $filter_hari;
}
$sql .= " ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), j.jam";

$stmt = $db->prepare($sql);
if (count($params) == 2) {
    $stmt->bind_param("is", $params[0], $params[1]);
} else {
    $stmt->bind_param("i", $params[0]);
}
$stmt->execute();
$jadwal = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<style>
    .filter-bar {
        margin-bottom: 24px;
    }

    .jadwal-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .jadwal-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(30, 136, 229, 0.08);
        padding: 20px 24px;
        min-width: 260px;
        max-width: 320px;
        flex: 1 1 260px;
        border-left: 5px solid #1976d2;
        position: relative;
    }

    .jadwal-card .hari {
        font-weight: bold;
        color: #1976d2;
        font-size: 1.1em;
        margin-bottom: 6px;
    }

    .jadwal-card .jam {
        font-size: 1em;
        color: #333;
        margin-bottom: 8px;
    }

    .jadwal-card .kelas {
        font-size: 1em;
        color: #444;
        margin-bottom: 4px;
    }

    .jadwal-card .mapel {
        font-size: 1.1em;
        font-weight: 500;
        color: #1565c0;
        margin-bottom: 4px;
    }

    .jadwal-card .ruangan {
        font-size: 0.97em;
        color: #666;
    }

    @media (max-width: 768px) {
        .jadwal-grid {
            flex-direction: column;
            gap: 14px;
        }

        .jadwal-card {
            max-width: 100%;
        }
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center filter-bar flex-wrap">
        <form method="get" action="guru.php" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="page" value="jadwal">
            <label for="hari" class="me-2 fw-semibold">Filter Hari:</label>
            <select name="hari" id="hari" class="form-select" style="width:150px" onchange="this.form.submit()">
                <option value="">Semua</option>
                <?php foreach ($hari_map as $h): ?>
                    <option value="<?= $h ?>" <?= $filter_hari == $h ? 'selected' : '' ?>><?= $h ?></option>
                <?php endforeach ?>
            </select>
        </form>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Cetak Jadwal
        </button>
    </div>

    <?php if ($jadwal): ?>
        <div class="jadwal-grid">
            <?php foreach ($jadwal as $j): ?>
                <div class="jadwal-card">
                    <div class="hari"><?= htmlspecialchars($j['hari']) ?></div>
                    <div class="jam">
                        <i class="fas fa-clock"></i>
                        <?= htmlspecialchars($j['jam']) ?>
                    </div>
                    <div class="kelas">
                        <i class="fas fa-door-open"></i>
                        <?= htmlspecialchars($j['kelas_nama']) ?>
                    </div>
                    <div class="mapel">
                        <i class="fas fa-book"></i>
                        <?= htmlspecialchars($j['mapel_nama']) ?>
                    </div>
                    <div class="ruangan">
                        <i class="fas fa-map-marker-alt"></i>
                        Ruangan: <?= htmlspecialchars($j['kelas_nama']) ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-4">Tidak ada jadwal ditemukan.</div>
    <?php endif ?>
</div>