<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
$userId = $_SESSION['user_id'];

$pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
$stmt = $pdo->query("SELECT * FROM users WHERE id = $userId");
$user = $stmt->fetch();

require_once './profile/profile_page.php';


