<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float)$_POST['amount'];
    $user_id = $_SESSION['user_id'];
    $sql = "UPDATE users SET balance = balance + $amount WHERE id = $user_id";
    if ($conn->query($sql) === TRUE) {
        // Dummy transaction for history (self-add)
        $sql_trans = "INSERT INTO transactions (from_id, to_id, amount, description) VALUES ($user_id, $user_id, $amount, 'Added funds')";
        $conn->query($sql_trans);
        echo "<script>alert('Funds added!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding funds.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Funds</title>
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
        <h2>Add Funds (Dummy)</h2>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" step="0.01" required>
            <button type="submit" class="btn">Add</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
