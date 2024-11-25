<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/User.php");
include_once(__DIR__ . "/Category.php");
include_once(__DIR__ . "/Product.php");

class Admin extends User
{
    // Nieuwe categorie aanmaken
    public function createCategory($name, $image)
    {
        $category = new Category();
        $category->setName($name);
        $category->setImage($image);

        try {
            return $category->create() ? "Categorie succesvol aangemaakt" : "Het is niet gelukt om de categorie aan te maken";
        } catch (Exception $e) {
            return "Fout: " . $e->getMessage();
        }
    }

    // Nieuw product aanmaken
    public function createProduct($name, $price, $description, $category_id, $image, $stock)
    {
        $product = $this->initializeProduct($name, $price, $description, $category_id, $image, $stock);

        try {
            return $this->saveProduct($product) ? "Product succesvol aangemaakt" : "Het is niet gelukt om het product aan te maken";
        } catch (Exception $e) {
            return "Fout: " . $e->getMessage();
        }
    }

    private function initializeProduct($name, $price, $description, $category_id, $image, $stock)
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setDescription($description);
        $product->setStock($stock);
        $product->setCategory($category_id);
        $product->setImage($image);
        return $product;
    }

    public static function deleteProduct($productId)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om product te verwijderen');
        }

        $product = new Product();
        $product->setId($productId);

        // Haal productgegevens op om afbeelding te krijgen
        $productDetails = $product->getById($productId);
        if (!$productDetails) {
            throw new Exception('Product niet gevonden');
        }

        // Construeer het pad naar de afbeelding
        $imagePath = __DIR__ . "/../images/uploads/" . basename($productDetails['image']);

        try {
            // Verwijder het product uit de database
            if ($product->delete()) {
                // Controleer of de afbeeldingsbestand bestaat en probeer deze te verwijderen
                if (file_exists($imagePath)) {
                    if (unlink($imagePath)) {
                        return "Product en bijbehorende afbeelding succesvol verwijderd";
                    } else {
                        return "Product verwijderd, maar het is niet gelukt om de bijbehorende afbeelding te verwijderen";
                    }
                } else {
                    return "Product succesvol verwijderd, maar geen bijbehorende afbeelding gevonden";
                }
            } else {
                return "Het is niet gelukt om het product te verwijderen";
            }
        } catch (Exception $e) {
            return "Fout: " . $e->getMessage();
        }
    }

    private function saveProduct($product)
    {
        return $product->create();
    }

    // Categorie bijwerken
    public function updateCategory($categoryId, $name, $image)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om categorie bij te werken');
        }

        $category = new Category();
        $category->setId($categoryId);
        $category->setName($name);
        $category->setImage($image);

        try {
            return $category->update() ? "Categorie succesvol bijgewerkt" : "Het is niet gelukt om de categorie bij te werken";
        } catch (Exception $e) {
            return "Fout: " . $e->getMessage();
        }
    }

    // Categorie verwijderen
    public function deleteCategory($categoryId)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om categorie te verwijderen');
        }

        $category = new Category();
        $category->setId($categoryId);

        try {
            return $category->delete() ? "Categorie succesvol verwijderd" : "Het is niet gelukt om de categorie te verwijderen";
        } catch (Exception $e) {
            return "Fout: " . $e->getMessage();
        }
    }

    // Afbeelding uploaden voor producten of categorieën
    public function uploadImage($file)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om afbeeldingen te uploaden');
        }

        $targetDir = __DIR__ . "/../images/uploads/";
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Controleer of bestand een afbeelding is
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception('Bestand is geen afbeelding');
        }

        // Controleer bestandsgrootte (max 500 KB)
        if ($file["size"] > 500000) {
            throw new Exception('Sorry, het bestand is te groot');
        }

        // Toegestane bestandstypen
        $allowedFormats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            throw new Exception('Sorry, alleen JPG, JPEG, PNG en GIF-bestanden zijn toegestaan');
        }

        // Controleer of bestand al bestaat
        if (file_exists($targetFile)) {
            throw new Exception('Sorry, bestand bestaat al');
        }

        // Probeer bestand te uploaden
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            throw new Exception('Sorry, er is een fout opgetreden bij het uploaden van het bestand');
        }

        return basename($file["name"]);
    }
}
?>