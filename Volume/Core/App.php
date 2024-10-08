<?php

namespace Core;

use Core\Router;
use Core\DB\PgsqlConnection;
use Core\Request;
use Core\Interfaces\ServiceInterface;
use Core\DB\Interfaces\RepositoryInterface;
use Core\Security\Security;
use Core\TemplateEngine\Interfaces\ViewInterface;

class App
{
    const APP = 'App';
    const APPPATH = '\app';
    const CORE = 'Core';
    const PHP = '.php';
    const ROUTER = 'Router';
    const REQUEST = 'Request';
    const TEMPLATES = 'Templates';
    const PROJECT = 'Project';
    const EXCEPTIONS = ['.', '..', 'Core', 'autoloader.php', 'Templates', 'Storage', 'Configs', 'Command'];
    const DB = 'Db';
    const SECURITY = 'Security';
    const MAINVIEW = 'MainView';

    public static array $binded = [];
    private static array $scanedFiles = [];

    public function __get(string $nameClass)
    {
        if (array_key_exists($nameClass, self::$binded)) {
            return self::$binded[$nameClass];
        } else {
            throw new \LogicException('The class must define');
        }
    }

    public static function getApp(): object
    {
        return self::$binded[self::APP];
    }

    public static function initApp(): void
    {
        self::bind(self::APP, new App);
    }

    public static function collectServices(): void
    {
        self::bindImplements(self::$scanedFiles, ServiceInterface::class);
    }

    public static function collectRepositories(): void
    {
        self::bindImplements(self::$scanedFiles, RepositoryInterface::class);
    }

    private static function scanFolder(string $folderPath = BasePath, array &$files = []): void
    {
        $rawFiles = array_diff(scandir($folderPath), self::EXCEPTIONS);

        foreach ($rawFiles as $file) {
            $filePath = $folderPath . '/' . $file;
            if (is_dir($filePath)) {
                self::scanFolder($filePath, $files);
            } elseif (str_contains($file, self::PHP)) {
                $files[] = $folderPath . '/' . $file;
            }
        }

        self::$scanedFiles = $files;
    }

    private static function bindImplements(array $classes, string $interface): void
    {
        foreach ($classes as $className) {
            $realClassName = str_replace(self::PHP, '', str_replace(self::APPPATH, '', str_replace('/', '\\', $className)));
            if (array_search($interface, class_implements($realClassName))) {
                $obj = new $realClassName;
                self::bind(str_replace('\\', '', strrchr($obj::class, '\\')), $obj);
            }
        }
    }

    public static function initConfigs(): void
    {
        define('BasePath', dirname(__DIR__));
        define('Routes', BasePath . '/App/Configs/routes.php');
        define('StoragePath', BasePath . '/App/Storage');
        define('AuthPath', BasePath . '/App/Storage/Auth');
        define('TemplatesPath', BasePath . '/App/Templates');
        $env = parse_ini_file(BasePath . '/' . '.env');
        foreach ($env as $key => $val) {
            $_ENV[$key] = $val;
        }
        self::scanFolder();
    }

    public static function initRouter(): void
    {
        self::bind(self::ROUTER, new Router());
    }

    public static function initDb(): void
    {
        self::bind(self::DB, PgsqlConnection::getInstance());
    }

    public static function initSecurity(): void
    {
        self::bind(self::SECURITY, new Security());
    }

    public static function collectViews(): void
    {
        self::bindImplements(self::$scanedFiles, ViewInterface::class);
    }

    private static function bind(string $id, object $service): void
    {
        self::$binded[$id] = $service;
    }

    public static function prepareRequest(): void
    {
        self::bind(self::REQUEST, new Request());
    }
}