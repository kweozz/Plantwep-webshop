<?php
class Db {
    // Declare the static property $conn
    private static $conn;

    // Method to get the database connection
    public static function getConnection() {
        // Check if the connection already exists
        if (self::$conn === null) {
            try {
                // Use Railway database connection details
                $host = 'autorack.proxy.rlwy.net';
                $port = '45276';
                $dbname = 'railway';
                $username = 'root';
                $password = 'wRKVCAXctmaZqxdZXcqWcyxQeBUdvsjV';

                // Create a new connection
                self::$conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // Throw a clear error message if the connection fails
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn; // Return the connection
    }
}
?>
