<?php
class Db {
    private static $conn;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                $host = getenv('MYSQLHOST');
                $port = getenv('MYSQLPORT');
                $dbname = getenv('MYSQLDATABASE');
                $username = getenv('MYSQLUSER');
                $password = getenv('MYSQLPASSWORD');

                self::$conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}

?>
