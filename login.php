<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            echo "<script>window.location.href = 'index.php';</script>";
        } else {
            $error = "Invalid email or password";
        }
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
    <title>Login - Fiverr Clone</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(to right, #e0f7fa, #b2ebf2); margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        h2 { color: #00796b; text-align: center; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; color: #004d40; font-size: 18px; }
        .form-group input { width: 100%; padding: 10px; border: 2px solid #00796b; border-radius: 5px; font-size: 16px; }
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
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        <a href="#" onclick="redirect('signup.php')">Need an account? Signup</a>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
