<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
// Basic 2FA enable: Generate secret and show QR (text-based for simplicity)
function base32_encode($data) {
    $lut = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $output = '';
    $v = 0;
    $vbits = 0;
    for ($i = 0; $i < strlen($data); $i++) {
        $v = ($v << 8) | ord($data[$i]);
        $vbits += 8;
        while ($vbits >= 5) {
            $output .= $lut[$v >> ($vbits - 5)];
            $vbits -= 5;
            $v &= (1 << $vbits) - 1;
        }
    }
    if ($vbits > 0) {
        $output .= $lut[$v << (5 - $vbits)];
    }
    return $output;
}
 
$user_id = $_SESSION['user_id'];
$sql = "SELECT twofa_secret FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
 
if (empty($user['twofa_secret'])) {
    $secret = base32_encode(random_bytes(10));  // 160 bits
    $sql = "UPDATE users SET twofa_secret = '$secret' WHERE id = $user_id";
    $conn->query($sql);
} else {
    $secret = $user['twofa_secret'];
}
 
// For QR, in real use Google Chart API or library; here show secret
$qr_url = "otpauth://totp/PayPalClone:{$_SESSION['username']}?secret=$secret&issuer=PayPalClone";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enable 2FA</title>
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
        .container {
            background: var(--paypal-white);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: var(--paypal-blue);
        }
        .qr {
            margin: 20px 0;
        }
        .btn {
            background: var(--paypal-accent);
            color: var(--paypal-white);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: var(--paypal-light-blue);
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enable 2FA</h2>
        <p>Scan this QR with your authenticator app or enter the secret: <?php echo $secret; ?></p>
        <img class="qr" src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=<?php echo urlencode($qr_url); ?>" alt="QR Code">
        <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>
