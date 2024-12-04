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

        // Stap 1: Voeg het product toe
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
            // Stap 2: Haal het product_id op
            $productId = $conn->lastInsertId();

            // Stap 3: Voeg de opties toe in product_options
            if (!empty($options)) {
                $optionStatement = $conn->prepare("
                    INSERT INTO product_options (product_id, option_id)
                    VALUES (:product_id, :option_id)
                ");

                foreach ($options as $optionId) {
                    $optionStatement->bindValue(':product_id', $productId);
                    $optionStatement->bindValue(':option_id', $optionId);
                    $optionStatement->execute();
                }
            }

            return $productId; // Return het product_id
        }

        return false;
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

        // First, retrieve the image path
        $statement = $conn->prepare("SELECT image FROM products WHERE id = :id");
        $statement->bindValue(":id", $this->getId());
        $statement->execute();
        $product = $statement->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $imagePath = $product['image'];

            // Delete the product from the database
            $statement = $conn->prepare("DELETE FROM products WHERE id = :id");
            $statement->bindValue(":id", $this->getId());
            $result = $statement->execute();

            if ($result) {
                // Delete the image file
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            return $result;
        }

        return false;
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

    // Method to create a new product (alternative way)
    public static function createProduct($name, $price, $description, $category_id, $image, $stock)
    {
        // Maak een nieuw productobject
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setCategory($category_id);
        $product->setImage($image);
        $product->setStock($stock);

        // Sla het product op in de database
        if ($product->create()) {
            return "Product succesvol aangemaakt";
        } else {
            return "Het is niet gelukt om het product aan te maken";
        }
    }

    // Method to update an existing product (alternative way)
    public static function updateProduct($productId, $name, $price, $description, $category_id, $image, $stock)
    {
        // Haal het product op en stel de nieuwe waarden in
        $product = new Product();
        $product->setId($productId);
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setCategory($category_id);
        $product->setImage($image);
        $product->setStock($stock);

        // Werk het product bij
        if ($product->update()) {
            return "Product succesvol bijgewerkt";
        } else {
            return "Het is niet gelukt om het product bij te werken";
        }
    }
}

?>