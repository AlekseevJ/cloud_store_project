<?php

namespace Core\TemplateEngine;

use Core\TemplateEngine\Interfaces\ViewInterface;

abstract class View implements ViewInterface
{
    public function render(string $templatePath, array $data): string
    {
        $templateData = file_get_contents($templatePath);

        foreach ($data as $key => $value) {
            $templateData = str_replace($key, $value, $templateData);
        }

        return $templateData;
    }
}