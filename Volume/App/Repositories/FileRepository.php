<?php

namespace App\Repositories;

use Core\App;
use PDO;
use Core\DB\AbstractRepository;

class FileRepository extends AbstractRepository
{

    public function __construct()
    {
        parent::__construct();
        $this->table = 'files';
    }

    public function createFile(string $path, string $name, int $idUser, string $ext): int
    {
        $stmt = $this->connection->prepare('insert into files(path, name, access, ext) values (?, ?, ?, ?)');
        $stmt->execute([$path, $name, $idUser, $ext]);
        return $this->connection->lastInsertId();
    }

    public function renameFile(int $id, string $newName): int
    {
        $stmt = $this->connection->prepare("UPDATE files SET name = ? WHERE id = ?");
        $stmt->execute([$newName, $id]);
        return $stmt->rowCount();
    }

    public function createDirectory(string $name, string $path, int $access): int
    {
        $stmt = $this->connection->prepare("INSERT INTO files(path, name, is_dir, access) values (?, ?, true, ?)");
        $stmt->execute([$path, $name, $access]);
        return $stmt->rowCount();
    }

    public function renameDirectory(int $id, string $newName): int
    {
        $stmt = $this->connection->prepare("UPDATE files SET name =? WHERE id =?");
        $stmt->execute([$newName, $id]);
        return $stmt->rowCount();
    }

    public function listDir(int $id): array
    {
        $query = $this->connection->prepare("SELECT * FROM files where id = ?");
        $query->execute([$id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteDirectory(int $id): string
    {
        $query = $this->connection->prepare("SELECT * FROM files where id = ?");
        $query->execute([$id]);
        $path = App::getApp()->FileService->getNewDirPath($query->fetchAll(PDO::FETCH_ASSOC));
        $stmt = $this->connection->prepare("DELETE FROM files where id = ?");
        $stmt->execute([$id]);
        return $path;
    }

    public function shareFileUsers(int $id): array
    {
        $query = $this->connection->prepare("SELECT access FROM files where id = ?");
        $query->execute([$id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setShareFile(int $idFile, int $idUser): int
    {
        $oldAccess = $this->shareFileUsers($idFile);

        if ($oldAccess[0]['access'] == '') {
            $newAccess = $idUser;
        } else {
            $newAccess = $oldAccess[0]['access'] . ',' . $idUser;
        }

        $query = $this->connection->prepare("UPDATE files SET access = ? where id = ? ");
        $query->execute([$newAccess, $idFile]);
        return $query->rowCount();
    }

    public function unsetShareFile(int $idFile, int $idUser): int
    {
        $oldAccess = $this->shareFileUsers($idFile);
        $userAccess = explode(',', $oldAccess[0]['access']);
        unset($userAccess[array_search($idUser, $userAccess)]);
        $newAccess = implode(',', $userAccess);
        $query = $this->connection->prepare("UPDATE files SET access = ? where id = ? ");
        $query->execute([$newAccess, $idFile]);
        return $query->rowCount();
    }

    public function getFilesWithAccess(string $idPath, int $idUser, int $page = 1): array
    {
        $query = $this->connection->prepare("SELECT * FROM files where path = ? and access = ? LIMIT 20 OFFSET 20 * ?");
        $query->execute([$idPath, $idUser, $page - 1]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilesWithoutAccess(string $path, int $page = 1): array
    {
        $query = $this->connection->prepare("SELECT * FROM files where path = ? LIMIT 20 OFFSET 20 * ?");
        $query->execute([$path, $page - 1]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(string $path = '/'): int
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM {$this->table} WHERE path = ?");
        $stmt->execute([$path]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['count'];
    }

    public function findAll(string $path = '/'): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} where path = ?");
        $stmt->execute([$path]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}