<?php
session_start();
require_once "../config/koneksi.php";

// selalu kembalikan JSON
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Get request data (robust JSON detection)
$requestData = [];
$contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (stripos($contentType, 'application/json') !== false) {
    $input = file_get_contents('php://input');
    $requestData = json_decode($input, true) ?? [];
} else {
    $requestData = $_POST ?? [];
}

$action = isset($_GET['action']) ? $_GET['action'] : (isset($requestData['action']) ? $requestData['action'] : '');

// Get absensi data (include detail rows)
if ($action == 'get_absensi') {
    $query = "SELECT a.*, k.nama as kelas_nama 
              FROM absensi a 
              LEFT JOIN kelas k ON a.kelas_id = k.id 
              ORDER BY a.tanggal DESC";

    $result = mysqli_query($db, $query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
        exit;
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $absensi_id = (int) $row['id'];
        $detailQuery = "SELECT ad.*, s.nama as siswa_nama 
                        FROM absensi_detail ad 
                        LEFT JOIN siswa s ON ad.siswa_id = s.id 
                        WHERE ad.absensi_id = $absensi_id";
        $detailRes = mysqli_query($db, $detailQuery);
        $details = [];
        if ($detailRes) {
            while ($d = mysqli_fetch_assoc($detailRes)) {
                $details[] = $d;
            }
        }
        $row['detail'] = $details;
        $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// Add absensi
if ($action == 'add_absensi') {
    $kelas_id = mysqli_real_escape_string($db, $requestData['kelas_id']);
    $tanggal = mysqli_real_escape_string($db, $requestData['tanggal']);
    $total = (int) $requestData['total'];
    $hadir = (int) $requestData['hadir'];
    $sakit = (int) $requestData['sakit'];
    $izin = (int) $requestData['izin'];
    $alpha = (int) $requestData['alpha'];

    $query = "INSERT INTO absensi (tanggal, kelas_id, total_siswa, hadir, sakit, izin, alpha) 
              VALUES ('$tanggal', $kelas_id, $total, $hadir, $sakit, $izin, $alpha)";

    if (mysqli_query($db, $query)) {
        $absensi_id = mysqli_insert_id($db);

        // Insert detail absensi
        if (isset($requestData['detail'])) {
            foreach ($requestData['detail'] as $detail) {
                $siswa_id = (int) $detail['siswa_id'];
                $status = mysqli_real_escape_string($db, $detail['status']);
                $keterangan = mysqli_real_escape_string($db, $detail['keterangan']);

                mysqli_query($db, "INSERT INTO absensi_detail (absensi_id, siswa_id, status, keterangan) 
                                  VALUES ($absensi_id, $siswa_id, '$status', '$keterangan')");
            }
        }

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
    }
    exit;
}

// Get nilai data
if ($action == 'get_nilai') {
    $query = "SELECT n.*, s.nama as siswa_nama 
              FROM nilai n 
              LEFT JOIN siswa s ON n.siswa_id = s.id 
              ORDER BY n.id DESC";

    $result = mysqli_query($db, $query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
        exit;
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// Add nilai
if ($action == 'add_nilai') {
    $siswa_id = mysqli_real_escape_string($db, $requestData['siswa_id']);
    $mapel = mysqli_real_escape_string($db, $requestData['mapel']);
    $tugas = (int) $requestData['tugas'];
    $uts = (int) $requestData['uts'];
    $uas = (int) $requestData['uas'];

    $query = "INSERT INTO nilai (siswa_id, mapel, tugas, uts, uas) 
              VALUES ($siswa_id, '$mapel', $tugas, $uts, $uas)";

    if (mysqli_query($db, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
    }
    exit;
}

// Get jadwal data
if ($action == 'get_jadwal') {
    $query = "SELECT j.*, k.nama as kelas_nama 
              FROM jadwal j 
              LEFT JOIN kelas k ON j.kelas_id = k.id 
              ORDER BY j.id DESC";

    $result = mysqli_query($db, $query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
        exit;
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// Add jadwal
if ($action == 'add_jadwal') {
    $kelas_id = mysqli_real_escape_string($db, $requestData['kelas_id']);
    $hari = mysqli_real_escape_string($db, $requestData['hari']);
    $jam = mysqli_real_escape_string($db, $requestData['jam']);
    $mapel = mysqli_real_escape_string($db, $requestData['mapel']);
    $guru_id = $_SESSION['user_id'];

    $query = "INSERT INTO jadwal (kelas_id, hari, jam, mapel, guru_id) 
              VALUES ($kelas_id, '$hari', '$jam', '$mapel', $guru_id)";

    if (mysqli_query($db, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
    }
    exit;
}

// Update absensi
if ($action == 'update_absensi') {
    $id = (int) ($requestData['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        exit;
    }

    $kelas_id = (int) ($requestData['kelas_id'] ?? 0);
    $tanggal = mysqli_real_escape_string($db, $requestData['tanggal'] ?? '');
    $total = (int) ($requestData['total'] ?? 0);
    $hadir = (int) ($requestData['hadir'] ?? 0);
    $sakit = (int) ($requestData['sakit'] ?? 0);
    $izin = (int) ($requestData['izin'] ?? 0);
    $alpha = (int) ($requestData['alpha'] ?? 0);

    $update = "UPDATE absensi SET tanggal='$tanggal', kelas_id=$kelas_id, total_siswa=$total, hadir=$hadir, sakit=$sakit, izin=$izin, alpha=$alpha WHERE id=$id";
    if (!mysqli_query($db, $update)) {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
        exit;
    }

    // replace detail rows
    mysqli_query($db, "DELETE FROM absensi_detail WHERE absensi_id = $id");
    if (!empty($requestData['detail']) && is_array($requestData['detail'])) {
        foreach ($requestData['detail'] as $d) {
            $siswa_id = (int) ($d['siswa_id'] ?? 0);
            $status = mysqli_real_escape_string($db, $d['status'] ?? '');
            $keterangan = mysqli_real_escape_string($db, $d['keterangan'] ?? '');
            mysqli_query($db, "INSERT INTO absensi_detail (absensi_id, siswa_id, status, keterangan) VALUES ($id, $siswa_id, '$status', '$keterangan')");
        }
    }

    echo json_encode(['status' => 'success']);
    exit;
}

// Get mapel list for current user (teacher) or all for admin
if ($action === 'get_mapel_for_user') {
    $user_id = $_SESSION['user_id'];
    $level = $_SESSION['level'];

    if ($level === 'admin') {
        // Admin bisa akses semua mapel
        $query = "SELECT DISTINCT mapel FROM jadwal WHERE mapel IS NOT NULL AND mapel <> ''";
        $result = mysqli_query($db, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['mapel'])) {
                $data[] = ['nama' => $row['mapel']];
            }
        }
    } else {
        // Guru hanya bisa akses mapel yang dia ajar
        $query = "SELECT mapel FROM users WHERE id = $user_id";
        $result = mysqli_query($db, $query);
        $data = [];
        if ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['mapel'])) {
                $mapel_array = array_map('trim', explode(',', $row['mapel']));
                foreach ($mapel_array as $mapel) {
                    $data[] = ['nama' => $mapel];
                }
            }
        }
    }

    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

// Get classes teacher teaches for a mapel
if ($action === 'get_kelas_for_mapel') {
    $uid = (int) $_SESSION['user_id'];
    $level = $_SESSION['level'] ?? '';

    $mapel_id = isset($_GET['mapel_id']) ? (int) $_GET['mapel_id'] : null;
    $mapel_name = isset($_GET['mapel_name']) ? mysqli_real_escape_string($db, $_GET['mapel_name']) : null;

    // detect mapel table & jadwal mapel_id column
    $mapel_table = '';
    $checkTables = ['mapel', 'mata_pelajaran', 'pelajaran'];
    foreach ($checkTables as $t) {
        $r = mysqli_query($db, "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $t) . "'");
        if ($r && mysqli_num_rows($r) > 0) {
            $mapel_table = $t;
            break;
        }
    }
    $colRes = mysqli_query($db, "SHOW COLUMNS FROM `jadwal` LIKE 'mapel_id'");
    $has_mapel_id = ($colRes && mysqli_num_rows($colRes) > 0);

    $whereParts = [];
    if ($mapel_id) {
        if ($has_mapel_id) {
            $whereParts[] = "j.mapel_id = $mapel_id";
        } else {
            // try resolve id -> name
            $mname = '';
            if ($mapel_table) {
                $mrow = mysqli_fetch_assoc(mysqli_query($db, "SELECT nama FROM `$mapel_table` WHERE id = $mapel_id LIMIT 1"));
                $mname = $mrow['nama'] ?? '';
            }
            if ($mname !== '') {
                $whereParts[] = "j.mapel = '" . mysqli_real_escape_string($db, $mname) . "'";
            } else {
                echo json_encode(['status' => 'success', 'data' => []]);
                exit;
            }
        }
    } elseif ($mapel_name) {
        $whereParts[] = "j.mapel = '" . $mapel_name . "'";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing mapel parameter']);
        exit;
    }

    if ($level !== 'admin')
        $whereParts[] = "j.guru_id = " . (int) $uid;

    $where = implode(' AND ', $whereParts);
    $q = "SELECT DISTINCT k.id, k.nama FROM jadwal j JOIN kelas k ON j.kelas_id = k.id WHERE $where ORDER BY k.nama";
    $res = mysqli_query($db, $q);
    $out = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res))
            $out[] = $r;
    }
    echo json_encode(['status' => 'success', 'data' => $out]);
    exit;
}

// Add nilai (with teacher-mapel validation)
if ($action == 'add_nilai') {
    $siswa_id = (int) ($requestData['siswa_id'] ?? 0);
    if ($siswa_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Siswa tidak valid']);
        exit;
    }

    // mapel may be provided as id and/or name
    $mapel_id = isset($requestData['mapel_id']) && $requestData['mapel_id'] !== '' ? (int) $requestData['mapel_id'] : null;
    $mapel_name = trim($requestData['mapel'] ?? '');

    // if we have id and mapel table exists, resolve name
    $mapel_table = '';
    $checkTables = ['mapel', 'mata_pelajaran', 'pelajaran'];
    foreach ($checkTables as $t) {
        $r = mysqli_query($db, "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $t) . "'");
        if ($r && mysqli_num_rows($r) > 0) {
            $mapel_table = $t;
            break;
        }
    }
    if ($mapel_id && $mapel_table) {
        $mr = mysqli_fetch_assoc(mysqli_query($db, "SELECT nama FROM `$mapel_table` WHERE id = $mapel_id LIMIT 1"));
        if ($mr)
            $mapel_name = $mr['nama'];
    }

    $tugas = isset($requestData['tugas']) ? (int) $requestData['tugas'] : 0;
    $uts = isset($requestData['uts']) ? (int) $requestData['uts'] : 0;
    $uas = isset($requestData['uas']) ? (int) $requestData['uas'] : 0;

    // security: ensure teacher is allowed to input nilai for this siswa/mapel
    $uid = (int) $_SESSION['user_id'];
    $level = $_SESSION['level'] ?? '';

    $sRow = mysqli_fetch_assoc(mysqli_query($db, "SELECT kelas_id FROM siswa WHERE id = $siswa_id LIMIT 1"));
    $s_kelas_id = $sRow['kelas_id'] ?? null;

    $allowed = false;
    if ($level === 'admin') {
        $allowed = true;
    } else {
        // check users.mapel_ids if exists
        $colCheck = mysqli_query($db, "SHOW COLUMNS FROM `users` LIKE 'mapel_ids'");
        if ($colCheck && mysqli_num_rows($colCheck) > 0) {
            $ur = mysqli_fetch_assoc(mysqli_query($db, "SELECT mapel_ids FROM users WHERE id = $uid LIMIT 1"));
            if (!empty($ur['mapel_ids'])) {
                $arr = json_decode($ur['mapel_ids'], true);
                if (is_array($arr)) {
                    $arr = array_map('intval', $arr);
                    if ($mapel_id && in_array($mapel_id, $arr))
                        $allowed = true;
                    else if (!$mapel_id && $mapel_table && $mapel_name) {
                        // try resolve name->id
                        $mr = mysqli_fetch_assoc(mysqli_query($db, "SELECT id FROM `$mapel_table` WHERE nama = '" . mysqli_real_escape_string($db, $mapel_name) . "' LIMIT 1"));
                        if ($mr && in_array((int) $mr['id'], $arr))
                            $allowed = true;
                    }
                }
            }
        }

        // fallback: check jadwal (teacher teaches this mapel for student's class)
        $jadwalWhere = "j.guru_id = $uid AND j.kelas_id = " . (int) $s_kelas_id;
        $colMapelId = mysqli_query($db, "SHOW COLUMNS FROM `jadwal` LIKE 'mapel_id'");
        if ($colMapelId && mysqli_num_rows($colMapelId) > 0 && $mapel_id) {
            $jadwalWhere .= " AND j.mapel_id = $mapel_id";
        } else {
            $jadwalWhere .= " AND j.mapel = '" . mysqli_real_escape_string($db, $mapel_name) . "'";
        }
        $rj = mysqli_query($db, "SELECT 1 FROM jadwal j WHERE $jadwalWhere LIMIT 1");
        if ($rj && mysqli_num_rows($rj) > 0)
            $allowed = true;
    }

    if (!$allowed) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak mengajar mapel ini untuk kelas siswa tersebut.']);
        exit;
    }

    // insert nilai (store mapel name)
    $mapelEsc = mysqli_real_escape_string($db, $mapel_name);
    $query = "INSERT INTO nilai (siswa_id, mapel, tugas, uts, uas) VALUES ($siswa_id, '$mapelEsc', $tugas, $uts, $uas)";
    if (mysqli_query($db, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
    }
    exit;
}

// Update nilai (with same validation)
if ($action == 'update_nilai') {
    $id = (int) ($requestData['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        exit;
    }

    $siswa_id = (int) ($requestData['siswa_id'] ?? 0);
    if ($siswa_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Siswa tidak valid']);
        exit;
    }

    $mapel_id = isset($requestData['mapel_id']) && $requestData['mapel_id'] !== '' ? (int) $requestData['mapel_id'] : null;
    $mapel_name = trim($requestData['mapel'] ?? '');

    $mapel_table = '';
    $checkTables = ['mapel', 'mata_pelajaran', 'pelajaran'];
    foreach ($checkTables as $t) {
        $r = mysqli_query($db, "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $t) . "'");
        if ($r && mysqli_num_rows($r) > 0) {
            $mapel_table = $t;
            break;
        }
    }
    if ($mapel_id && $mapel_table) {
        $mr = mysqli_fetch_assoc(mysqli_query($db, "SELECT nama FROM `$mapel_table` WHERE id = $mapel_id LIMIT 1"));
        if ($mr)
            $mapel_name = $mr['nama'];
    }

    $tugas = (int) ($requestData['tugas'] ?? 0);
    $uts = (int) ($requestData['uts'] ?? 0);
    $uas = (int) ($requestData['uas'] ?? 0);

    // validate teacher mapping same as add_nilai
    $uid = (int) $_SESSION['user_id'];
    $level = $_SESSION['level'] ?? '';
    $sRow = mysqli_fetch_assoc(mysqli_query($db, "SELECT kelas_id FROM siswa WHERE id = $siswa_id LIMIT 1"));
    $s_kelas_id = $sRow['kelas_id'] ?? null;
    $allowed = false;
    if ($level === 'admin')
        $allowed = true;
    else {
        // users.mapel_ids check
        $colCheck = mysqli_query($db, "SHOW COLUMNS FROM `users` LIKE 'mapel_ids'");
        if ($colCheck && mysqli_num_rows($colCheck) > 0) {
            $ur = mysqli_fetch_assoc(mysqli_query($db, "SELECT mapel_ids FROM users WHERE id = $uid LIMIT 1"));
            if (!empty($ur['mapel_ids'])) {
                $arr = json_decode($ur['mapel_ids'], true);
                if (is_array($arr)) {
                    $arr = array_map('intval', $arr);
                    if ($mapel_id && in_array($mapel_id, $arr))
                        $allowed = true;
                    else if (!$mapel_id && $mapel_table && $mapel_name) {
                        $mr = mysqli_fetch_assoc(mysqli_query($db, "SELECT id FROM `$mapel_table` WHERE nama = '" . mysqli_real_escape_string($db, $mapel_name) . "' LIMIT 1"));
                        if ($mr && in_array((int) $mr['id'], $arr))
                            $allowed = true;
                    }
                }
            }
        }
        // jadwal fallback
        $colMapelId = mysqli_query($db, "SHOW COLUMNS FROM `jadwal` LIKE 'mapel_id'");
        if ($colMapelId && mysqli_num_rows($colMapelId) > 0 && $mapel_id) {
            $jadwalWhere = "j.guru_id = $uid AND j.kelas_id = " . (int) $s_kelas_id . " AND j.mapel_id = $mapel_id";
        } else {
            $jadwalWhere = "j.guru_id = $uid AND j.kelas_id = " . (int) $s_kelas_id . " AND j.mapel = '" . mysqli_real_escape_string($db, $mapel_name) . "'";
        }
        $rj = mysqli_query($db, "SELECT 1 FROM jadwal j WHERE $jadwalWhere LIMIT 1");
        if ($rj && mysqli_num_rows($rj) > 0)
            $allowed = true;
    }

    if (!$allowed) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak mengajar mapel ini untuk kelas siswa tersebut.']);
        exit;
    }

    $mapelEsc = mysqli_real_escape_string($db, $mapel_name);
    $update = "UPDATE nilai SET siswa_id=$siswa_id, mapel='$mapelEsc', tugas=$tugas, uts=$uts, uas=$uas WHERE id=$id";
    if (mysqli_query($db, $update)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($db)]);
    }
    exit;
}
?>