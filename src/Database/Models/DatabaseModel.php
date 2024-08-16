<?php
 
namespace Database\Models;

use PDO;
abstract class DatabaseModel {
    protected $db;
    protected $table;

    public function __construct(PDO $db, $table) {
        $this->db = $db;
        $this->table = $table;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getOneByProductId($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE product_id = :id Limit 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    abstract public function create(array $data);

    abstract public function update($id, array $data);

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    protected function executeStatement($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}