<?php 

namespace Core\Interfaces;

interface ResponseInterface
{
    public function setData(mixed $data): void;

    public function setHeaders(array $headers): void;
}