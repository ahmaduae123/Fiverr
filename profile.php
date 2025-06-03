<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$gigs = $conn->query("SELECT * FROM gigs WHERE seller_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);
$orders = $conn->query("SELECT o.*, g.title FROM orders o JOIN gigs g ON o.gig_id = g.id WHERE o.buyer_id = $user_id OR g.seller_id = $user_id")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $gig_id = $_POST['gig_id'];
    if ($_POST['action'] == 'delete') {
        $conn->prepare("DELETE FROM gigs WHERE id = ? AND seller_id = ?")->execute([$gig_id, $user_id]);
    } elseif ($_POST['action'] == 'update_order') {
        $status = $_POST['status'];
        $conn->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$status, $_POST['order_id']]);
    }
    echo "<script>window.location.href = 'profile.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fiverr Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 800px; margin: 50px auto; padding: 20px; }
        h2 { color: #00796b; text-align: center; }
        .gig-card, .order-card { background: white; border-radius: 10px; padding: 15px; margin: 10px 0; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .gig-card h3, .order-card h3 { color: #004d40; margin: 10px 0; }
        .gig-card p, .order-card p { color: #555; }
        button { padding: 10px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #004d40; }
        select { padding: 5px; border: 2px solid #00796b; border-radius: 5px; }
        a { color: #00796b; text-decoration: none; display: block; text-align: center; margin: 10px 0; }
        a:hover { color: #004d40; }
        @media (max-width: 768px) { .container { width: 90%; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Profile</h2>
        <a href="#" onclick="redirect('create_gig.php')">Create New Gig</a>
        <h2>Your Gigs</h2>
        <?php foreach ($gigs as $gig): ?>
            <div class="gig-card">
                <h3><?php echo $gig['title']; ?></h3>
                <p><?php echo $gig['description']; ?></p>
                <p>Price: $<?php echo $gig['price']; ?></p>
                <button onclick="if(confirm('Delete this gig?')) { document.getElementById('delete-<?php echo $gig['id']; ?>').submit(); }">Delete</button>
                <form id="delete-<?php echo $gig['id']; ?>" method="POST" style="display: none;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="gig_id" value="<?php echo $gig['id']; ?>">
                </form>
            </div>
        <?php endforeach; ?>
        <h2>Your Orders</h2>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <h3>Order for: <?php echo $order['title']; ?></h3>
                <p>Status: <?php echo $order['status']; ?></p>
                <button onclick="redirect('message.php?order_id=<?php echo $order['id']; ?>')">Message</button>
                <?php if ($_SESSION['role'] == 'seller'): ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="update_order">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status">
                            <option value="pending">Pending</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                        <button type="submit">Update Status</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <a href="#" onclick="redirect('index.php')">Back to Home</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
