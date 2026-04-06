<?php
namespace App\Models;

class User {

    public function __construct(
        public string $id,
        public string $name,
        public ?string $profileStatus,
        public ?string $avatar,
        public string $email,
        public string $password,
        public int $registeredAt
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            id: ($data['id']) ?? '',
            name: $data['name'] ?? '',
            profileStatus: $data['profileStatus'] ?? null,
            avatar: $data['avatar'] ?? null,
            email: $data['email'] ?? '',
            password: $data['password'] ?? '',
            registeredAt: (int)($data['registeredAt'] ?? time())
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,  
            'name' => $this->name,
            'profileStatus' => $this->profileStatus,
            'avatar' => $this->avatar,
            'email' => $this->email,
            'password' => $this->password, 
            'registeredAt' => $this->registeredAt,
        ];
    }

    public function toPublicArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profileStatus' => $this->profileStatus,
            'avatar' => $this->avatar,
            'registeredAt' => $this->registeredAt,
        ];
    }

    public function getAvatarUrl(): string {
        if ($this->avatar) {
            return "/uploads/avatars/{$this->avatar}";
        }
        return '/assets/images/default-avatar.png';
    }

    public function getEscapedName(): string {
        return htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8');
    }

    public function getEscapedStatus(): string {
        return htmlspecialchars($this->profileStatus ?? '', ENT_QUOTES, 'UTF-8');
    }
}