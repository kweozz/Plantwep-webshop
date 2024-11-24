<?php
include_once(__DIR__ . "/Db.php");

class Product
{
    private $id;
    private $name;
    private $price;
    private $description;
    private $category_id;
    private $image;
    private $stock;

    // Getters and Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setCategory($category_id)
    {
        $this->category_id = $category_id;
    }

    public function getCategory()
    {
        return $this->category_id;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    public function getStock()
    {
        return $this->stock;
    }

    // CRUD Operations

    // Create a new product
    public function create()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            INSERT INTO products (name, price, description, category_id, image, stock) 
            VALUES (:name, :price, :description, :category_id, :image, :stock)
        ");
        $statement->bindValue(":name", $this->getName());
        $statement->bindValue(":price", $this->getPrice());
        $statement->bindValue(":description", $this->getDescription());
        $statement->bindValue(":category_id", $this->getCategory());
        $statement->bindValue(":image", $this->getImage());
        $statement->bindValue(":stock", $this->getStock());
        return $statement->execute();
    }

    // Update an existing product
    public function update()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            UPDATE products 
            SET name = :name, price = :price, description = :description, category_id = :category_id, 
                image = :image, stock = :stock
            WHERE id = :id
        ");
        $statement->bindValue(":name", $this->getName());
        $statement->bindValue(":price", $this->getPrice());
        $statement->bindValue(":description", $this->getDescription());
        $statement->bindValue(":category_id", $this->getCategory());
        $statement->bindValue(":image", $this->getImage());
        $statement->bindValue(":stock", $this->getStock());
        $statement->bindValue(":id", $this->getId());
        return $statement->execute();
    }

    // Delete a product
    public function delete()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("DELETE FROM products WHERE id = :id");
        $statement->bindValue(":id", $this->getId());
        return $statement->execute();
    }

    // Retrieve all products
    public static function getAll()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM products");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve a single product by ID
    public static function getById($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM products WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Retrieve products by category
    public static function getByCategory($category_id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM products WHERE category_id = :category_id");
        $statement->bindValue(":category_id", $category_id);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

// product opties 
public function getOptions()
{
    $conn = Db::getConnection();
    $stmt = $conn->prepare("
        SELECT * 
        FROM product_options 
        WHERE product_id = :product_id
    ");
    $stmt->bindValue(":product_id", $this->getId(), PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function addOption($optionName, $optionValue, $extraPrice = 0)
{
    $conn = Db::getConnection();
    $stmt = $conn->prepare("
        INSERT INTO product_options (product_id, option_name, option_value, extra_price) 
        VALUES (:product_id, :option_name, :option_value, :extra_price)
    ");
    $stmt->bindValue(":product_id", $this->getId(), PDO::PARAM_INT);
    $stmt->bindValue(":option_name", $optionName);
    $stmt->bindValue(":option_value", $optionValue);
    $stmt->bindValue(":extra_price", $extraPrice, PDO::PARAM_STR);
    return $stmt->execute();
}
public function deleteOption($optionId)
{
    $conn = Db::getConnection();
    $stmt = $conn->prepare("DELETE FROM product_options WHERE id = :id");
    $stmt->bindValue(":id", $optionId, PDO::PARAM_INT);
    return $stmt->execute();
}

}
?>
