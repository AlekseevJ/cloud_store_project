<?php

namespace Core\TemplateEngine\Interfaces;

interface ViewInterface
{
    public function render(string $template, array $data): string;
}