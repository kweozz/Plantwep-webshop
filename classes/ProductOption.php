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


}
?>