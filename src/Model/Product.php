<?php

require_once '../Model/Model.php';
class Product extends Model
{
    public function getById($productId): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :product_id");
        $stmt->execute(['product_id' => $productId]);
        $result = $stmt->fetch();
        return $result;
    }
}