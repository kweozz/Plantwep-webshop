<?php
class ProductOption
{
    private $id;
    private $productId;
    private $optionId;
    private $priceAddition;

    // Getters and Setters

    public function getId()
    {
        return $this->id;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getOptionId()
    {
        return $this->optionId;
    }

    public function getPriceAddition()
    {
        return $this->priceAddition;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
    }

    public function setPriceAddition($priceAddition)
    {
        $this->priceAddition = $priceAddition;
    }

    // Method to save a product option
    public static function save($productId, $optionId, $priceAddition)
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO product_options (product_id, option_id, price_addition) 
                               VALUES (:productId, :optionId, :priceAddition)');
        $query->bindValue(':productId', $productId, PDO::PARAM_INT);
        $query->bindValue(':optionId', $optionId, PDO::PARAM_INT);
        $query->bindValue(':priceAddition', $priceAddition, PDO::PARAM_STR); // Ensure it captures decimal values
        return $query->execute();
    }

    // Method to update product options
    public static function updateOptions($productId, $options)
    {
        $db = Db::getConnection();

        // Delete existing options for the product
        $deleteQuery = $db->prepare('DELETE FROM product_options WHERE product_id = :productId');
        $deleteQuery->bindValue(':productId', $productId, PDO::PARAM_INT);
        $deleteQuery->execute();

        // Insert new options
        foreach ($options as $option) {
            self::save($productId, $option['option_id'], $option['price_addition']);
        }
    }

    // Get options for a product
    public static function getByProductId($productId)
    {
        $conn = Db::getConnection();

        // Fetch the options for this product (sizes and pots) only
        $stmt = $conn->prepare("
         SELECT o.id, o.name, o.type, po.price_addition
            FROM options o
            JOIN product_options po ON o.id = po.option_id
            WHERE po.product_id = ?
        ");
        $stmt->execute([$productId]);

        // Fetch all available options for the product
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $options;
    }

    public static function delete($productId, $optionId) {

        $db = Db::getConnection();

        $stmt = $db->prepare("DELETE FROM product_options WHERE product_id = :product_id AND option_id = :option_id");

        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);

        $stmt->bindParam(':option_id', $optionId, PDO::PARAM_INT);

        return $stmt->execute();

    }
}
?>