<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $amount = (float)$_POST['amount'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $from_id = $_SESSION['user_id'];
 
    $sql = "SELECT id, email FROM users WHERE email = '$identifier' OR username = '$identifier'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $to_user = $result->fetch_assoc();
        $to_id = $to_user['id'];
        if ($to_id != $from_id) {
            try {
                $sql_trans = "INSERT INTO transactions (from_id, to_id, amount, description) VALUES ($from_id, $to_id, $amount, '$description')";
                $conn->query($sql_trans);
                // Email notification
                $message = "You received $$amount from {$_SESSION['username']}. Description: $description";
                mail($to_user['email'], "Money Received", $message);
                echo "<script>alert('Money sent!'); window.location.href = 'dashboard.php';</script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
            }
        } else {
            echo "<script>alert('Cannot send to yourself.');</script>";
        }
    } else {
        echo "<script>alert('Recipient not found.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Money</title>
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
        input, textarea {
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
        <h2>Send Money</h2>
        <form method="POST">
            <input type="text" name="identifier" placeholder="Recipient Email or Username" required>
            <input type="number" name="amount" placeholder="Amount" step="0.01" required>
            <textarea name="description" placeholder="Description (optional)"></textarea>
            <button type="submit" class="btn">Send</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
