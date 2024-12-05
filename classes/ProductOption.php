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




    public static function update($productId, $options, $productOptions, $selectedOptions)
    {
        // Stap 1: Bijwerken van bestaande opties (prijs toevoegen, etc.)
        foreach ($options as $option) {
            $existingOption = self::getOption($productId, $option['option_id']); // Haal de bestaande optie op
            if ($existingOption) {
                // Optie bestaat al, werk de prijs bij
                self::updateOptionPriceAddition($productId, $option['option_id'], $option['price_addition']);
            } else {
                // Optie bestaat nog niet, voeg deze toe
                self::addOption($productId, $option['option_id'], $option['price_addition']);
            }
        }

        // Stap 2: Verwijder opties die niet langer geselecteerd zijn
        foreach ($productOptions as $existingOption) {
            if (!in_array($existingOption['option_id'], $selectedOptions)) {
                self::removeOption($productId, $existingOption['option_id']);
            }
        }
    }

    // Haal de bestaande optie op uit de database (controleer of deze al bestaat)
    private static function getOption($productId, $optionId)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare("SELECT * FROM product_options WHERE product_id = ? AND option_id = ?");
        $stmt->execute([$productId, $optionId]);
        return $stmt->fetch();
    }

    // Voeg een nieuwe optie toe aan het product
    private static function addOption($productId, $optionId, $priceAddition)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare("INSERT INTO product_options (product_id, option_id, price_addition) VALUES (?, ?, ?)");
        $stmt->execute([$productId, $optionId, $priceAddition]);
    }

    // Werk de prijs van een bestaande optie bij
    private static function updateOptionPriceAddition($productId, $optionId, $priceAddition)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare("UPDATE product_options SET price_addition = ? WHERE product_id = ? AND option_id = ?");
        $stmt->execute([$priceAddition, $productId, $optionId]);
    }

    // Verwijder een optie uit het product
    private static function removeOption($productId, $optionId)
    {
        $conn = Db::getConnection();
        $stmt = $conn->prepare("DELETE FROM product_options WHERE product_id = ? AND option_id = ?");
        $stmt->execute([$productId, $optionId]);
    }


}





?>