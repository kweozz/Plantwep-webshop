<?php
require_once __DIR__ . '/../env.php';

class Db {
    // Declare the static property $conn
    private static $conn;

    // Method to get the database connection
    public static function getConnection() {
        // Load environment variables
        loadEnv(__DIR__ . '/../.env');

        // Check if the connection already exists
        if (self::$conn === null) {
            try {
                // Use environment variables for database connection details
                $host = getenv('MYSQLHOST');
                $port = getenv('MYSQLPORT');
                $dbname = getenv('MYSQLDATABASE');
                $username = getenv('MYSQLUSER');
                $password = getenv('MYSQLPASSWORD');

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
