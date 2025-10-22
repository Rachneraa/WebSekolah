<?php
function getBerita($db, $limit = null, $kategori = null)
{
    try {
        $sql = "SELECT * FROM berita";
        if ($kategori) {
            $sql .= " WHERE kategori = '" . mysqli_real_escape_string($db, $kategori) . "'";
        }
        $sql .= " ORDER BY tanggal DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $result = mysqli_query($db, $sql);
        if (!$result) {
            return [];
        }

        $berita = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $berita[] = $row;
        }
        return $berita;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return [];
    }
}

function getBeritaById($db, $id)
{
    try {
        $id = (int) $id;
        $sql = "SELECT * FROM berita WHERE id = $id";
        $result = mysqli_query($db, $sql);
        if (!$result) {
            return null;
        }
        return mysqli_fetch_assoc($result);
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}