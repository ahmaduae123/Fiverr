<?php
session_start();
require 'db.php';

$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$where = "1=1";
$params = [];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $where .= " AND title LIKE ?";
    $params[] = "%" . $_GET['q'] . "%";
}
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $where .= " AND category_id = ?";
    $params[] = $_GET['category'];
}
if (isset($_GET['price_min']) && !empty($_GET['price_min'])) {
    $where .= " AND price >= ?";
    $params[] = $_GET['price_min'];
}
if (isset($_GET['price_max']) && !empty($_GET['price_max'])) {
    $where .= " AND price <= ?";
    $params[] = $_GET['price_max'];
}

$stmt = $conn->prepare("SELECT g.*, u.username FROM gigs g JOIN users u ON g.seller_id = u.id WHERE $where");
$stmt->execute($params);
$gigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Gigs - Fiverr Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 50px auto; padding: 20px; }
        h2 { color: #00796b; text-align: center; }
        .filter { margin: 20px 0; }
        .filter label { color: #004d40; font-size: 18px; margin-right: 10px; }
        .filter input, .filter select { padding: 10px; border: 2px solid #00796b; border-radius: 5px; font-size: 16px; }
        .filter button { padding: 10px 20px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .filter button:hover { background: #004d40; }
        .gig-card { background: white; border-radius: 10px; padding: 15px; margin: 10px 0; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .gig-card h3 { color: #004d40; margin: 10px 0; }
        .gig-card p { color: #555; }
        .gig-card button { padding: 10px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .gig-card button:hover { background: #004d40; }
        a { color: #00796b; text-decoration: none; display: block; text-align: center; margin: 10px 0; }
        a:hover { color: #004d40; }
        @media (max-width: 768px) { .container { width: 90%; } .filter input, .filter select { width: 100%; margin: 5px 0; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Gigs</h2>
        <div class="filter">
            <label>Search:</label>
            <input type="text" id="q" value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>">
            <label>Category:</label>
            <select id="category">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <label>Price Min:</label>
            <input type="number" id="price_min" value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : ''; ?>">
            <label>Price Max:</label>
            <input type="number" id="price_max" value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : ''; ?>">
            <button onclick="filterGigs()">Filter</button>
        </div>
        <?php foreach ($gigs as $gig): ?>
            <div class="gig-card">
                <h3><?php echo $gig['title']; ?></h3>
                <p>By: <?php echo $gig['username']; ?></p>
                <p><?php echo $gig['description']; ?></p>
                <p>Price: $<?php echo $gig['price']; ?></p>
                <p>Rating: <?php echo $gig['rating']; ?>/5</p>
                <button onclick="redirect('order.php?gig_id=<?php echo $gig['id']; ?>')">Order Now</button>
            </div>
        <?php endforeach; ?>
        <a href="#" onclick="redirect('index.php')">Back to Home</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
        function filterGigs() {
            const q = document.getElementById('q').value;
            const category = document.getElementById('category').value;
            const price_min = document.getElementById('price_min').value;
            const price_max = document.getElementById('price_max').value;
            let url = 'search.php?';
            if (q) url += 'q=' + encodeURIComponent(q) + '&';
            if (category) url += 'category=' + category + '&';
            if (price_min) url += 'price_min=' + price_min + '&';
            if (price_max) url += 'price_max=' + price_max;
            redirect(url);
        }
    </script>
</body>
</html>
