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
                id, 
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

    public function updateUser(string $userId, array $data): ?User {
        $oldAvatar = null;
        $query = "SELECT avatar FROM user WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $oldAvatar = $result['avatar'];
        }
        $updateData = [
            'name' => $data['name'] ?? '',
            'profileStatus' => $data['profileStatus'] ?? '',
            'avatar' => $data['avatar'] ?? $oldAvatar,  
            'id' => $userId
        ];
        $query = <<<SQL
            UPDATE user
            SET 
                name = ?,
                profileStatus = ?,
                avatar = ?
            WHERE id = ?
            SQL;
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute([
            $updateData['name'],
            $updateData['profileStatus'],
            $updateData['avatar'],
            $userId
        ]);
        if (!$success) return null;
        
        $newAvatar = $updateData['avatar'];
        if ($oldAvatar && $newAvatar && $oldAvatar !== $newAvatar) {
            $this->deleteOldAvatar($oldAvatar);
        }
        return User::fromArray($updateData);
    }

    private function deleteOldAvatar(string $avatarPath): void {
        if (strpos($avatarPath, 'default-avatar') !== false) {
            return;
        }
        // Нормализуем путь: убираем дубли слэшей, проверяем на ../
        $avatarPath = preg_replace('#/+#', '/', $avatarPath);
        if (strpos($avatarPath, '..') !== false) {
            error_log("Blocked path traversal attempt: $avatarPath");
            return;
        }
        // Собираем абсолютный путь к файлу
        $publicDir = realpath(__DIR__ . '/../../public/uploads/avatars');
        $filePath = realpath($publicDir . $avatarPath);
        // Проверяем, что файл существует и находится внутри public/
        if ($filePath && strpos($filePath, $publicDir) === 0 && file_exists($filePath)) {
            @unlink($filePath);  // @ подавляет предупреждения, если файл уже удалён
            error_log("Deleted old avatar: $filePath");
        }
    }

    public function uploadAvatar(array $file): string {
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Ошибка загрузки файла');
        }
        if (!in_array($file['type'], $allowedTypes)) {
            throw new \Exception('Недопустимый формат файла');
        }
        if ($file['size'] > $maxSize) {
            throw new \Exception('Файл слишком большой');
        }
        // Генерируем уникальное имя
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \Exception('Не удалось сохранить файл');
        }
        // Возвращаем относительный путь для БД
        return '/' . $fileName;
    }
}