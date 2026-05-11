<?php
namespace App\Services;
use App\Core\BaseController;
use App\Models\User;
use PDO;
use App\Core\Database;

class UserService extends BaseController {

    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAllUserList(): array {
        $query = <<<SQL
            SELECT 
                id,
                name,
                avatar,
                profileStatus
            FROM user
        SQL;
        $stmt = $this->pdo->query( $query);
        $data = $stmt->fetchAll();
        return array_map(fn($u) => User::fromArray($u), $data);
    }
    
    public function getUserById(string $id): ?User {
        $query = <<<SQL
            SELECT 
                id,
                name,
                avatar,
                profileStatus
            FROM user
            WHERE id = ?
        SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? User::fromArray($data) : null;
    }

    public function getUserByEmail(string $email): ?User {
        $query = <<<SQL
            SELECT
                id 
                email,
                password
            FROM user
            WHERE email = ?
        SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$email]);
        $data = $stmt->fetch();
        return $data ? User::fromArray($data) : null;
    }

    protected function getCurrentUserId(): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        return null;
    }

    public function createUser(string $email, string $password): User {
        $id = $this->generateUuid();
        $timestamp = time();
        $name = 'Аноним';
        $query = <<<SQL
            INSERT INTO
                user (
                    id,
                    email,
                    password,
                    name,
                    registeredAt
                )
            VALUES (
                ?, ?, ?, ?, ?
            )
        SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id, $email, $password, $name, $timestamp]);
        $data = [
            'email' => $email, 
            'password' => $password, 
            'name' => $name, 
            'id' => $id, 
            'registeredAt' => $timestamp
        ];
        return $data ? User::fromArray($data) : null;
    }
}