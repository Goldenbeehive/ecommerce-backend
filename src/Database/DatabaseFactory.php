<?php
namespace App\Database;
use App\Database\Models\Category;
use App\Database\Models\Product;
use App\Database\Models\Price;
use App\Database\Models\Attribute;
use App\Database\Models\Gallery;
use App\Database\Models\Order;
use App\Database\Models\OrderDetails;
use Exception;
use PDO;
class DatabaseFactory {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function createModel($modelName) {
        switch ($modelName) {
            case 'Category':
                return new Category($this->db);
            case 'Product':
                return new Product($this->db);
            case 'Price':
                return new Price($this->db);
            case 'Attribute':
                return new Attribute($this->db);
            case 'Gallery':
                return new Gallery($this->db);
            case 'Order':
                return new Order($this->db);
            case 'OrderDetails':
                return new OrderDetails($this->db);
            default:
                throw new Exception("Invalid model name");
        }
    }
}
