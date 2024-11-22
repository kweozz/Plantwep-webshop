<?php
include_once(__DIR__ . "/Db.php");

class Product {
    private $id;
    private $name;
    private $price;
    private $description;
    private $category_id;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setCategory($category_id) {
        $this->category_id = $category_id;
    }

    public function getCategory() {
        return $this->category_id;
    }

    public static function getAll() {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM products");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
