<?php
session_start();
require 'db.php';

// Fetch categories
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Fetch featured gigs (highest rated)
$featured = $conn->query("SELECT g.*, u.username FROM gigs g JOIN users u ON g.seller_id = u.id ORDER BY g.rating DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

// Fetch trending gigs (most recent)
$trending = $conn->query("SELECT g.*, u.username FROM gigs g JOIN users u ON g.seller_id = u.id ORDER BY g.created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiverr Clone - Homepage</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #00796b; color: white; padding: 15px; text-align: center; border-radius: 10px; }
        .nav { margin: 20px 0; text-align: center; }
        .nav a { color: #00796b; text-decoration: none; margin: 0 15px; font-size: 18px; }
        .nav a:hover { color: #004d40; }
        .section { margin: 30px 0; }
        .section h2 { color: #00796b; font-size: 28px; text-align: center; }
        .gig-card { background: white; border-radius: 10px; padding: 15px; margin: 10px; display: inline-block; width: 30%; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .gig-card h3 { color: #004d40; margin: 10px 0; }
        .gig-card p { color: #555; }
        .search-bar { margin: 20px 0; text-align: center; }
        .search-bar input { padding: 10px; width: 50%; border: 2px solid #00796b; border-radius: 5px; font-size: 16px; }
        .search-bar button { padding: 10px 20px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .search-bar button:hover { background: #004d40; }
        @media (max-width: 768px) { .gig-card { width: 100%; } .search-bar input { width: 70%; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Fiverr Clone</h1>
            <div class="nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="#" onclick="redirect('profile.php')">Profile</a>
                    <a href="#" onclick="redirect('logout.php')">Logout</a>
                <?php else: ?>
                    <a href="#" onclick="redirect('login.php')">Login</a>
                    <a href="#" onclick="redirect('signup.php')">Signup</a>
                <?php endif; ?>
                <a href="#" onclick="redirect('create_gig.php')">Create Gig</a>
            </div>
        </div>
        <div class="search-bar">
            <input type="text" id="search" placeholder="Search for services...">
            <button onclick="searchGigs()">Search</button>
        </div>
        <div class="section">
            <h2>Categories</h2>
            <?php foreach ($categories as $cat): ?>
                <a href="#" onclick="filterCategory(<?php echo $cat['id']; ?>)" style="display: inline-block; padding: 10px; background: #00796b; color: white; margin: 5px; border-radius: 5px; text-decoration: none;"><?php echo $cat['name']; ?></a>
            <?php endforeach; ?>
        </div>
        <div class="section">
            <h2>Featured Gigs</h2>
            <?php foreach ($featured as $gig): ?>
                <div class="gig-card">
                    <h3><?php echo $gig['title']; ?></h3>
                    <p>By: <?php echo $gig['username']; ?></p>
                    <p><?php echo $gig['description']; ?></p>
                    <p>Price: $<?php echo $gig['price']; ?></p>
                    <p>Rating: <?php echo $gig['rating']; ?>/5</p>
                    <button onclick="redirect('order.php?gig_id=<?php echo $gig['id']; ?>')" style="padding: 10px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer;">Order Now</button>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="section">
            <h2>Trending Gigs</h2>
            <?php foreach ($trending as $gig): ?>
                <div class="gig-card">
                    <h3><?php echo $gig['title']; ?></h3>
                    <p>By: <?php echo $gig['username']; ?></p>
                    <p><?php echo $gig['description']; ?></p>
                    <p>Price: $<?php echo $gig['price']; ?></p>
                    <p>Rating: <?php echo $gig['rating']; ?>/5</p>
                    <button onclick="redirect('order.php?gig_id=<?php echo $gig['id']; ?>')" style="padding: 10px; background: #00796b; color: white; border: none; border-radius: 5px; cursor: pointer;">Order Now</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
        function searchGigs() {
            const query = document.getElementById('search').value;
            redirect('search.php?q=' + encodeURIComponent(query));
        }
        function filterCategory(catId) {
            redirect('search.php?category=' + catId);
        }
    </script>
</body>
</html>
