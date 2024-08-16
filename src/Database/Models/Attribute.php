<?php
namespace App\Database\Models;
use Database\Models\DatabaseModel;
use PDO;
class Attribute extends DatabaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'attributes');
    }

    public function create(array $data) {
        $data['items'] = json_encode($data['items']);
        $stmt = $this->executeStatement(
            "INSERT INTO {$this->table} (product_id, name, type, items) 
             VALUES (:product_id, :name, :type, :items)",
            $data
        );
        return $this->db->lastInsertId();
    }
    public function getByProductId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE product_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function update($id, array $data) {
        $data['id'] = $id;
        $data['items'] = json_encode($data['items']);
        return $this->executeStatement(
            "UPDATE {$this->table} 
             SET product_id = :product_id, name = :name, type = :type, items = :items 
             WHERE id = :id",
            $data
        )->rowCount() > 0;
    }
}
