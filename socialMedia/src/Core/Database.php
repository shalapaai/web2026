<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private PDO $connection;
    
    private function __construct() {
        try {
            $configPath = __DIR__ . '/../../db-config.json';   
            $json = file_get_contents($configPath);
            if ($json === false) {
                throw new \Exception("Не удалось прочитать файл: {$configPath}");
            }
            $config = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(
                    "Ошибка JSON в {$configPath}: " . json_last_error_msg()
                );
            }         
            
            $host = $config['host'];
            $port = $config['port'];
            $dbname = $config['dbname'];
            $username = $config['username'];
            $password = $config['password'];

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO {
        return $this->connection;
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}