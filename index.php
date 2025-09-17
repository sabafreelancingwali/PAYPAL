<?php
session_start();
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Clone - Homepage</title>
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
        }
        header {
            background: var(--paypal-blue);
            color: var(--paypal-white);
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        header h1 {
            margin: 0;
            font-size: 2.5em;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: var(--paypal-white);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .feature {
            background: var(--paypal-light-blue);
            color: var(--paypal-white);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        .feature h2 {
            font-size: 1.5em;
        }
        .btn {
            background: var(--paypal-accent);
            color: var(--paypal-white);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: var(--paypal-light-blue);
        }
        footer {
            text-align: center;
            padding: 10px;
            background: var(--paypal-blue);
            color: var(--paypal-white);
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        @media (max-width: 768px) {
            header h1 {
                font-size: 2em;
            }
            .container {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to PayPal Clone</h1>
        <p>Your secure online payment platform</p>
    </header>
    <div class="container">
        <h2>Our Features</h2>
        <div class="features">
            <div class="feature">
                <h2>Send Money</h2>
                <p>Transfer funds securely to anyone.</p>
            </div>
            <div class="feature">
                <h2>Receive Money</h2>
                <p>Get payments instantly.</p>
            </div>
            <div class="feature">
                <h2>Manage Wallet</h2>
                <p>Track your balance and history.</p>
            </div>
            <div class="feature">
                <h2>Secure Transactions</h2>
                <p>With encryption and 2FA.</p>
            </div>
        </div>
        <a href="signup.php" class="btn">Sign Up</a>
        <a href="login.php" class="btn">Log In</a>
    </div>
    <footer>&copy; 2025 PayPal Clone</footer>
</body>
</html>
