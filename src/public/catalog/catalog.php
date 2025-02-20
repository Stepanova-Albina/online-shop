<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$userId = $_SESSION['user_id'];

$pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll();


require_once './catalog/catalog_page.php';
?>