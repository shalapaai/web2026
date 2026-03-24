<?php
namespace App\Models;

class Post {
    public function __construct(
        public int $id,
        public int $authorId,
        public string $content,
        public array $images,
        public int $likes,
        public int $createdAt
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            id: (int)($data['id'] ?? 0),
            authorId: (int)($data['authorId']),
            content: $data['content'] ?? '',
            images: (array)($data['images'] ?? []),
            likes: (int)($data['likes'] ?? 0),
            createdAt: (int)($data['createdAt'] ?? time())
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'authorId' => $this->authorId, 
            'content' => $this->content,
            'images' => $this->images,
            'likes' => $this->likes,
            'createdAt' => $this->createdAt,
        ];
    }

    public function getRelativeTime(): string {
        return self::formatRelativeTime($this->createdAt);
    }

    public static function formatRelativeTime(int $timestamp): string {
        $now = time();
        $diff = $now - $timestamp;

        // Если время в будущем (защита от неверных данных)
        if ($diff < 0) {
            return 'только что';
        }
        // Менее минуты
        if ($diff < 60) {
            return 'только что';
        }
        // Менее часа
        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return (string)$minutes . ' ' . self::pluralize($minutes, 'минуту', 'минуты', 'минут') . ' назад';
        }
        // Менее дня
        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return (string)$hours . ' ' . self::pluralize($hours, 'час', 'часа', 'часов') . ' назад';
        }
        // Менее недели
        if ($diff < 604800) {
            $days = floor($diff / 86400);
            return (string)$days . ' ' . self::pluralize($days, 'день', 'дня', 'дней') . ' назад';
        }
        // Менее месяца (примерно 4 недели)
        if ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return (string)$weeks . ' ' . self::pluralize($weeks, 'неделю', 'недели', 'недель') . ' назад';
        }
        // Менее года
        if ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return (string)$months . ' ' . self::pluralize($months, 'месяц', 'месяца', 'месяцев') . ' назад';
        }
        // Год и более
        $years = floor($diff / 31536000);
        return (string)$years . ' ' . self::pluralize($years, 'год', 'года', 'лет') . ' назад';
    }

    public static function pluralizePosts(int $count): string {
        return self::pluralize($count, 'пост', 'поста', 'постов');
    }


    private static function pluralize(int $count, string $one, string $few, string $many): string {
        $lastDigit = $count % 10;
        $lastTwo = $count % 100;

        // 11-14 всегда "many"
        if ($lastTwo >= 11 && $lastTwo <= 14) {
            return $many;
        }
        // 1 -> "one" 
        if ($lastDigit === 1) {
            return $one;
        }

        // 2-4 -> "few"
        if ($lastDigit >= 2 && $lastDigit <= 4) {
            return $few;
        }

        // 5-9, 0 -> "many"
        return $many;
    }

    public function getFirstImage(): ?string {
        return $this->images[0] ?? null;
    }

    public function getImagesCount(): int {
        return count($this->images);
    }
}