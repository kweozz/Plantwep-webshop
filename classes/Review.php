<?php
include_once(__DIR__ . "/Db.php");
class Review {
    private $id;
    private $user_id;
    private $product_id;
    private $rating;
    private $comment;
    private $created_at;

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getRating() {
        return $this->rating;
    }

    public function getComment() {
        return $this->comment;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setProductId($product_id) {
        $this->product_id = $product_id;
    }

    public function setRating($rating) {
        $this->rating = $rating;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    public static function getByProductId($product_id) {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT reviews.*, users.firstname, users.lastname FROM reviews JOIN users ON reviews.user_id = users.id WHERE product_id = :product_id ORDER BY created_at DESC");
        $statement->bindValue(":product_id", $product_id);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save() {
        $conn = Db::getConnection();
        $statement = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment, created_at) VALUES (:user_id, :product_id, :rating, :comment, NOW())");
        $statement->bindValue(":user_id", $this->getUserId());
        $statement->bindValue(":product_id", $this->getProductId());
        $statement->bindValue(":rating", $this->getRating());
        $statement->bindValue(":comment", $this->getComment());
        return $statement->execute();
    }
}
?>