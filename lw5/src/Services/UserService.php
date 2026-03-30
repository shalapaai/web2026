<?php
namespace App\Services;
use App\Models\User;

class UserService extends JsonStorageService {

    public function getAll(): array {
        $data = $this->readJson();
        return array_map(fn($u) => User::fromArray($u), $data);
    }
    
    public function getById(int $id): ?User {
        foreach ($this->getAll() as $user) {
            if ($user->id === $id) {
                return $user;
            }
        }
        return null;
    }

    public function create($data, int $authorId): User {
        $users = $this->readJson();
        $id = end($users)['id'] + 1;
        $newUser = [
            'id' => $id,
            'name' => $data['name'],
            'profileStatus' => $data['profileStatus'],
            'avatar' => $data['uploadedImages'] ?? [],
            'email'=> $data['email'],
            'password' => $data['password'],
            'registeredAt' => time(),
        ];
        
        $users[] = $newUser;
        print_r($newUser);
        $this->writeJson($users);
        
        return User::fromArray($newUser);
    }

    // public function getByQuery(): Post {
    //     $id = $_GET['id'] ?? 1;
    //     if (!$id) return [];
    //     $id = (int)$id;
    //     $users = $this->readJson();
    //     $user = array_find($users, fn($u) => $u->id === $id);
    //     return $user;
    // }
}