<?php

namespace App\Database\Models;

use Database\Models\DatabaseModel;
use PDO;

class OrderDetails extends DatabaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'order_details');
    }

    public function create(array $data) {
        $stmt = $this->executeStatement(
            "INSERT INTO {$this->table} (order_id, product_id, quantity, selected_attributes) 
             VALUES (:order_id, :product_id, :quantity, :selected_attributes)",
            $data
        );
        $id = $this->db->lastInsertId();
        return $this->getById($id);
    }
    public function update($id, array $data) {
        $data['id'] = $id;  
        return $this->executeStatement(
            "UPDATE {$this->table} 
             SET order_id = :order_id, product_id = :product_id, 
                 quantity = :quantity, selected_attributes = :selected_attributes 
             WHERE id = :id",
            $data
        )->rowCount() > 0;
    }
}
