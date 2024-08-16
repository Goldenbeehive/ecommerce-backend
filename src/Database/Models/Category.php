<?php
namespace App\Database\Models;
use Database\Models\DatabaseModel;
use PDO;
class Category extends DatabaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'categories');
    }

    public function create(array $data) {
        $stmt = $this->executeStatement(
            "INSERT INTO {$this->table} (name) VALUES (:name)",
            ['name' => $data['name']]
        );
        return $this->db->lastInsertId();
    }

    public function update($id, array $data) {
        return $this->executeStatement(
            "UPDATE {$this->table} SET name = :name WHERE id = :id",
            ['name' => $data['name'], 'id' => $id]
        )->rowCount() > 0;
    }
}