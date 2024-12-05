<?php

class Option {
    private $product_id;
    private $name;
    private $value;

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        return $this->value;
    }

    // Andere setters/getters voor naam en waarde van de optie

    public function save() {
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            INSERT INTO product_options (product_id, name, value)
            VALUES (:product_id, :name, :value)
        ");
        $statement->bindValue(":product_id", $this->getProductId());
        $statement->bindValue(":name", $this->getName());
        $statement->bindValue(":value", $this->getValue());

        return $statement->execute();
    }

    public static function getAll() {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM options");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveProductOptions($productId, $options) {
        if (empty($productId)) {
            throw new Exception("Invalid product ID: $productId");
        }
    
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            INSERT INTO product_options (product_id, option_id)
            VALUES (:product_id, :option_id)
        ");
        foreach ($options as $optionId) {
            $statement->bindValue(":product_id", $productId);
            $statement->bindValue(":option_id", $optionId);
            $statement->execute();
        }
    }
    public static function getPriceAdditionById($optionId) {
        $conn = Db::getConnection();
        $stmt = $conn->prepare("SELECT price_addition FROM options WHERE id = ?");
        $stmt->execute([$optionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['price_addition'] : 0;  // Return 0 if no price addition is set
    }
}