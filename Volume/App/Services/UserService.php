<?php

namespace App\Services;

use Core\App;
use Core\Interfaces\ServiceInterface;

class UserService implements ServiceInterface
{
    public function authorize(string $login, string $password): bool
    {
        $user = App::getApp()->UserRepository->getUserByLogin(trim($login));
        $userCred = array_shift($user);

        if (isset($userCred['password']) && password_verify($password, $userCred['password'])) {
            $payload = ['id' => $userCred['id'], 'login' => $userCred['login'], 'role' => $userCred['role'], 'auth' => true, 'date' => date('Y-m-d H:i:s')];
            $chars = implode('', range('a', 'z')) . implode('', range(0, 9));
            $token = hash('sha256', str_shuffle($chars));
            $_SESSION['token'] = $token;
            file_put_contents(AuthPath . '/' . $token, base64_encode(serialize($payload)));
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        if (file_exists(AuthPath . '/' . $_SESSION['token'])) {
            unlink(AuthPath . '/' . $_SESSION['token']);
        }

        session_destroy();
    }

}