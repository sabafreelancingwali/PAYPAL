<?php
include 'db.php';
 
if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $sql = "SELECT email FROM password_resets WHERE token = '$token' AND expires_at > NOW()";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE users SET password = '$password' WHERE email = '$email'";
            $conn->query($sql);
            $sql = "DELETE FROM password_resets WHERE token = '$token'";
            $conn->query($sql);
            echo "<script>alert('Password reset successful!'); window.location.href = 'login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid or expired token.');</script>";
    }
} else {
    echo "<script>window.location.href = 'login.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        <h2>Reset Password</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit" class="btn">Reset</button>
        </form>
    </div>
</body>
</html>
