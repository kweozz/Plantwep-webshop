<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/User.php");
include_once(__DIR__ . "/Category.php");

class Admin extends User {
// create method to add a new category
    public function addCategory($name) {
        $conn = Db::getConnection();
        $statement = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
        $statement->bindValue(":name", $name);
        $statement->execute();
    }

    // create method to delete a category
    public function deleteCategory($id) {
        $conn = Db::getConnection();
        $statement = $conn->prepare("DELETE FROM categories WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
    }
    //if role == 1 assign admin role

}
?>