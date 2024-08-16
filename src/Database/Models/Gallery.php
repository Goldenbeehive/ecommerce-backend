<?php

namespace App\Database\Models;

use Database\Models\DatabaseModel;
use PDO;

class Gallery extends DatabaseModel
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'gallery');
    }

    public function create(array $data)
    {
        $this->executeStatement(
            "INSERT INTO {$this->table} (product_id, image_url) 
             VALUES (:product_id, :image_url)",
            $data
        );
        return $this->db->lastInsertId();
    }
    public function getByProductId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE product_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_DEFAULT);
    }
    public function update($id, array $data)
    {
        $data['id'] = $id;
        return $this->executeStatement(
            "UPDATE {$this->table} 
             SET product_id = :product_id, image_url = :image_url 
             WHERE id = :id",
            $data
        )->rowCount() > 0;
    }
}