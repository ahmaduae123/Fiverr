<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

if (!isset($_GET['gig_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

$gig_id = $_GET['gig_id'];
$gig = $conn->query("SELECT g.*, u.username FROM gigs g JOIN users u ON g.seller_id = u.id WHERE g.id = $gig_id")->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $buyer_id = $_SESSION['user_id'];
    try {
        $stmt = $conn->prepare("INSERT INTO orders (buyer_id, gig_id) VALUES (?, ?)");
        $stmt->execute([$buyer_id, $gig_id]);
        echo "<script>window.location.href = 'profile.php';</script>";
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
    <title>Order Gig - Fiverr Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        h2 { color: #00796b; text-align: center; }
        .gig-card p { color: #555; }
        .gig-card h3 { color: #004d40; margin: 10px 0; }
        button { padding: 12px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 18px; }
        button:hover { background: #004d40; }
        .error { color: red; text-align: center; }
        a { color: #00796b; text-decoration: none; display: block; text-align: center; margin: 10px 0; }
        a:hover { color: #004d40; }
        @media (max-width: 768px) { .container { width: 90%; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order Gig</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <div class="gig-card">
            <h3><?php echo $gig['title']; ?></h3>
            <p>By: <?php echo $gig['username']; ?></p>
            <p><?php echo $gig['description']; ?></p>
            <p>Price: $<?php echo $gig['price']; ?></p>
            <p>Rating: <?php echo $gig['rating']; ?>/5</p>
            <form method="POST">
                <button type="submit">Place Order</button>
            </form>
        </div>
        <a href="#" onclick="redirect('index.php')">Back to Home</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
