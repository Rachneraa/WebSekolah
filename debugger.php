<?php
// Lightweight debugger page for local/testing only
// USAGE: /debugger.php?key=your_key

session_start();

// Change this key before uploading to a public server
$DEBUG_KEY = 'localdev_debug_key_change_me';

$provided = isset($_GET['key']) ? $_GET['key'] : null;
$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

// Allow access from localhost without key
$allow_local = ($remote === '127.0.0.1' || $remote === '::1');

if (!($allow_local || ($provided && hash_equals($DEBUG_KEY, $provided)))) {
    http_response_code(403);
    echo "<h2>Access denied</h2><p>Debugger is protected. Access from localhost or provide correct <code>?key=</code>.</p>";
    exit;
}

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// load config if present
$db_ok = false;
$db = null;
if (file_exists(__DIR__ . '/config/koneksi.php')) {
    include __DIR__ . '/config/koneksi.php';
    if (isset($db) && $db && function_exists('mysqli_ping')) {
        $db_ok = @mysqli_ping($db);
    }
}

?><!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Debugger</title>
    <style>
        body {
            font-family: system-ui, Segoe UI, Roboto, Helvetica, Arial;
            margin: 18px
        }

        pre {
            background: #f6f8fa;
            padding: 10px;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <h1>App Debugger</h1>
    <p>Access: <strong><?php echo h($allow_local ? 'localhost' : $remote); ?></strong></p>
    <ul>
        <li><a href="?key=<?php echo urlencode($provided ?: $DEBUG_KEY); ?>&action=phpinfo">Show phpinfo()</a></li>
        <li><a href="?key=<?php echo urlencode($provided ?: $DEBUG_KEY); ?>&action=dbtest">Test DB connection</a></li>
        <li><a href="?key=<?php echo urlencode($provided ?: $DEBUG_KEY); ?>&action=recent">Show recent registrants</a>
        </li>
        <li>Test NISN check (server-side):
            <form style="display:inline" method="get">
                <input type="hidden" name="key" value="<?php echo h($provided ?: $DEBUG_KEY); ?>">
                <input type="hidden" name="action" value="nisn_test">
                <input name="nisn" placeholder="Masukkan NISN" value="2121212121">
                <button type="submit">Test</button>
            </form>
        </li>
    </ul>
    <hr />
    <?php
    if ($action === 'phpinfo') {
        phpinfo();
        exit;
    }

    if ($action === 'dbtest') {
        echo '<h2>DB Connection Test</h2>';
        if (isset($db) && $db) {
            if ($db_ok) {
                echo '<p style="color:green">Connected to database (mysqli_ping OK).</p>';
                // show server version
                $vers = mysqli_get_server_info($db);
                echo '<p>MySQL server: ' . h($vers) . '</p>';
            } else {
                echo '<p style="color:red">Connection object exists but ping failed.</p>';
                if (function_exists('mysqli_connect_errno')) {
                    echo '<pre>' . h(mysqli_connect_error()) . '</pre>';
                }
            }
        } else {
            echo '<p style="color:red">config/koneksi.php not found or did not expose $db.</p>';
        }
    }

    if ($action === 'recent') {
        echo '<h2>Recent Registrants</h2>';
        if (isset($db) && $db) {
            $sql = "SELECT id,nama_lengkap,nisn,created_at FROM ppdb_pendaftar ORDER BY created_at DESC LIMIT 10";
            if ($res = mysqli_query($db, $sql)) {
                echo '<table border="1" cellpadding="6" cellspacing="0"><tr><th>ID</th><th>Nama</th><th>NISN</th><th>Created</th></tr>';
                while ($row = mysqli_fetch_assoc($res)) {
                    echo '<tr><td>' . h($row['id']) . '</td><td>' . h($row['nama_lengkap']) . '</td><td>' . h($row['nisn']) . '</td><td>' . h($row['created_at']) . '</td></tr>';
                }
                echo '</table>';
            } else {
                echo '<pre>Query error: ' . h(mysqli_error($db)) . '</pre>';
            }
        } else {
            echo '<p style="color:red">No DB available.</p>';
        }
    }

    if ($action === 'nisn_test') {
        $nisn = isset($_GET['nisn']) ? trim($_GET['nisn']) : '';
        echo '<h2>Server-side NISN check</h2>';
        if ($nisn === '') {
            echo '<p style="color:orange">No nisn provided.</p>';
        } else {
            $url = (isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] : '') . dirname($_SERVER['PHP_SELF']) . '/backend/modules/cek_nisn.php';
            // try curl
            if (function_exists('curl_version')) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['nisn' => $nisn]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                $resp = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                if ($resp === false) {
                    echo '<pre>CURL error: ' . h($err) . '</pre>';
                } else {
                    echo '<pre>POST ' . h($url) . "\nResponse:\n" . h($resp) . '</pre>';
                }
            } else {
                // fallback to file_get_contents
                $opts = ['http' => ['method' => 'POST', 'header' => 'Content-type: application/x-www-form-urlencoded', 'content' => http_build_query(['nisn' => $nisn]), 'timeout' => 5]];
                $context = stream_context_create($opts);
                $resp = @file_get_contents($url, false, $context);
                if ($resp === false) {
                    echo '<pre>Request failed to ' . h($url) . '</pre>';
                } else {
                    echo '<pre>POST ' . h($url) . "\nResponse:\n" . h($resp) . '</pre>';
                }
            }
        }
    }

    echo '<hr/><p>Remember to change <code>$DEBUG_KEY</code> or remove this file before deploying to production.</p>';

    ?>