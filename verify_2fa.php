<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
// Simple TOTP implementation (requires base32 decode/encode, HMAC-SHA1)
// Note: This is a basic implementation; in production, use a library like php-google-authenticator
function verify_totp($secret, $code) {
    $secret = base32_decode($secret);
    $time = floor(time() / 30);
    for ($i = -1; $i <= 1; $i++) {
        $hash = hash_hmac('sha1', pack('J', $time + $i), $secret, true);
        $offset = ord(substr($hash, -1)) & 0xF;
        $truncated = (ord(substr($hash, $offset, 1)) & 0x7F) << 24 |
                     (ord(substr($hash, $offset + 1, 1)) & 0xFF) << 16 |
                     (ord(substr($hash, $offset + 2, 1)) & 0xFF) << 8 |
                     (ord(substr($hash, $offset + 3, 1)) & 0xFF);
        $truncated %= 1000000;
        if ($truncated == (int)$code) {
            return true;
        }
    }
    return false;
}
 
function base32_decode($base32) {
    $lut = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $base32 = strtoupper($base32);
    $output = '';
    $v = 0;
    $vbits = 0;
    for ($i = 0; $i < strlen($base32); $i++) {
        $c = $base32[$i];
        if ($c == '=') break;
        $n = strpos($lut, $c);
        if ($n === false) continue;
        $v = ($v << 5) | $n;
        $vbits += 5;
        while ($vbits >= 8) {
            $output .= chr($v >> ($vbits - 8));
            $vbits -= 8;
            $v &= (1 << $vbits) - 1;
        }
    }
    return $output;
}
 
$user_id = $_SESSION['user_id'];
$sql = "SELECT twofa_secret FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];
    if (verify_totp($user['twofa_secret'], $code)) {
        $_SESSION['2fa_verified'] = true;
        echo "<script>window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid 2FA code.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify 2FA</title>
    <style>
        :root {
            --paypal-blue: #003087;
            --paypal-light-blue: #0070ba;
            --paypal-white: #ffffff;
            --paypal-gray: #f5f5f5;
            --paypal-accent: #009cde;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--paypal-gray);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: var(--paypal-white);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: var(--paypal-blue);
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid var(--paypal-light-blue);
            border-radius: 5px;
            font-size: 1em;
        }
        .btn {
            background: var(--paypal-accent);
            color: var(--paypal-white);
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: var(--paypal-light-blue);
        }
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Verify 2FA</h2>
        <form method="POST">
            <input type="text" name="code" placeholder="Enter 6-digit code" required>
            <button type="submit" class="btn">Verify</button>
        </form>
    </div>
</body>
</html>
