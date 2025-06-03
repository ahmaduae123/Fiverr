<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$order_id = $_GET['order_id'];
$order = $conn->query("SELECT o.*, g.title, g.seller_id, o.buyer_id FROM orders o JOIN gigs g ON o.gig_id = g.id WHERE o.id = $order_id")->fetch(PDO::FETCH_ASSOC);
if (!$order || ($order['buyer_id'] != $_SESSION['user_id'] && $order['seller_id'] != $_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

$messages = $conn->query("SELECT m.*, u.username FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.order_id = $order_id ORDER BY m.created_at")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id'];
    $receiver_id = ($_SESSION['user_id'] == $order['buyer_id']) ? $order['seller_id'] : $order['buyer_id'];
    try {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, order_id, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sender_id, $receiver_id, $order_id, $message]);
        echo "<script>window.location.href = 'message.php?order_id=$order_id';</script>";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Fiverr Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; }
        h2 { color: #00796b; text-align: center; }
        .message-box { background: white; border-radius: 10px; padding: 15px; margin: 10px 0; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .message-box p { color: #555; }
        .message-box .sender { color: #00796b; font-weight: bold; }
        .form-group { margin: 15px 0; }
        .form-group textarea { width: 100%; padding: 10px; border: 2px solid #00796b; border-radius: 5px; font-size: 16px; }
        .form-group button { width: 100%; padding: 12px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; }
        .form-group button:hover { background: #004d40; }
        .error { color: red; text-align: center; }
        a { color: #00796b; text-decoration: none; display: block; text-align: center; margin: 10px 0; }
        a:hover { color: #004d40; }
        @media (max-width: 768px) { .container { width: 90%; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Messages for Order: <?php echo $order['title']; ?></h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-box">
                <p class="sender"><?php echo $msg['username']; ?>:</p>
                <p><?php echo $msg['message']; ?></p>
                <p style="font-size: 12px; color: #777;"><?php echo $msg['created_at']; ?></p>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <textarea name="message" rows="5" placeholder="Type your message..." required></textarea>
            <button onclick="document.forms[0].submit()">Send Message</button>
        </div>
        <a href="#" onclick="redirect('profile.php')">Back to Profile</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
