<?php
namespace Controller;

use Model\Cart;
use Model\Product;
class CartController
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


        $cartModel = new Cart();
        $userProducts = $cartModel->getAll($userId);

        $count = 0;
        foreach ($userProducts as $userProduct) {
            $productId = $userProduct['product_id'];


            $productModel = new Product();

            $products[$count] = $productModel->getById($productId);
            $products[$count]['amount'] = $userProduct['amount'];
            $count++;
        }
        require_once '../Views/cart.php';

    }
}