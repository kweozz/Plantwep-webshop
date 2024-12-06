<?php

class Basket
{
    private $id;
    private $user_id;

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    public static function create($user_id)
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO basket (user_id) VALUES (:user_id)');
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        return $query->execute();
    }
    public static function getBasket($user_id)
    {
        $db = Db::getConnection();
        $query = $db->prepare('SELECT * FROM basket WHERE user_id = :user_id');
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }
    public function save()
    {
        $db = Db::getConnection();
        $query = $db->prepare('INSERT INTO basket (user_id) VALUES (:user_id)');
        $query->bindValue(':user_id', $this->getUserId(), PDO::PARAM_INT);
        $query->execute();
        $this->setId($db->lastInsertId());
    }
}
?>