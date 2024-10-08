<?php

namespace Core\DB\Interfaces;

interface DbInterface
{
    public function getConnection(): object;
}