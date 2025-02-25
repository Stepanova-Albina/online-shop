<?php
namespace Controller;

use Model\Cart;
use Model\Product;

class ProductController
{
    public function getCatalog()
    {
        require_once '../Views/catalog.php';
    }

    public function addProduct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $errors = $this->validateAddProduct($_POST);

        if (empty($errors)) {
            $userId = $_SESSION['user_id'];
            $productId = $_POST['product_id'];
            $amount = $_POST['amount'];


            $cartModel = new Cart();
            $product = $cartModel->getProductById($productId, $userId);
            if ($product === false) {
                $cartModel->insertAll($userId, $productId, $amount);
                echo 'Продукт добавлен в корзину';
            } else {
                $amount = $product['amount'] + $amount;
                $cartModel->updateAmount($amount, $productId, $userId);
                echo 'Количество продуктов увеличено';
            }
        }
        require_once '../Views/catalog.php';
    }

    private function validateAddProduct(array $data): array
    {
        $errors = [];
        if (isset($data['product_id'])) {
            $productId = (int)$data['product_id'];


            $productModel = new Product();
            $product = $productModel->getById($productId);

            if ($product === false) {
                $errors['product_id'] = 'Продукт не найден';
            }
        } else {
            $errors['product_id'] = 'Идентификатор продукта не заполнен';
        }
        if (!empty($data['amount'])) {
            $amount = (int)$data['amount'];

            if ($amount < 0 || $amount > 100) {
                $errors['amount'] = 'Количество некорректно';
            }
        } else {
            $errors['amount'] = 'Количество не заполнено';
        }
        return $errors;
    }
}