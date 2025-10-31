<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() == PHP_SESSION_NONE) session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/config/koneksi.php';

$kelas = isset($_POST['kelas']) ? intval($_POST['kelas']) : 0;
$mapel = isset($_POST['mapel']) ? intval($_POST['mapel']) : 0;

if (!$kelas || !$mapel) {
    echo json_encode(['success' => false, 'msg' => 'Parameter kelas/mapel wajib', 'posted' => $_POST]);
    exit;
}

$stmt = $db->prepare("SELECT COALESCE(MAX(tugas_ke),0) as max_t FROM nilai_tugas WHERE kelas_id=? AND mapel_id=?");
if (!$stmt) { echo json_encode(['success'=>false,'msg'=>'Prepare failed (max_t)','error'=>$db->error]); exit; }
$stmt->bind_param("ii", $kelas, $mapel);
if (!$stmt->execute()) { echo json_encode(['success'=>false,'msg'=>'Execute failed (max_t)','error'=>$stmt->error]); $stmt->close(); exit; }
$stmt->bind_result($max_t);
$stmt->fetch();
$stmt->close();
$new = intval($max_t) + 1;

$siswa = [];
$sq = $db->prepare("SELECT siswa_id FROM siswa WHERE kelas_id = ?");
if (!$sq) { echo json_encode(['success'=>false,'msg'=>'Prepare failed (select siswa)','error'=>$db->error]); exit; }
$sq->bind_param("i", $kelas);
if (!$sq->execute()) { echo json_encode(['success'=>false,'msg'=>'Execute failed (select siswa)','error'=>$sq->error]); $sq->close(); exit; }
$res = $sq->get_result();
while ($r = $res->fetch_assoc()) $siswa[] = $r['siswa_id'];
$sq->close();

if (empty($siswa)) { echo json_encode(['success'=>false,'msg'=>'Tidak ada siswa di kelas ini']); exit; }

$db->begin_transaction();
$ok_all = true;
$ins = $db->prepare("INSERT INTO nilai_tugas (siswa_id, kelas_id, mapel_id, tugas_ke, nilai) VALUES (?, ?, ?, ?, NULL)");
if (!$ins) { echo json_encode(['success'=>false,'msg'=>'Prepare failed (insert)','error'=>$db->error]); $db->rollback(); exit; }
foreach ($siswa as $sid) {
    $ins->bind_param("iiii", $sid, $kelas, $mapel, $new);
    if (!$ins->execute()) { $ok_all = false; $last_error = $ins->error; break; }
}
$ins->close();

if ($ok_all) { $db->commit(); echo json_encode(['success'=>true, 'tugas_ke'=>$new, 'msg'=>'Tugas berhasil dibuat']); }
else { $db->rollback(); echo json_encode(['success'=>false, 'msg'=>'Gagal insert beberapa baris','error'=>$last_error ?? $db->error]); }
exit;
?>