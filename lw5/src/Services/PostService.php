<?php
namespace App\Services;
use App\Models\Post;

class PostService extends JsonStorageService {

    public function getAll(): array {
        $data = $this->readJson();
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    public function getById(int $id): ?Post {
        foreach ($this->getAll() as $post) {
            if ($post->id === $id) {
                return $post;
            }
        }
        return null;
    }
    
    public function getByAuthorId(int $authorId): array {
        return array_values(array_filter(
            $this->getAll(),
            fn(Post $p) => $p->authorId === $authorId
        ));
    }

    public function create(array $data, int $authorId): Post {
        $posts = $this->readJson();
        
        $newPost = [
            'id' => time(),
            'authorId' => $authorId,
            'content' => trim($data['content'] ?? ''),
            'images' => $data['images'] ?? [],
            'likes' => 0,
            'createdAt' => time(),
        ];
        
        $posts[] = $newPost;
        $this->writeJson(array_map(fn(Post $p) => $p->toArray(), $this->getAll()));
        
        return Post::fromArray($newPost);
    }

    public function printPostCount(int $cnt): string {
        if (!$cnt > 10 && !$cnt < 15) {
            $cnt = $cnt % 10;
        }
        switch ($cnt) {
            case 1:
                return 'пост';
            case 2:
            case 3:
            case 4:
                return 'поста';
            default: 
                return 'постов';
        }
    }
}
?>