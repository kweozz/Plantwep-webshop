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

    public static function createBasketItem($basket_id, $product_id, $quantity, $price, $option_id = null, $price_addition = 0)
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO basket_items (basket_id, product_id, quantity, price, option_id, price_addition, total_price) VALUES (:basket_id, :product_id, :quantity, :price, :option_id, :price_addition, :total_price)');
        $total_price = ($price + $price_addition) * $quantity;
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        $query->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindValue(':price', $price, PDO::PARAM_STR);
        $query->bindValue(':option_id', $option_id, PDO::PARAM_INT);
        $query->bindValue(':price_addition', $price_addition, PDO::PARAM_STR);
        $query->bindValue(':total_price', $total_price, PDO::PARAM_STR);
        $query->execute();

        $basketItem = new self();
        $basketItem->setId($db->lastInsertId());
        $basketItem->setBasketId($basket_id);
        $basketItem->setProductId($product_id);
        $basketItem->setQuantity($quantity);
        $basketItem->setPrice($price);
        $basketItem->setOptionId($option_id);
        $basketItem->setPriceAddition($price_addition);
        $basketItem->setTotalPrice($total_price);
        return $basketItem;
    }

    public static function getItemsByBasketId($basketId)
    {

        $db = Db::getConnection();

        $stmt = $db->prepare('SELECT * FROM basket_items WHERE basket_id = :basket_id');

        $stmt->bindParam(':basket_id', $basketId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    //add item to basket function
    public static function addItemToBasket($basket_id, $product_id, $quantity, $price, $option_id = null, $price_addition = 0)
    {
        // Check if the item already exists in the basket
        $db = Db::getConnection();
        $query = $db->prepare('SELECT * FROM basket_items WHERE basket_id = :basket_id AND product_id = :product_id AND option_id = :option_id');
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        $query->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $query->bindValue(':option_id', $option_id, PDO::PARAM_INT);
        $query->execute();
    }
    public static function removeItemFromBasket($basket_id, $product_id, $option_id = null)
    {
        $db = Db::getConnection();
        $query = $db->prepare('DELETE FROM basket_items WHERE basket_id = :basket_id AND product_id = :product_id AND option_id = :option_id');
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        $query->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $query->bindValue(':option_id', $option_id, PDO::PARAM_INT);
        return $query->execute();
    }
    public static function clearBasket($basket_id)
    {
        $db = Db::getConnection();
        $query = $db->prepare('DELETE FROM basket_items WHERE basket_id = :basket_id');
        $query->bindValue(':basket_id', $basket_id, PDO::PARAM_INT);
        return $query->execute();
    }
}
?>