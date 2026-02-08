<?php
namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $instance = null;
    
    public static function getConnection() {
        if (self::$instance === null) {
            $host = "localhost";
            $db = "escuela";
            $user = "root";
            $pass = "";
            $charset = "utf8mb4";
            
            try {
                $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$instance = new PDO($dsn, $user, $pass, $options);
                
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}