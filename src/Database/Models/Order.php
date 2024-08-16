<?php

namespace App\Database\Models;

use Database\Models\DatabaseModel;
use PDO;

class Order extends DatabaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'orders');
    }

    public function create(array $data) {
        $stmt = $this->executeStatement(
            "INSERT INTO {$this->table} (total, currency, created_at) 
             VALUES (:total, :currency, NOW())",
            $data
        );
        return $this->db->lastInsertId();
    }

    public function update($id, array $data) {
        $data['id'] = $id;  
        return $this->executeStatement(
            "UPDATE {$this->table} 
             SET total = :total, currency = :currency 
             WHERE id = :id",
            $data
        )->rowCount() > 0;
    }
}
