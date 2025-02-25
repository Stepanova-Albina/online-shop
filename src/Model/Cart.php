<?php
namespace Model;
class Cart extends Model
{
    public function getProductById(int $productId, int $userId): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_products WHERE product_id = :product_id AND user_id = :user_id");
        $stmt->execute(['product_id' => $productId, 'user_id' => $userId]);
        $result = $stmt->fetch();
        return $result;
    }

    public function insertAll($userId, $productId, $amount)
    {
        $stmt = $this->pdo->prepare("INSERT INTO user_products (user_id, product_id, amount) VALUES (:user_id, :product_id, :amount)");
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId, 'amount' => $amount]);
    }

    public function updateAmount($amount, $productId, $userId)
    {
        $stmt = $this->pdo->prepare("UPDATE user_products SET amount = :amount WHERE product_id = :product_id AND user_id = :user_id");
        $stmt->execute(['amount' => $amount, 'product_id' => $productId, 'user_id' => $userId]);
    }
    public function getAll(int $userId): array|false
    {
        $stmt = $this->pdo->query("SELECT * FROM user_products WHERE user_id = $userId");
        $result = $stmt->fetchAll();
        return $result;
    }
}