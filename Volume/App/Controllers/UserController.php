<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Core\Response;
use Core\App;

class UserController extends BaseController
{
    public function list(): void
    {
        $response = new Response(App::getApp()->UserRepository->find());
        $response->sendResponse();
    }

    public function get(object $request): array
    {
        $id = $request->getData()['urlparams'];
        return App::getApp()->UserRepository->getUserById($id);
    }

    public function update(object $request): void
    {
        $field = array_key_first($request->getData()['params']);
        $value = array_shift($request->getData()['params']);
        App::getApp()->UserRepository->updateUser($field, $value, App::getApp()->Security->getUserData()['login']);
        $response = new Response('OK, 200');
        $response->addHeader(['Location' => '/']);
        $response->sendResponse();
    }

    public function login(object $request): void
    {
        if (isset($request->getData()['params']['login']) && isset($request->getData()['params']['password'])) {
            $login = $request->getData()['params']['login'];
            $password = $request->getData()['params']['password'];
            if (App::getApp()->UserService->authorize($login, $password)) {
                $this->standartResponseRedir();
            } else {
                $response = new Response('403');
                $response->addHeader(['Location' => '/']);
                $response->sendResponse();
            }
        } else {
            $response = new Response('401, Login and password required.');
            $response->addHeader(['Location' => '/']);
            $response->sendResponse();
        }
    }

    public function logout(): void
    {
        App::getApp()->UserService->logout();
        $this->standartResponseRedir();
    }

    public function reset_password(object $request): string
    {
        $email = $request->getData()['params']['email'];
        $count = App::getApp()->UserRepository->getUserByEmail($email);

        if ($count === 1) {
            $this->sendEmailReset($email);
            return 'Reset email sended';
        } else {
            return 'User not exists';
        }
    }

    public function sendEmailReset(string $email): void
    {
        //симуляция отправки send email
    }

    public function standartResponseRedir(): void
    {
        $response = new Response('200, ok');
        $response->addHeader(['Location' => '/']);
        $response->sendResponse();
    }

    public function register(object $request): void
    {
        $login = $request->getData()['params']['login'];
        $email = $request->getData()['params']['email'];
        $password = $request->getData()['params']['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if (App::getApp()->UserRepository->register($login, $email, $hashed_password) > 0) {
            $this->login($request);
            $response = new Response('401');
            $response->addHeader(['Location' => '/']);
            $response->sendResponse();
        } else {
            $response = new Response('200');
            $response->addHeader(['Location' => '/']);
            $response->sendResponse();
        }
    }
}