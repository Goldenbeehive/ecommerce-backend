<?php
namespace App\Database\Models;
use Database\Models\DatabaseModel;
use PDO;
class Price extends DatabaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'prices');
    }

    public function create(array $data) {
        $stmt = $this->executeStatement(
            "INSERT INTO {$this->table} (product_id, amount, currency) 
             VALUES (:product_id, :amount, :currency)",
            $data
        );
        return $this->db->lastInsertId();
    }
    public function getByProductId($id)  {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE product_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update($id, array $data) {
        $data['id'] = $id;
        return $this->executeStatement(
            "UPDATE {$this->table} 
             SET product_id = :product_id, amount = :amount, currency = :currency 
             WHERE id = :id",
            $data
        )->rowCount() > 0;
    }
}