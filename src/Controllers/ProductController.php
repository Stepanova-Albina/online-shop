<?php

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
            $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
            $userId = $_SESSION['user_id'];
            $productId = $_POST['product_id'];
            $amount = $_POST['amount'];

            $stmt = $pdo->prepare("SELECT * FROM user_products WHERE product_id = :product_id AND user_id = :user_id");
            $stmt->execute(['product_id' => $productId, 'user_id' => $userId]);
            $product = $stmt->fetch();
            if ($product === false) {
                $stmt = $pdo->prepare("INSERT INTO user_products (user_id, product_id, amount) VALUES (:user_id, :product_id, :amount)");
                $stmt->execute(['user_id' => $userId, 'product_id' => $productId, 'amount' => $amount]);
                echo 'Продукт добавлен в корзину';
            } else {
                $amount = $product['amount'] + $amount;
                $stmt = $pdo->prepare("UPDATE user_products SET amount = :amount WHERE product_id = :product_id AND user_id = :user_id");
                $stmt->execute(['amount' => $amount, 'product_id' => $productId, 'user_id' => $userId]);
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

            $pdo = new PDO('pgsql:host=db;port=5432;dbname=mydb', 'user', 'pwd');
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :product_id");
            $stmt->execute(['product_id' => $productId]);
            $product = $stmt->fetch();

            if ($product === false) {
                $errors['product_id'] = 'Продукт не найден';
            }
        } else {
            $errors['product_id'] = 'Идентификатор продукта не заполнен';
        }
        if (isset($data['amount'])) {
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