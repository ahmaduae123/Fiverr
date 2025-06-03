<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $seller_id = $_SESSION['user_id'];

    try {
        $stmt = $conn->prepare("INSERT INTO gigs (seller_id, title, description, category_id, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$seller_id, $title, $description, $category_id, $price]);
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
    <title>Create Gig - Fiverr Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        h2 { color: #00796b; text-align: center; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; color: #004d40; font-size: 18px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 2px solid #00796b; border-radius: 5px; font-size: 16px; }
        .form-group button { width: 100%; padding: 12px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 18px; }
        .form-group button:hover { background: #004d40; }
        .error { color: red; text-align: center; }
        a { color: #00796b; text-decoration: none; display: block; text-align: center; margin-top: 10px; }
        a:hover { color: #004d40; }
        @media (max-width: 768px) { .container { width: 90%; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create a Gig</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price ($)</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <button type="submit">Create Gig</button>
            </div>
        </form>
        <a href="#" onclick="redirect('profile.php')">Back to Profile</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
