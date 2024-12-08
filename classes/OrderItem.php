<?php
class OrderItem
{
    private $id;
    private $order_id;
    private $product_id;
    private $quantity;
    private $price;
    private $option_ids;
    private $price_addition;
    private $total_price;

    // Getters and Setters
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getOptionIds()
    {
        return $this->option_ids;
    }

    public function setOptionIds($option_ids)
    {
        $this->option_ids = $option_ids;
    }

    public function getPriceAddition()
    {
        return $this->price_addition;
    }

    public function setPriceAddition($price_addition)
    {
        $this->price_addition = $price_addition;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
    }

    public function save()
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price, option_ids, price_addition, total_price) 
                               VALUES (:order_id, :product_id, :quantity, :price, :option_ids, :price_addition, :total_price)');
        $query->bindValue(':order_id', $this->order_id, PDO::PARAM_INT);
        $query->bindValue(':product_id', $this->product_id, PDO::PARAM_INT);
        $query->bindValue(':quantity', $this->quantity, PDO::PARAM_INT);
        $query->bindValue(':price', $this->price, PDO::PARAM_STR);
        $query->bindValue(':option_ids', $this->option_ids, PDO::PARAM_STR);
        $query->bindValue(':price_addition', $this->price_addition, PDO::PARAM_STR);
        $query->bindValue(':total_price', $this->total_price, PDO::PARAM_STR);
        $query->execute();
    }

    public static function getByOrderId($orderId)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
        $query->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>