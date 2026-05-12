<?php
namespace App\Services;
use App\Core\BaseController;
use App\Models\Post;
use PDO;
use App\Core\Database;

class PostService extends BaseController {

    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAllPostList(): array {
        $query = <<<SQL
            SELECT 
                post.id,
                post.authorId,
                post.content,
                post.likes,
                post.createdAt,
                GROUP_CONCAT(image.path ORDER BY image.path SEPARATOR ',') AS images
            FROM post
            LEFT JOIN image ON post.id = image.postId
            GROUP BY post.id
            ORDER BY post.createdAt DESC
            SQL;
        $stmt = $this->pdo->query($query);
        $data = $stmt->fetchAll();
        foreach ($data as &$post) {
            $post = $this->convertImagesToArray($post);
        }
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    public function getPostById(string $id): ?Post {
        $query = <<<SQL
            SELECT 
                post.id,
                post.authorId,
                post.content,
                post.likes,
                post.createdAt,
                GROUP_CONCAT(image.path ORDER BY image.path SEPARATOR ',') AS images
            FROM post
            LEFT JOIN image ON post.id = image.postId
            WHERE id = ?
            GROUP BY post.id
            SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        $data = $this->convertImagesToArray($data);
        return $data ? Post::fromArray($data) : null;
    }
    
    public function getPostsByAuthorId(string $authorId): array {
        $query = <<<SQL
            SELECT 
                post.id,
                post.authorId,
                post.content,
                post.likes,
                post.createdAt,
                GROUP_CONCAT(image.path ORDER BY image.path SEPARATOR ',') AS images
            FROM post
            LEFT JOIN image ON post.id = image.postId
            WHERE authorId = ?
            GROUP BY post.id
            ORDER BY post.createdAt DESC
            SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$authorId]);
        $data = $stmt->fetchAll();
        foreach ($data as &$post) {
            $post = $this->convertImagesToArray($post);
        }
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    public function createPost(array $data, string $authorId): string {
        $id = $this->generateUuid();
        $data['id'] = $id; 
        $data['authorId'] = $authorId;
        $this->saveToPostTable($data);
        $images = $data['uploadedImages'];
        foreach ($images as $image) {
            $this->saveToImageTable($id, $image);
        }
        return $id;
    }

    public function updatePost(array $data, string $authorId): bool {
        $postId = $data['id'] ?? null;
        if (!$postId) {
            throw new \Exception('Post ID is required');
        }
        $query = "SELECT id FROM post WHERE id = ? AND authorId = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$postId, $authorId]);
        if (!$stmt->fetch()) return false;
        if (isset($data['content'])) {
            $query = "UPDATE post SET content = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$data['content'], $postId]);
        }
        foreach ($data['newImages'] ?? [] as $imagePath) {
            $this->saveToImageTable($postId, $imagePath);
        }
        $removedPaths = $data['removedImagePaths'] ?? [];
        if (!empty($removedPaths)) {
            foreach ($removedPaths as $path) {
                $normalizedPath = $this->normalizeImagePath($path);
                $query = "DELETE FROM image WHERE postId = ? AND path = ?";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([$postId, $normalizedPath]);
                $this->deleteImageFile($path);
            }
        }
        return true;
    }

    private function normalizeImagePath(string $path): string {
        // Убираем префикс /uploads/posts/ если есть
        $normalized = preg_replace('#^/uploads/posts/#i', '/', $path);
        // Убираем дубли слэшей
        $normalized = preg_replace('#/+#', '/', $normalized);
        return $normalized;
    }

    private function deleteImageFile(string $path): void {
        // Нормализуем путь
        $path = preg_replace('#/+#', '/', $path);
        $path = trim($path, '/');
        
        // Защита от обхода через ../
        if (strpos($path, '..') !== false) {
            error_log("Blocked path traversal: $path");
            return;
        }
        
        // Разрешаем только uploads/posts
        if (strpos($path, 'uploads/posts') !== 0) {
            error_log("Blocked deletion outside allowed dir: $path");
            return;
        }
        
        // Собираем абсолютный путь
        $publicDir = realpath(__DIR__ . '/../../public');
        $filePath = realpath($publicDir . '/' . $path);
        
        if ($filePath && strpos($filePath, $publicDir) === 0 && file_exists($filePath)) {
            @unlink($filePath);  // @ подавляет предупреждения
            error_log("Deleted image: $filePath");
        }
    }

    private function saveToPostTable(array $data): void {
        $timestamp = time();
        $query = <<<SQL
            INSERT INTO post (id, authorId, content, createdAt)
            VALUES (?, ?, ?, $timestamp)
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$data['id'], $data['authorId'], $data['content']]);
        return;
    }

    private function saveToImageTable(string $id, string $imagePath): void {
        $query = <<<SQL
            INSERT INTO image (postId, path)
            VALUES (?, ?)
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$id, $imagePath]);
        return;
    }

    public function uploadImages(?array $files): array {
        if (!$files || empty($files['name'][0])) {
            return [];
        }
        $uploaded = [];
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../public/uploads/posts/';
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                error_log("Ошибка загрузки файла #{$i}: " . $this->getUploadErrorMessage($files['error'][$i]));
                continue;
            }
            if (!in_array($files['type'][$i], $allowedTypes)) {
                throw new \Exception("Недопустимый формат файла #{$i}: {$files['type'][$i]}");
            }
            if ($files['size'][$i] > $maxSize) {
                throw new \Exception("Файл #{$i} слишком большой: " . round($files['size'][$i] / 1024 / 1024, 2) . "MB");
            }
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '_' . $i . '.' . $extension;
            $destination = $uploadDir . $fileName;
            if (!move_uploaded_file($files['tmp_name'][$i], $destination)) {
                throw new \Exception("Не удалось сохранить файл #{$i}");
            }
            $uploaded[] = '/' . $fileName;
        }
        
        return $uploaded;
    }

    private function getUploadErrorMessage(int $errorCode): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Превышен upload_max_filesize в php.ini',
            UPLOAD_ERR_FORM_SIZE => 'Превышен MAX_FILE_SIZE в форме',
            UPLOAD_ERR_PARTIAL => 'Файл загружен частично',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION => 'Загрузка прервана расширением PHP',
        ];
        return $messages[$errorCode] ?? 'Неизвестная ошибка';
    }

    private function convertImagesToArray(array $post): array {
        $post['images'] = empty($post['images']) 
            ? [] 
            : array_map('trim', explode(',', $post['images']));
        return $post;
    }

    public function findLike(string $userId, string $postId): bool {
        $query = <<<SQL
            SELECT *
            FROM likes
            WHERE userId = ? AND postId = ?
            SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $postId]);
        return (bool) $stmt->fetch();
    }

    public function addLike(string $userId, string $postId, int $postLikes) {
        $this->addLikeToLikesTable($userId, $postId);
        $this->updateLikeToPostTable($postId, $postLikes);
    }

    private function addLikeToLikesTable(string $userId, string $postId) {
        $query = <<<SQL
            INSERT INTO likes (userId, postId)
            VALUES (?, ?)
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$userId, $postId]);
        return;
    }

    public function removeLike(string $userId, string $postId, int $postLikes) {
        $this->removeLikeFromLikesTable($userId, $postId);
        $this->updateLikeToPostTable($postId, $postLikes);
    }

    private function removeLikeFromLikesTable(string $userId, string $postId) {
        $query = <<<SQL
            DELETE FROM likes
            WHERE userId = ? AND postId = ?
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$userId, $postId]);
        return;
    }

    private function updateLikeToPostTable(string $postId, int $postLikes) {
        $query = <<<SQL
            UPDATE post 
            SET likes = ?
            WHERE id = ?
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$postLikes, $postId]);
        return;
    }
}