<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Core\App;
use Core\Response;

class UserController extends BaseController
{
    public function list(): array
    {
        return App::getApp()->UserRepository->find();
    }

    public function getUser(object $request): array
    {
        return App::getApp()->UserRepository->findOneBy($request->getData()['urlparams']);
    }

    public function deleteUser(object $request): void
    {
        App::getApp()->UserRepository->delete((int) $request->getData()['urlparams']);
        $response = new Response('OK, 200');
        $response->addHeader(['Location' => '/adminpanel']);
        $response->sendResponse();
    }

    public function updateUser(object $request): void
    {
        $id = $request->getData()['params']['id'];
        $parameters = $request->getData()['params'];
        foreach ($parameters as $key => $value) {
            if ($key == 'password') {
                if ($value == '') {
                    continue;
                }
                $hashed = password_hash($value, PASSWORD_DEFAULT);
                App::getApp()->UserRepository->update($id, $key, $hashed);
            } else {
                App::getApp()->UserRepository->update($id, $key, $value);
            }
        }
        $response = new Response('');
        $response->addHeader(['Location' => '/adminpanel']);
        $response->sendResponse();
    }

    public function createUser(object $request): void
    {
        $login = $request->getData()['params']['login'];
        $password = password_hash($request->getData()['params']['password'], PASSWORD_DEFAULT);
        $email = $request->getData()['params']['email'];
        $role = (int) $request->getData()['params']['role'];
        App::getApp()->UserRepository->createUser($login, $email, $password, $role);
        $response = new Response('');
        $response->addHeader(['Location' => '/adminpanel']);
        $response->sendResponse();
    }
}