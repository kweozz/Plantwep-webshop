<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/User.php");
include_once(__DIR__ . "/Category.php");
include_once(__DIR__ . "/Product.php");

class Admin extends User
{
    // Create new category
    public function createCategory($name, $image)
    {
        $category = new Category();
        $category->setName($name);
        $category->setImage($image);
        
        try {
            return $category->create() ? "Category created successfully" : "Failed to create category";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // Create new product
    public function createProduct($name, $price, $description, $category_id, $image, $stock)
    {
        $product = $this->initializeProduct($name, $price, $description, $category_id, $image, $stock);
        
        try {
            return $this->saveProduct($product) ? "Product created successfully" : "Failed to create product";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
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

    private function saveProduct($product)
    {
        return $product->create();
    }

    // Update category
    public function updateCategory($categoryId, $name, $image)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('No permission to update category');
        }
        
        $category = new Category();
        $category->setId($categoryId);
        $category->setName($name);
        $category->setImage($image);

        try {
            return $category->update() ? "Category updated successfully" : "Failed to update category";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // Delete category
    public function deleteCategory($categoryId)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('No permission to delete category');
        }
        
        $category = new Category();
        $category->setId($categoryId);

        try {
            return $category->delete() ? "Category deleted successfully" : "Failed to delete category";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // Upload image for products or categories
    public function uploadImage($file)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('You do not have access to upload images');
        }
        
        $targetDir = __DIR__ . "/../images/uploads/";
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate file type
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception('File is not an image');
        }

        // Validate file size (max 500 KB)
        if ($file["size"] > 500000) {
            throw new Exception('Sorry, your file is too large');
        }

        // Allowed file formats
        $allowedFormats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            throw new Exception('Sorry, only JPG, JPEG, PNG & GIF files are allowed');
        }

        // Check if file already exists
        if (file_exists($targetFile)) {
            throw new Exception('Sorry, file already exists');
        }

        // Try to upload file
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            throw new Exception('Sorry, there was an error uploading your file');
        }

        return basename($file["name"]);
    }



}

?>
