<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/User.php");
include_once(__DIR__ . "/Category.php");


// create new category, columns are id(auto incremented), name, image
class Admin extends User
{
    public function createCategory($name, $image)
    {
        $category = new Category();
        $category->setName($name);
        $category->setImage($image);
        return $category->create();
    }

    //update category, columns are id(auto incremented), name, image
    public function updateCategory($categoryId, $name, $image)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming');
        }
        $category = new Category();
        $category->setId($categoryId);
        $category->setName($name);
        $category->setImage($image);
        return $category->update();
    }

    public function deleteCategory($categoryId)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om categorieen te verwijderen');
        }
        $category = new Category();
        $category->setId($categoryId);
        return $category->delete();
    }
    //functie om images te uploaden die ik kan aanroepen in admin dashboard
    public function uploadImage($file)
    {

        if ($_SESSION['role'] !== 1) {
            throw new Exception('Je hebt geen toegang');
        }
        $targetDir = __DIR__ . "/../images/uploads/";
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check of het bestandstype juist is
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception('Bestand is geen afbeelding');
        }

        // Check file size (maximaal 500 KB)
        if ($file["size"] > 500000) {
            throw new Exception('Sorry, je bestand is te groot');
        }

        // Allow certain file formats
        $allowedFormats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            throw new Exception('Sorry, alleen JPG, JPEG, PNG & GIF bestanden zijn toegestaan');
        }

        // Check of de file al in de database zit
        if (file_exists($targetFile)) {
            throw new Exception('Sorry, bestand bestaat al');
        }

        // Try to upload file
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            throw new Exception('Sorry, er was een fout bij het uploaden van je bestand');
        }

        return basename($file["name"]); // Return the filename after upload
    }
}


