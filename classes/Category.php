<?php
include_once(__DIR__ . "/Db.php");
class Category
{
    private $id;
    private $name;
    private $image;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    // Create a new category
    public function create()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("INSERT INTO category (name, image) VALUES (:name, :image)");
        $statement->bindValue(':name', $this->getName());
        $statement->bindValue(':image', $this->getImage());
        return $statement->execute();
    }

    // Update an existing category
    public function update()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("UPDATE category SET name = :name, image = :image WHERE id = :id");
        $statement->bindValue(':name', $this->getName());
        $statement->bindValue(':image', $this->getImage());
        $statement->bindValue(':id', $this->getId());
        return $statement->execute();
    }

    // Delete a category
    public function delete()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("DELETE FROM category WHERE id = :id");
        $statement->bindValue(':id', $this->getId());
        return $statement->execute();
    }

    // Retrieve all categories
    public static function getAll()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM category");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve a single category by ID
    public static function getById($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM category WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}
?>
