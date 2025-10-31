<?php
session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

$siswa = intval($_POST['siswa'] ?? 0);
$kelas = intval($_POST['kelas'] ?? 0);
$mapel = intval($_POST['mapel'] ?? 0);
$tugas = intval($_POST['tugas'] ?? 0);
$nilai = isset($_POST['nilai']) ? intval($_POST['nilai']) : null;

if (!$siswa || !$kelas || !$mapel || !$tugas || $nilai === null) {
    echo json_encode(['success'=>false]);
    exit;
}

// Simpan/update nilai
$stmt = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE nilai=VALUES(nilai)");
$stmt->bind_param("iiiii", $siswa, $kelas, $mapel, $tugas, $nilai);
$stmt->execute();
$stmt->close();

echo json_encode(['success'=>true]);
header('Content-Type: application/json; charset=utf-8');
if (session_status() == PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__) . '/config/koneksi.php';

// Matikan mysqli exceptions supaya kita bisa menangani error secara manual
mysqli_report(MYSQLI_REPORT_OFF);

// helper response
function json_res($ok, $msg = '', $extra = []) {
    $out = ['success' => (bool)$ok, 'msg' => $msg];
    if (!empty($extra)) $out = array_merge($out, $extra);
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $siswa = isset($_POST['siswa']) ? intval($_POST['siswa']) : 0;
    $kelas = isset($_POST['kelas']) ? intval($_POST['kelas']) : 0;
    $mapel = $_POST['mapel'] ?? '';
    $tugas = isset($_POST['tugas']) ? intval($_POST['tugas']) : 0;
    $field = $_POST['field'] ?? 'tugas';
    $nilai_raw = $_POST['nilai'] ?? '';

    if (!$siswa || !$kelas || $mapel === '') {
        json_res(false, 'Parameter wajib tidak lengkap', ['posted' => $_POST]);
    }

    $nilai = ($nilai_raw === '' || $nilai_raw === null) ? null : (is_numeric($nilai_raw) ? intval($nilai_raw) : null);

    // Cek kolom pada tabel nilai
    $mapel_col = null;
    $cols = [];
    $colRes = $db->query("SHOW COLUMNS FROM nilai");
    if ($colRes) {
        while ($c = $colRes->fetch_assoc()) $cols[] = $c['Field'];
    }
    if (in_array('mapel_id', $cols)) $mapel_col = 'mapel_id';
    elseif (in_array('mapel', $cols)) $mapel_col = 'mapel';

    $mapel_is_id = ctype_digit((string)$mapel);

    // Simpan nilai tugas (tabel nilai_tugas)
    if ($field === 'tugas' && $tugas > 0) {
        $mapel_id = intval($mapel);
        $q = "SELECT id FROM nilai_tugas WHERE siswa_id=? AND kelas_id=? AND mapel_id=? AND tugas_ke=? LIMIT 1";
        $stmt = $db->prepare($q);
        if (!$stmt) json_res(false, 'Prepare gagal (cek nilai_tugas): ' . $db->error);
        $stmt->bind_param("iiii", $siswa, $kelas, $mapel_id, $tugas);
        if (!$stmt->execute()) { $stmt->close(); json_res(false, 'Execute gagal (cek nilai_tugas): ' . $stmt->error); }
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $id = $row['id'];
            $stmt->close();
            if ($nilai === null) {
                $u = $db->prepare("UPDATE nilai_tugas SET nilai = NULL WHERE id = ?");
                if (!$u) json_res(false, 'Prepare gagal (update tugas): ' . $db->error);
                $u->bind_param("i", $id);
            } else {
                $u = $db->prepare("UPDATE nilai_tugas SET nilai = ? WHERE id = ?");
                if (!$u) json_res(false, 'Prepare gagal (update tugas): ' . $db->error);
                $u->bind_param("ii", $nilai, $id);
            }
            if (!$u->execute()) { $u->close(); json_res(false, 'Execute gagal (update tugas): ' . $u->error); }
            $u->close();
            json_res(true, 'updated');
        } else {
            $stmt->close();
            if ($nilai === null) {
                $ins = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai) VALUES (?, ?, ?, ?, NULL)");
                if (!$ins) json_res(false, 'Prepare gagal (insert tugas NULL): ' . $db->error);
                $ins->bind_param("iiii", $siswa, $kelas, $mapel_id, $tugas);
            } else {
                $ins = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai) VALUES (?, ?, ?, ?, ?)");
                if (!$ins) json_res(false, 'Prepare gagal (insert tugas): ' . $db->error);
                $ins->bind_param("iiiii", $siswa, $kelas, $mapel_id, $tugas, $nilai);
            }
            if (!$ins->execute()) { $ins->close(); json_res(false, 'Execute gagal (insert tugas): ' . $ins->error); }
            $ins->close();
            json_res(true, 'inserted');
        }
    }

    // Simpan UTS / UAS pada tabel nilai
    if ($field === 'uts' || $field === 'uas') {
        // tentukan nilai mapel pada WHERE
        if ($mapel_col === 'mapel') {
            if ($mapel_is_id) {
                $mstmt = $db->prepare("SELECT nama FROM mapel WHERE id = ? LIMIT 1");
                if (!$mstmt) json_res(false, 'Prepare gagal (select mapel nama): ' . $db->error);
                $mstmt->bind_param("i", $mapel);
                if (!$mstmt->execute()) { $mstmt->close(); json_res(false,'Execute gagal (select mapel nama): '.$mstmt->error); }
                $mr = $mstmt->get_result()->fetch_assoc();
                $mapel_val = $mr['nama'] ?? '';
                $mstmt->close();
            } else {
                $mapel_val = $mapel;
            }
        } else {
            $mapel_val = intval($mapel);
        }

        if ($mapel_col === 'mapel_id') {
            $stmt = $db->prepare("SELECT id, uts, uas FROM nilai WHERE siswa_id=? AND kelas_id=? AND mapel_id=? LIMIT 1");
            if (!$stmt) json_res(false, 'Prepare gagal (select nilai row): ' . $db->error);
            $stmt->bind_param("iii", $siswa, $kelas, $mapel_val);
        } else {
            $stmt = $db->prepare("SELECT id, uts, uas FROM nilai WHERE siswa_id=? AND kelas_id=? AND mapel=? LIMIT 1");
            if (!$stmt) json_res(false, 'Prepare gagal (select nilai row): ' . $db->error);
            $stmt->bind_param("iis", $siswa, $kelas, $mapel_val);
        }
        if (!$stmt->execute()) { $stmt->close(); json_res(false, 'Execute gagal (select nilai row): ' . $stmt->error); }
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $nid = $row['id'];
            $cur_uts = $row['uts'];
            $cur_uas = $row['uas'];
            $stmt->close();
            if ($field === 'uts') $cur_uts = $nilai;
            if ($field === 'uas') $cur_uas = $nilai;
            // update tanpa asumsi kolom updated_at
            $u = $db->prepare("UPDATE nilai SET uts = ?, uas = ? WHERE id = ?");
            if (!$u) json_res(false, 'Prepare gagal (update nilai): ' . $db->error);
            $u->bind_param("iii", $cur_uts, $cur_uas, $nid);
            if (!$u->execute()) { $u->close(); json_res(false, 'Execute gagal (update nilai): ' . $u->error); }
            $u->close();
            json_res(true, 'updated');
        } else {
            $stmt->close();
            if ($mapel_col === 'mapel_id') {
                $ins = $db->prepare("INSERT INTO nilai (siswa_id, kelas_id, mapel_id, uts, uas) VALUES (?, ?, ?, ?, ?)");
                if (!$ins) json_res(false, 'Prepare gagal (insert nilai): ' . $db->error);
                $val_uts = ($field === 'uts') ? $nilai : null;
                $val_uas = ($field === 'uas') ? $nilai : null;
                $ins->bind_param("iiiii", $siswa, $kelas, $mapel_val, $val_uts, $val_uas);
            } else {
                $ins = $db->prepare("INSERT INTO nilai (siswa_id, kelas_id, mapel, uts, uas) VALUES (?, ?, ?, ?, ?)");
                if (!$ins) json_res(false, 'Prepare gagal (insert nilai): ' . $db->error);
                $val_uts = ($field === 'uts') ? $nilai : null;
                $val_uas = ($field === 'uas') ? $nilai : null;
                $ins->bind_param("iisii", $siswa, $kelas, $mapel_val, $val_uts, $val_uas);
            }
            if (!$ins->execute()) { $ins->close(); json_res(false, 'Execute gagal (insert nilai): ' . $ins->error); }
            $ins->close();
            json_res(true, 'inserted');
        }
    }

    json_res(false, 'Aksi tidak dikenali', ['field' => $field]);
} catch (\Throwable $e) {
    // kembalikan pesan error untuk debugging
    json_res(false, 'Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
}
?>
