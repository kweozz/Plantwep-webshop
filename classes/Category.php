<?php
include_once(__DIR__ . "/Db.php");

class Category
{
    private $id;
    private $name;
    private $image;

    // Getters and Setters
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

    // CRUD Operations

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
    public function delete($categoryId)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("DELETE FROM category WHERE id = :id");
        $statement->bindValue(':id', $categoryId);
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

    // Method to create a new category (alternative way)
    public static function createCategory($name, $image)
    {
        // Maak een nieuw category-object
        $category = new Category();
        $category->setName($name);
        $category->setImage($image);

        // Sla de nieuwe categorie op in de database
        if ($category->create()) {
            return "Categorie succesvol aangemaakt";
        } else {
            return "Het is niet gelukt om de categorie aan te maken";
        }
    }

    // Method to update an existing category (alternative way)
    public static function updateCategory($categoryId, $name, $image)
    {
        // Haal de categorie op en stel de nieuwe waarden in
        $category = new Category();
        $category->setId($categoryId);
        $category->setName($name);
        $category->setImage($image);

        // Werk de categorie bij
        if ($category->update()) {
            return "Categorie succesvol bijgewerkt";
        } else {
            return "Het is niet gelukt om de categorie bij te werken";
        }
    }

    // Method to delete a category (alternative way)
    public static function deleteCategory($categoryId)
    {
        // Haal de categorie op via ID
        $category = new Category();
        $category->setId($categoryId);

        // Verwijder de categorie
        if ($category->delete()) {
            return "Categorie succesvol verwijderd";
        } else {
            return "Het is niet gelukt om de categorie te verwijderen";
        }
    }

 

}
?>
