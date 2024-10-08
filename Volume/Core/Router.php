<?php

namespace Core;

use Core\Response;

class Router
{
    const ACCESS = 'Access';
    private static array $urlList;
    private static array $templates;

    public function __construct()
    {
        include (Routes);
        self::$urlList = $urlList;
        self::$templates = $templates;
    }

    public static function processRequest(object $request): void
    {
        if (array_key_exists($request->getRoute(), self::$urlList)) {
            if (!array_key_exists($request->getMethod(), self::$urlList[$request->getRoute()])) {
                $response = new Response('Not Found, 404');
                $response->sendResponse();
            }

            $obj = new self::$urlList[$request->getRoute()][$request->getMethod()][0];
            $method = self::$urlList[$request->getRoute()][$request->getMethod()][1];
            self::securityCheck(self::$urlList[$request->getRoute()][self::ACCESS]);
            call_user_func_array([$obj, $method], [$request]);
        } elseif (array_key_exists($request->getRoute(), self::$templates)) {
            $obj = new self::$templates[$request->getRoute()][0];
            $method = self::$templates[$request->getRoute()][1];
            self::securityCheck(self::$templates[$request->getRoute()][self::ACCESS]);
            call_user_func_array([$obj, $method], [$request]);
        }

        $response = new Response('Not Found, 404');
        $response->sendResponse();
    }

    private static function securityCheck(array $accessData): void
    {
        if (!App::getApp()->Security->isAllowed($accessData)) {
            $response = new Response('403 Forbidden');
            $response->sendResponse();
        }
    }
}