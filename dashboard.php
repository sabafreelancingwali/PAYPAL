<?php
session_start();
if (!isset($_SESSION['user_id']) || (isset($_SESSION['2fa_verified']) && !$_SESSION['2fa_verified'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$user = $conn->query($sql)->fetch_assoc();
 
$sql_trans = "SELECT t.*, u_from.username AS from_user, u_to.username AS to_user 
              FROM transactions t 
              LEFT JOIN users u_from ON t.from_id = u_from.id 
              LEFT JOIN users u_to ON t.to_id = u_to.id 
              WHERE t.from_id = $user_id OR t.to_id = $user_id 
              ORDER BY t.created_at DESC LIMIT 10";
$transactions = $conn->query($sql_trans);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: var(--paypal-white);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .balance {
            text-align: center;
            font-size: 2em;
            color: var(--paypal-light-blue);
            margin-bottom: 20px;
        }
        .actions {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid var(--paypal-light-blue);
            text-align: left;
        }
        th {
            background: var(--paypal-blue);
            color: var(--paypal-white);
        }
        @media (max-width: 768px) {
            .actions {
                flex-direction: column;
            }
            .btn {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $user['username']; ?></h1>
    </header>
    <div class="container">
        <div class="balance">Balance: $<?php echo number_format($user['balance'], 2); ?></div>
        <div class="actions">
            <a href="add_funds.php" class="btn">Add Funds</a>
            <a href="send_money.php" class="btn">Send Money</a>
            <a href="enable_2fa.php" class="btn">Enable 2FA</a>
            <a href="logout.php" class="btn">Log Out</a>
        </div>
        <h2>Recent Transactions</h2>
        <table>
            <tr><th>Date</th><th>From</th><th>To</th><th>Amount</th><th>Description</th></tr>
            <?php while ($trans = $transactions->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $trans['created_at']; ?></td>
                    <td><?php echo $trans['from_user']; ?></td>
                    <td><?php echo $trans['to_user']; ?></td>
                    <td>$<?php echo number_format($trans['amount'], 2); ?></td>
                    <td><?php echo $trans['description']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
