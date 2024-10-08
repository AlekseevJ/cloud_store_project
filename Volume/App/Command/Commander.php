<?php

namespace App\Command;

use Core\App;
use Exception;
use PDO;

class Commander
{
    public function runMigration(string $name = ''): void
    {
        $connection = App::getApp()->Db->getConnection();
        $migrationDb = $this->getMigrations();
        $migrations = $this->scanDir();

        foreach ($migrations as $migration) {
            $sql = [file_get_contents('./Migrations/' . $migration)];
            foreach ($sql as $sqlcommand) {
                if (in_array($migration, array_column($migrationDb, 'name'))) {
                    continue;
                }

                $connection->beginTransaction();

                try {
                    $connection->query($sqlcommand);
                    $connection->query("INSERT INTO migrations(name) VALUES('{$migration}')");
                    $connection->commit();
                } catch (Exception $e) {
                    $connection->rollback();
                }
            }
        }
    }

    private function scanDir(): array
    {
        return array_diff(scandir('./Migrations'), ['.', '..']);
    }

    private function getMigrations(): array
    {
        $connection = App::getApp()->Db->getConnection();
        try {
            $connection->query("SELECT * FROM migrations LIMIT 1");
        } catch (\PDOException $e) {
            if ($e->getCode() == '42P01') {
                $connection->query("CREATE TABLE IF NOT EXISTS migrations (id serial PRIMARY KEY, name varchar(255))");
            }
        }
        $res = $connection->query("SELECT name FROM migrations", PDO::FETCH_ASSOC);
        return $res->fetchAll();
    }
}