<?php

namespace Core\DB;

use Core\App;
use PDO;
use Core\DB\Interfaces\RepositoryInterface;

abstract class AbstractRepository implements RepositoryInterface
{
    protected object $connection;
    protected string $table;

    public function __construct()
    {
        $this->connection = App::getApp()->Db->getConnection();
    }

    public function find(int $offset = 0): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} LIMIT 20 OFFSET ?");
        $stmt->execute([$offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOneBy(array $criteria = [], array $sort = []): array
    {
        $orderStmt = '';
        $stmtAndValue = $this->prepareStmtAndValue($criteria, 'WHERE');

        if (isset($sort[0]) && isset($sort[1])) {
            $orderStmt = "ORDER BY {$sort[0]} {$sort[1]}";
        }

        $stmt = $this->connection->prepare("SELECT * FROM {$this->table} " . $stmtAndValue[0] . $orderStmt . ' LIMIT 1');
        $stmt->execute($stmtAndValue[1]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findBy(array $criteria = [], array $sort = [], int $limit = 20, int $offset = 0): array
    {
        $stmtWhereAndValue = $this->prepareStmtAndValue($criteria, 'WHERE');
        $sql = "SELECT * FROM {$this->table} ". $stmtWhereAndValue[0] . " LIMIT {$limit} OFFSET {$offset}";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($stmtWhereAndValue[1]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteOne(int $id): int
    {
        $stmt = $this->connection->prepare("DELETE FROM {$this->table} where id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function update(array $fieldValue, array $criteria): int
    {
        $setSt = $this->prepareStmtAndValue($fieldValue, 'SET');
        $whereSt = $this->prepareStmtAndValue($criteria, 'WHERE');
        $keyValue = array_merge($setSt[1], $whereSt[1]);
        $stmt = $this->connection->prepare("UPDATE {$this->table}" . $setSt[0] . $whereSt[0]);
        $stmt->execute($keyValue);
        return $stmt->rowCount();
    }

    protected function prepareStmtAndValue(array $fieldValue, string $action): array
    {
        $c = 0;
        $statment = [''];
        $keyValueParameters = [];

        foreach ($fieldValue as $key => $value) {
            if ($c == 0) {
                $statment = $action . ' :' . $key . '=?';
            } else {
                $statment .= ', :' . $key . '=?';
            }
            $keyValueParameters[] = [$key => $key, $value];
            array_push($keyValueParameters, $value);
            $c++;
        }

        return [$statment, $keyValueParameters];
    }
}