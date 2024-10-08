<?php

namespace Core\Security;

use Core\App;
use DateTime;

class Security
{
    const ROLE = 'role';
    const AUTH = 'auth';
    const TIME_FORMAT = 'Y-m-d H:i:s';
    const DAY_FORMAT = '%a';
    const DATE = 'date';
    const OLD_TOKEN = 'old_token';
    const TOKEN = 'token';
    const ID = 'id';
    const LOGIN = 'login';
    const OLD_DATA = 'old_data';
    const ADMIN_ROLE_ID = 1;
    const USER_ROLE_ID = 2;
    const EXPIRED_DAYS = 1;
    private array $userData = [];

    public function isAllowed(array $accessData): bool
    {
        if (
            $accessData[self::AUTH] == false
            || ($this->setUserData() && $this->checkDate() && $this->checkDb() && $this->checkRole($accessData[self::ROLE]))
        ) {
            return true;
        }

        return false;
    }

    private function checkDate(): bool
    {
        $nowTime = new DateTime();
        $date = DateTime::createFromFormat(self::TIME_FORMAT, $this->userData[self::DATE]);
        $interval = $nowTime->diff($date);

        if ((int) $interval->format(self::DAY_FORMAT) >= self::EXPIRED_DAYS) {
            App::getApp()->userService->logout();
            return false;
        }

        return true;
    }

    private function setUserData(): bool
    {
        if (isset($_SESSION[self::TOKEN])) {
            $this->userData = unserialize(base64_decode(file_get_contents(AuthPath . '/' . $_SESSION[self::TOKEN])));
            return true;
        }

        return false;
    }

    private function checkDb(): bool
    {
        $user = App::getApp()->UserRepository->findOne((int) $this->userData[self::ID]);

        if ($user[0][self::LOGIN] == $this->userData[self::LOGIN] && $user[0][self::ROLE] == $this->userData[self::ROLE]) {
            return true;
        }

        App::getApp()->userService->logout();
        return false;
    }

    public function getUserData(): array
    {
        if (isset($this->userData[self::ID])) {
            return $this->userData;
        }

        $this->setUserData();
        return $this->userData;
    }

    private function checkRole(int $accessRole): bool
    {
        if ($this->adminAccess($accessRole) || $this->userAccess($accessRole)) {
            return true;
        }

        return false;
    }

    private function adminAccess(int $accessRole): bool
    {
        return $accessRole == self::ADMIN_ROLE_ID && (int) $this->userData[self::ROLE] == self::ADMIN_ROLE_ID;
    }

    private function userAccess(int $accessRole): bool
    {
        return $accessRole == self::USER_ROLE_ID
            && ((int) $this->userData[self::ROLE] == self::ADMIN_ROLE_ID || (int) $this->userData[self::ROLE] == self::USER_ROLE_ID);
    }
}