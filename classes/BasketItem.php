<?php

class BasketItem
{
    private $id;
    private $basket_id;
    private $product_id;
    private $quantity;
    private $price;
    private $option_id;
    private $price_addition;
    private $total_price;

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getBasketId()
    {
        return $this->basket_id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getOptionId()
    {
        return $this->option_id;
    }

    public function getPriceAddition()
    {
        return $this->price_addition;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setBasketId($basket_id)
    {
        $this->basket_id = $basket_id;
    }

    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setOptionId($option_id)
    {
        $this->option_id = $option_id;
    }

    public function setPriceAddition($price_addition)
    {
        $this->price_addition = $price_addition;
    }

    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
    }
// get option id
    public static function getOptionById($optionId)
    {
        $db = Db::getConnection();
        $stmt = $db->prepare('SELECT * FROM options WHERE id = :option_id');
        $stmt->bindParam(':option_id', $optionId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function createBasketItem($basket_id, $product_id, $quantity, $price, $option_ids = null, $price_addition = 0, $total_price)
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO basket_item 
            (basket_id, product_id, quantity, price, option_ids, price_addition, total_price) 
            VALUES (:basket_id, :product_id, :quantity, :price, :option_ids, :price_addition, :total_price)');
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        $query->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindValue(':price', $price, PDO::PARAM_STR);
        $query->bindValue(':option_ids', $option_ids, PDO::PARAM_STR);
        $query->bindValue(':price_addition', $price_addition, PDO::PARAM_STR);
        $query->bindValue(':total_price', $total_price, PDO::PARAM_STR);

        if (!$query->execute()) {
            throw new Exception('Failed to create basket item');
        }
    }

    public static function getItemsByBasketId($basketId)
    {
        $db = Db::getConnection();
        $stmt = $db->prepare('SELECT * FROM basket_item WHERE basket_id = :basket_id');
        $stmt->bindParam(':basket_id', $basketId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add item to basket function
    public static function addItemToBasket($basket_id, $product_id, $quantity, $price, $total_price, $option_id = null, $price_addition = 0)
    {
        // Check if the item already exists in the basket
        $db = Db::getConnection();
        $query = $db->prepare('SELECT * FROM basket_item WHERE basket_id = :basket_id AND product_id = :product_id AND option_id = :option_id');
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        $query->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $query->bindValue(':option_id', $option_id, PDO::PARAM_INT);
        $query->execute();
        $existingItem = $query->fetch(PDO::FETCH_ASSOC);

        if ($existingItem) {
            // Update the quantity and total price of the existing item
            $newQuantity = $existingItem['quantity'] + $quantity;
            $newTotalPrice = $existingItem['total_price'] + $total_price;
            $updateQuery = $db->prepare('UPDATE basket_item SET quantity = :quantity, total_price = :total_price WHERE id = :id');
            $updateQuery->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
            $updateQuery->bindValue(':total_price', $newTotalPrice, PDO::PARAM_STR);
            $updateQuery->bindValue(':id', $existingItem['id'], PDO::PARAM_INT);
            $updateQuery->execute();
        } else {
            // Insert a new item into the basket
            $insertQuery = $db->prepare('INSERT INTO basket_item 
                (basket_id, product_id, quantity, price, option_id, price_addition, total_price) 
                VALUES (:basket_id, :product_id, :quantity, :price, :option_id, :price_addition, :total_price)');
            $insertQuery->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
            $insertQuery->bindValue(':product_id', $product_id, PDO::PARAM_INT);
            $insertQuery->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $insertQuery->bindValue(':price', $price, PDO::PARAM_STR);
            $insertQuery->bindValue(':option_id', $option_id, PDO::PARAM_INT);
            $insertQuery->bindValue(':price_addition', $price_addition, PDO::PARAM_STR);
            $insertQuery->bindValue(':total_price', $total_price, PDO::PARAM_STR);
            $insertQuery->execute();
        }
    }

    public static function removeItemFromBasket($basket_item_id)
    {
        $db = Db::getConnection();
        $query = $db->prepare('DELETE FROM basket_item WHERE id = :basket_item_id');
        $query->bindValue(':basket_item_id', $basket_item_id, PDO::PARAM_INT);
        return $query->execute();
    }

    public static function clearBasket($basket_id)
    {
        $db = Db::getConnection();
        $query = $db->prepare('DELETE FROM basket_item WHERE basket_id = :basket_id');
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        return $query->execute();
    }

    public static function getTotalItems($basketId)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT SUM(quantity) as total_items FROM basket_item WHERE basket_id = :basket_id');
        $query->bindValue(':basket_id', $basketId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total_items'] ?? 0;
    }
}
?>