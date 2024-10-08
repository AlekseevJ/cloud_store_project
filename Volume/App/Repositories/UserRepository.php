<?php

namespace App\Repositories;

use PDO;
use Core\DB\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'users';
    }

    public function getUserByCred(string $login, string $password): array
    {
        $stmt = $this->connection->prepare('SELECT id, login, role FROM users where login = ? and password = ?');
        $stmt->execute([$login, $password]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getUserByEmail(string $email): int
    {
        $stmt = $this->connection->prepare('SELECT count(id) FROM users where email = ?');
        $stmt->execute([$email]);
        $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $count[0]['count'];
    }

    public function updateUser(string $field, string $value, string $login): int
    {
        $sql = $this->connection->prepare("UPDATE users SET " . $field . " = :value WHERE login = :userid");
        $sql->execute(
            array(
                'value' => $value,
                'userid' => $login
            )
        );
        return $sql->rowCount();
    }

    public function register(string $login, string $email, string $hashed_password): int
    {
        $stmt = $this->connection->prepare("INSERT INTO {$this->table}(login, email, password, role) values(?, ?, ? , 2)");
        $stmt->execute([$login, $email, $hashed_password]);
        return $stmt->rowCount();
    }

    public function getUserByLogin(string $login): array
    {
        $stmt = $this->connection->prepare("SELECT id, login, role, password FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $user;
    }

    public function findAll(): array
    {
        $stmt = $this->connection->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser(string $login, string $email, string $password, int $role): int
    {
        $stmt = $this->connection->prepare("INSERT INTO {$this->table}(login, email, password, role) values(?, ?, ?, ?)");
        $stmt->execute([$login, $email, $password, $role]);
        return $stmt->rowCount();
    }

    public function findOne(int $id): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}