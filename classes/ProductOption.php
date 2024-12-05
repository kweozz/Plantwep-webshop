<?php
class ProductOption {
    private $id;
    private $productId;
    private $optionId;

    // Constructor, getters, and setters
    public function __construct($id, $productId, $optionId) {
        $this->id = $id;
        $this->productId = $productId;
        $this->optionId = $optionId;
    }

    public function getId() {
        return $this->id;
    }

    public function getProductId() {
        return $this->productId;
    }

    public function getOptionId() {
        return $this->optionId;
    }

    // Method to save a product option
    public static function save($productId, $optionId) {
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            INSERT INTO product_options (product_id, option_id)
            VALUES (:product_id, :option_id)
        ");
        $statement->bindValue(":product_id", $productId);
        $statement->bindValue(":option_id", $optionId);
        return $statement->execute();
    }

    // Get options for a product
    public static function getByProductId($productId) {
        $conn = Db::getConnection();
    
        // Fetch the options for this product (sizes and pots) only
        $stmt = $conn->prepare("
            SELECT o.id, o.name, o.type
            FROM options o
            JOIN product_options po ON o.id = po.option_id
            WHERE po.product_id = ?
        ");
        $stmt->execute([$productId]);
    
        // Fetch all available options for the product
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $options;
    }
    
    
}
?>
