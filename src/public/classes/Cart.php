<?php
class Cart
{
    public function getCart()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
        $stmtUserProducts= $pdo->query("SELECT * FROM user_products WHERE user_id = $userId");
        $userProducts = $stmtUserProducts->fetchAll();

        $count = 0;
        foreach ($userProducts as $userProduct) {
            $productId = $userProduct['product_id'];

            $stmtProducts = $pdo->query("SELECT * FROM products WHERE id = $productId");
            $products[$count] = $stmtProducts->fetch();
            $products[$count]['amount'] = $userProduct['amount'];
            $count++;
        }
        require_once './pages/cart_page.php';

    }
}