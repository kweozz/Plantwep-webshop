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
    public function create($options = [])
    {
        $conn = Db::getConnection();
    
        // Step 1: Add the product
        $statement = $conn->prepare("
            INSERT INTO products (name, price, description, category_id, image, stock)
            VALUES (:name, :price, :description, :category_id, :image, :stock)
        ");
        $statement->bindValue(':name', $this->getName());
        $statement->bindValue(':price', $this->getPrice());
        $statement->bindValue(':description', $this->getDescription());
        $statement->bindValue(':category_id', $this->getCategory());
        $statement->bindValue(':image', $this->getImage());
        $statement->bindValue(':stock', $this->getStock());
    
        if ($statement->execute()) {
            // Step 2: Get the product_id
            $productId = $conn->lastInsertId();
    
            // Step 3: Add the options to product_options
            if (!empty($options)) {
                $optionStatement = $conn->prepare("
                    INSERT INTO product_options (product_id, option_id, price_addition)
                    VALUES (:product_id, :option_id, :price_addition)
                ");
    
                foreach ($options as $option) {
                    $optionStatement->bindValue(':product_id', $productId);
                    $optionStatement->bindValue(':option_id', $option['option_id']);
                    $optionStatement->bindValue(':price_addition', $option['price_addition']);
                    $optionStatement->execute();
                }
            }
    
            return $productId; // Return the product_id
        }
    
        return false;
    }

    public function update($options = [])
    {
        $conn = Db::getConnection();
    
        // Step 1: Update the product
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
    
        if ($statement->execute()) {
            // Step 2: Delete existing options
            $deleteOptionsStatement = $conn->prepare("
                DELETE FROM product_options WHERE product_id = :product_id
            ");
            $deleteOptionsStatement->bindValue(':product_id', $this->getId());
            $deleteOptionsStatement->execute();
    
            // Step 3: Add new options to product_options
            if (!empty($options)) {
                $optionStatement = $conn->prepare("
                    INSERT INTO product_options (product_id, option_id, price_addition)
                    VALUES (:product_id, :option_id, :price_addition)
                ");
    
                foreach ($options as $option) {
                    $optionStatement->bindValue(':product_id', $this->getId());
                    $optionStatement->bindValue(':option_id', $option['option_id']);
                    $optionStatement->bindValue(':price_addition', $option['price_addition']);
                    $optionStatement->execute();
                }
            }
    
            return true;
        }
    
        return false;
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

    // Retrieve all products
    public static function getAll()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM products");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
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

    // Method to update an existing product (alternative way)
    public static function updateProduct($productId, $name, $price, $description, $category_id, $image, $stock)
    {
        // Retrieve the product and set the new values
        $product = new Product();
        $product->setId($productId);
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setCategory($category_id);
        $product->setImage($image);
        $product->setStock($stock);

        // Update the product
        if ($product->update()) {
            return "Product succesvol bijgewerkt";
        } else {
            return "Het is niet gelukt om het product bij te werken";
        }
    }
}
?>