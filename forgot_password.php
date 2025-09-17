<?php
include 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES ('$email', '$token', '$expires')";
        $conn->query($sql);
        $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
        $message = "Click here to reset password: $reset_link";
        mail($email, "Password Reset", $message);  // Configure mail server
        echo "<script>alert('Reset link sent to email.');</script>";
    } else {
        echo "<script>alert('Email not found.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
        a {
            text-align: center;
            display: block;
            margin-top: 10px;
            color: var(--paypal-light-blue);
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
        <h2>Forgot Password</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        <a href="login.php">Back to Log In</a>
    </div>
</body>
</html>
