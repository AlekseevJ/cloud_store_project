<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Core\App;
use Core\Response;

class ViewController extends BaseController
{
    private array $userData;

    public function __construct()
    {
        $this->userData = App::getApp()->Security->getUserData() == [] ? [] : App::getApp()->Security->getUserData();
    }

    public function homePage(): void
    {
        $registerForm = '';
        $adminPanel = '';

        if (isset($this->userData['auth']) && $this->userData['auth'] == true) {
            $log = file_get_contents(TemplatesPath . '/logoutform');
            $heloUser = App::getApp()->MainView->render(TemplatesPath . '/hellouser', ['$$name$$' => $this->userData['login']]);
        } else {
            $log = file_get_contents(TemplatesPath . '/loginform');
            $registerForm = file_get_contents(TemplatesPath . '/register');
            $heloUser = '';
        }
        if (isset($this->userData['role']) && $this->userData['role'] == 1) {
            $adminPanel = '<li><a color="222222"  href="/adminpanel">Администрирование</a></li>';
        }
        if (isset($this->userData['id'])) {
            $linktoFiles = '<li><a color="222222"  href="/files">Мои файлы</a></li>';
        } else {
            $linktoFiles = '';
        }

        $preparedData = ['$$adminpanel$$' => $adminPanel, '$$register$$' => $registerForm, '$$logform$$' => $log, '$$hellouser$$' => $heloUser, '$$linktofiles$$' => $linktoFiles];
        $response = new Response(App::getApp()->MainView->render(TemplatesPath . '/home.php', $preparedData));
        $response->view();
    }

    public function filePage(object $request): void
    {
        if (isset($request->getData()['params']['path']) && $request->getData()['params']['path'] !== '/') {
            $path = $request->getData()['params']['path'] . '/';
        } else {
            $path = '/';
        }
        if (isset($request->getData()['params']['page'])) {
            $page = $request->getData()['params']['page'];
        } else {
            $page = 1;
        }

        $countPages = 0;

        if (isset($this->userData['role']) && $this->userData['role'] == 1) {
            $files = App::getApp()->FileRepository->getFilesWithoutAccess($path, $page);
            $countPages = App::getApp()->FileRepository->count($path) / 20;
        } elseif (isset($userData['id'])) {
            $files = App::getApp()->FileRepository->getFilesWithAccess($path, $this->userData['id'], $page);
            $countPages = App::getApp()->FileRepository->count($path) / 20;
        } else {
            $files = null;
        }
        if ($files == []) {
            $files = null;
        }

        $parsedPath = array_diff(explode('/', $path), ['']);
        $parsingPath = App::getApp()->MainView->render(TemplatesPath . '/parsedpath', ['$$path$$' => '/']);
        $allPath = '/';

        foreach ($parsedPath as $piece) {
            $allPath .= $piece;
            $parsingPath .= App::getApp()->MainView->render(TemplatesPath . '/parsedpath', ['$$path$$' => $allPath]);
            $allPath .= '/';
        }

        $filesShablon = '';

        if ($files !== null) {
            foreach ($files as $file) {
                if ($file['is_dir'] == true) {
                    $image = base64_encode(file_get_contents(TemplatesPath . '/' . 'smallDir.png'));
                    $preparedData = [
                        '$$image$$' => $image,
                        '$$path$$' => $file['name'],
                        '$$name$$' => $file['name'],
                        '$$aaa$$' => '/files?path=' . $path . $file['name'],
                        '$$delete$$' => '/directories/delete/' . $file['id'],
                        '$$access$$' => '/filesaccess/' . $file['id']
                    ];
                    $filesShablon .= App::getApp()->MainView->render(TemplatesPath . '/dir', $preparedData);
                } else {
                    $image = base64_encode(file_get_contents(TemplatesPath . '/' . 'file.png'));
                    $preparedData = [
                        '$$image$$' => $image,
                        '$$path$$' => $file['path'],
                        '$$name$$' => $file['name'] . $file['ext'],
                        '$$aaa$$' => '/files/get/' . $file['id'],
                        '$$delete$$' => '/files/remove/' . $file['id'],
                        '$$access$$' => '/filesaccess/' . $file['id'],
                    ];
                    $filesShablon .= App::getApp()->MainView->render(TemplatesPath . '/file', $preparedData);
                }
            }
        }

        $filesShablonCreate = App::getApp()->MainView->render(TemplatesPath . '/formCreateFile', ['$$path$$', $path]);
        $filesShablonCreate .= App::getApp()->MainView->render(TemplatesPath . '/formCreateDir', ['$$path$$', $path]);
        $pages = '';

        for ($i = 1; $i < $countPages + 1; $i++) {
            $pages .= App::getApp()->MainView->render(TemplatesPath . '/paginatepage', ['$$pagelink$$' => '/files?path=' . $path . '&page=' . $i, '$$pagenumber$$' => $i]);
        }

        $pageShablon = App::getApp()->MainView->render(TemplatesPath . '/paginationpages', ['$$pages$$' => $pages]);
        $preparedData = ['$$currentPath$$' => $parsingPath, '$$files$$' => $filesShablon, '$$formcreate$$' => $filesShablonCreate, '$$pagination$$' => $pageShablon];
        $response = new Response(App::getApp()->MainView->render(TemplatesPath . '/files', $preparedData));
        $response->view();
    }

    public function fileAccess(object $request): void
    {
        $idFile = $request->getData()['urlparams'];
        $users = App::getApp()->UserRepository->find();
        $file = App::getApp()->FileRepository->findOneBy($idFile);
        $acc = array_shift($file)['access'];
        $access = !isset($acc) ? '' : $acc;
        $accessArray = explode(',', $access);
        $usersShablon = '';

        foreach ($users as $user) {
            if (in_array($user['id'], $accessArray)) {
                $usersShablon .= "<option value={$user['id']}>{$user['email']} имеет доступ</option>";
            } else {
                $usersShablon .= "<option value={$user['id']}>{$user['email']} не имеет доступ</option>";
            }
        }

        $preparedData = ['$$optionusers$$' => $usersShablon, '$$idfileplace$$' => $idFile];
        $response = new Response(App::getApp()->MainView->render(TemplatesPath . '/accesspage', $preparedData));
        $response->view();
    }

    public function adminPanel(): void
    {
        $itemUser = '';
        $users = App::getApp()->UserRepository->findAll();

        foreach ($users as $user) {
            $preparedData = [
                '$$id$$' => $user['id'],
                '$$login$$' => $user['login'],
                '$$email$$' => $user['email']
            ];
            $itemUser .= App::getApp()->MainView->render(TemplatesPath . '/useritem', $preparedData);
        }

        $preparedData = ['$$bodyadminpanel$$' => file_get_contents(TemplatesPath . '/homeadminpanel'), '$$useritem$$' => $itemUser];
        $response = new Response(App::getApp()->MainView->render(TemplatesPath . '/adminpanel.php', $preparedData));
        $response->view();
    }

    public function adminUpdate(object $request): void
    {
        $id = $request->getData()['urlparams'];
        $user = App::getApp()->UserRepository->findOne($id);
        $user = array_shift($user);
        $preparedData = [
            '$$bodyadminpanel$$' => file_get_contents(TemplatesPath . '/updateuser'),
            '$$loginvalue$$' => $user['login'],
            '$$email$$' => $user['email'],
            '$$id$$' => $user['id']
        ];
        $response = new Response(App::getApp()->MainView->render(TemplatesPath . '/adminpanel.php', $preparedData));
        $response->view();
    }
}