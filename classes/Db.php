<?php
class Db {
    // Declare de statische eigenschap $conn
    private static $conn;
//singleton pattern:
    // Methode om de databaseverbinding te verkrijgen
    public static function getConnection() {
        // Controleer of de verbinding al bestaat
        if (self::$conn === null) {
            try {
                // Maak een nieuwe verbinding
                self::$conn = new PDO("mysql:host=localhost;dbname=plantwerp", "root", "");
                
            } catch (PDOException $e) {
                // Geef een duidelijke foutmelding als de verbinding mislukt
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn; // Retourneer de verbinding
    }
}
?>
