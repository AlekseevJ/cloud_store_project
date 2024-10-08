<?php

namespace Core\DB\Interfaces;

interface RepositoryInterface
{
    public function find(int $id): array;

    public function findOneBy(array $criteria = [], array $sort = []): array;

    public function findBy(array $criteria = [], array $sort = [], int $limit = 20, int $offset = 0): array;

    public function deleteOne(int $id): int;

    public function update(array $fieldValue, array $criteria): int;
}