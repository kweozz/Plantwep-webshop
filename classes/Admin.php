<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/User.php");
include_once(__DIR__ . "/Category.php");


// create new category, columns are id(auto incremented), name, image
class Admin extends User
{
    public function createCategory($name, $image)
    {
        if ($this->getRole() !== 1) {
            throw new Exception('Je hebt geen toestemming om nieuwe categorieen te maken');
        }

        $category = new Category();
        $category->setName($name);
        $category->setImage($image);
        return $category->create();
    }

    public function deleteCategory($categoryId)
    {
        if ($this->getRole() !== 1) {
            throw new Exception('Geen toestemming om categorieen te verwijderen');
        }

        $category = new Category();
        $category->setId($categoryId);
        return $category->delete();
    }
}

