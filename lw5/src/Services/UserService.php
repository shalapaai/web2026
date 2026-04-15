<?php
namespace App\Services;
use App\Models\User;
use PDO;
use App\Core\Database;

class UserService {

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

    public static function getCurrentUserId(): string {
        return '0874af11-e313-4e09-8c10-b233f293bf70';
    }

    // public function create($data, int $authorId): User {
    //     $users = $this->readJson();
    //     $id = end($users)['id'] + 1;
    //     $newUser = [
    //         'id' => $id,
    //         'name' => $data['name'],
    //         'profileStatus' => $data['profileStatus'],
    //         'avatar' => $data['uploadedImages'] ?? [],
    //         'email'=> $data['email'],
    //         'password' => $data['password'],
    //         'registeredAt' => time(),
    //     ];
        
    //     $users[] = $newUser;
    //     print_r($newUser);
    //     $this->writeJson($users);
        
    //     return User::fromArray($newUser);
    // }

    // public function getByQuery(): Post {
    //     $id = $_GET['id'] ?? 1;
    //     if (!$id) return [];
    //     $id = (int)$id;
    //     $users = $this->readJson();
    //     $user = array_find($users, fn($u) => $u->id === $id);
    //     return $user;
    // }
}