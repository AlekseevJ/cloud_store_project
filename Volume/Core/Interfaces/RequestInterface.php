<?php 

namespace Core\Interfaces;

interface RequestInterface
{
    public function getData(): array;

    public function getRoute(): string;

    public function getMethod(): string;
}