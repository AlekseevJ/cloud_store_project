<?php

namespace Core;

use Core\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    const PARAMS = 'params';
    const FILES = 'files';
    const URLPARAMS = 'urlparams';
    const TWOPARAMS = 'twopar';
    const PHP_INPUT = 'php://input';
    const POST = 'POST';
    const GET = 'GET';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_URI = 'REQUEST_URI';
    private array $data = [self::PARAMS => [], self::FILES => [], self::URLPARAMS => [], self::TWOPARAMS => []];
    private string $method;
    private string $route;

    public function __construct(
    ) {
        $this->retriveRequestParameters($_SERVER[self::REQUEST_URI]);
        $this->retrieveMethod();
        $this->retriveFiles();
    }

    public function __get(string $name)
    {
        return $this->getData()[$name];
    }

    private function retrieveMethod(): void
    {
        $this->method = $_SERVER[self::REQUEST_METHOD];
    }

    private function retriveRequestParameters(string $route): void
    {
        $match = [];
        $countParam = preg_match_all('/(\d+)/', $route, $match);

        if (str_contains($route, '?')) {
            $this->route = stristr($route, '?', true);
        } else {
            $this->parseParams($match, $countParam, $route);
        }
    }

    private function parseParams(array $match, int $countParam, string $route): void
    {
        if ($countParam == 1) {
            $id = preg_replace('/[^0-9]/', '', $route);
            $this->route = str_replace('/' . $id, '', $route);
            $this->data[self::URLPARAMS] = $id;
        } elseif ($countParam == 2) {
            $this->route = str_replace('/' . $match[0][0] . '/' . $match[0][1], '', $route);
            $this->data[self::TWOPARAMS] = [$match[0][0], $match[0][1]];
        } else {
            $this->route = $route;
        }
    }

    private function retriveFiles(): void
    {
        if ($this->method == self::GET) {
            $this->data[self::PARAMS] = $_GET;
        } elseif ($this->method == self::POST) {
            $this->data[self::PARAMS] = $_POST;
            
            if (isset($_FILES)) {
                $this->data[self::FILES] = $_FILES;
            }
        } else {
            parse_str(file_get_contents(self::PHP_INPUT), $parsedData);

            if (isset($parsedData)) {
                $this->data[self::PARAMS] = $parsedData;
            }
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}