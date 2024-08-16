<?php
namespace App\Database\Models;
use Database\Models\DatabaseModel;
use PDO;
class Product extends DatabaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'products');
    }

    public function create(array $data) {
        $stmt = $this->executeStatement(
            "INSERT INTO {$this->table} (id, name, inStock, description, category_id, brand) 
             VALUES (:id, :name, :inStock, :description, :category_id, :brand)",
            $data
        );
        return $data['id'];
    }

    public function update($id, array $data) {
        $data['id'] = $id;
        return $this->executeStatement(
            "UPDATE {$this->table} 
             SET name = :name, inStock = :inStock, description = :description, 
                 category_id = :category_id, brand = :brand 
             WHERE id = :id",
            $data
        )->rowCount() > 0;
    }

    public function getPrices($id) {
        return $this->executeStatement(
            "SELECT * FROM prices WHERE product_id = :id",
            ['id' => $id]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAttributes($id) {
        return $this->executeStatement(
            "SELECT * FROM attributes WHERE product_id = :id",
            ['id' => $id]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGallery($id) {
        return $this->executeStatement(
            "SELECT * FROM gallery WHERE product_id = :id",
            ['id' => $id]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getGalleryOne($id) {
        return $this->executeStatement(
            "SELECT * FROM gallery WHERE product_id = :id Limit 1",
            ['id' => $id]
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}
