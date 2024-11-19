<?php
include_once(__DIR__ . "/Db.php");
include_once(__DIR__ . "/User.php");

class Admin extends User {
    // Methode om een nieuw product toe te voegen
    public static function addProduct($name, $description, $price, $category_id, $product_image) {
        // Maak een nieuwe databaseverbinding
        $conn = Db::getConnection();
        // Bereid een SQL-query voor
        $statement = $conn->prepare("INSERT INTO products (name, description, price, category_id) VALUES (:name, :description, :price, :category_id)");
        // Voer de query uit
        $statement->execute([
            ":name" => $name,
            ":description" => $description,
            ":price" => $price,
            ":category_id" => $category_id,
            ":image"=> $product_image
        ]);
    }
}

?>