<?php
class Order
{
    private $id;
    private $user_id;
    private $total_price;
    private $created_at;
// getters and setters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setTotalPrice($total_price)
    {
        $this->total_price = $total_price;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }


    public function save()
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO orders (user_id, total_price) VALUES (:user_id, :total_price)');
        $query->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $query->bindValue(':total_price', $this->total_price, PDO::PARAM_STR);
        $query->execute();
        $this->id = $db->lastInsertId();
        return $this->id;
    }

    public static function getByUserId($userId)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    //check if user has purchased a product
    public static function hasPurchasedProduct($userId, $productId)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT COUNT(*) FROM order_items JOIN orders ON order_items.order_id = orders.id WHERE orders.user_id = :user_id AND order_items.product_id = :product_id");
        $statement->bindValue(':user_id', $userId);
        $statement->bindValue(':product_id', $productId);
        $statement->execute();
        return $statement->fetchColumn() > 0;
    }
}

?>