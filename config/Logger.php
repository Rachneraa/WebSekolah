<?php

class Logger
{
    private $db;
    private $userId;

    public function __construct($db, $userId)
    {
        $this->db = $db;
        $this->userId = $userId;
    }

    public function log($aktivitas, $tabel, $recordId = null, $detail = null)
    {
        $query = "INSERT INTO logs (user_id, aktivitas, tabel, record_id, detail, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $this->db->error);
        }

        $stmt->bind_param(
            "issis",
            $this->userId,
            $aktivitas,
            $tabel,
            $recordId,
            $detail
        );

        if (!$stmt->execute()) {
            throw new Exception("Error logging activity: " . $stmt->error);
        }

        $stmt->close();
        return true;
    }

    public function getLogs($limit = 10)
    {
        $query = "SELECT l.*, u.username 
                 FROM logs l
                 JOIN users u ON l.user_id = u.id
                 ORDER BY l.created_at DESC 
                 LIMIT ?";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}