<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv as Dotenv;

class Db {
    // Declare the static property $conn
    private static $conn;

    // Method to get the database connection
    public static function getConnection() {
        // Load environment variables
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        // Check if the connection already exists
        if (self::$conn === null) {
            try {
                // Use Railway database connection details from environment variables
                $host = $_ENV['MYSQLHOST'];
                $port = $_ENV['MYSQLPORT'];
                $dbname = $_ENV['MYSQLDATABASE'];
                $username = $_ENV['MYSQLUSER'];
                $password = $_ENV['MYSQLPASSWORD'];

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
