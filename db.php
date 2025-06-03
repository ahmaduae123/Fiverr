<?php
$host = 'localhost'; // Adjust if your host is different (e.g., a specific server address)
$dbUser = 'urnrgaote95vf';
$dbPass = 'tgk9ztof7xb1';
$dbName = 'dbgypsegw5lydd';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
