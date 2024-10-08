<?php

namespace App\Controllers;

use Core\App;
use Core\Response;

class FileController extends BaseController
{
    public function listFiles(): void
    {
        $response = new Response(App::getApp()->FileRepository->find());
        $response->sendResponse();
    }

    public function getFile(object $request): void
    {
        $id = $request->urlparams;
        $fileData = App::getApp()->FileRepository->findOneBy($id);
        $fileData = array_shift($fileData);
        $id = $fileData['id'];
        $name = $fileData['name'];
        $file = StoragePath . '/' . $id;
        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $name . $fileData['ext'],
            'Content-Transfer-Encoding' => 'binary'
        ];
        $response = new Response($file);
        $response->addHeader($headers);
        $response->download();
    }

    public function createFile(object $request): void
    {
        if (isset($_SESSION['id'])) {
            $file = $request->getData()['files'];

            if (isset($request->getData()['params']['path'])) {
                $path = $request->getData()['params']['path'];
            } else {
                $path = '/';
            }
            if (!isset($request->getData()['params']['name']) || !array_key_exists('file', $file)) {
                $response = new Response('400, bad request');
                $response->sendResponse();
            }

            $name = $request->getData()['params']['name'];

            try {
                $ext = strchr($file['file']['name'], '.') ?: null;
                $idDb = App::getApp()->FileRepository->createFile($path, $name, (int) $_SESSION['id'], $ext);
                App::getApp()->FileService->createFile($file, $idDb);
                $header = ['Location' => '/files'];
                $response = new Response('200, ok');
                $response->addHeader($header);
                $response->sendResponse();
            } catch (\Exception $e) {
                $response = new Response('500, not ok');
                $response->addHeader($header);
                $response->sendResponse();
            }
        }
    }

    public function renameFile(object $request): void
    {
        $id = $request->getData()['params']['id'];
        $newName = $request->getData()['params']['name'];
        App::getApp()->FileRepository->renameFile($id, $newName);
    }



    public function deleteFile(object $request): void
    {
        $id = $request->getData()['urlparams'];
        App::getApp()->FileRepository->delete($id);
        App::getApp()->FileService->deleteFile($id);
        $response = new Response('200, ok');
        $response->addHeader(['Location' => '/']);
        $response->sendResponse();
    }

    public function deleteFileId(int $id): void
    {
        App::getApp()->FileRepository->delete($id);
        App::getApp()->FileService->deleteFile($id);
    }

    public function createDirectory(object $request): void
    {
        $path = $request->getData()['params']['path'];
        $name = $request->getData()['params']['name'];
        $access = $_SESSION['id'];
        App::getApp()->FileRepository->createDirectory($name, $path, $access);
        $response = new Response('ok');
        $response->addHeader(['Location' => '/files']);
        $response->sendResponse();
    }

    public function renameDirectory(object $request): void
    {
        $id = $request->getData()['params']['id'];
        $newName = $request->getData()['params']['name'];
        App::getApp()->FileRepository->renameDirectory($id, $newName);
    }

    public function listInDirs(object $request): array
    {
        $id = $request->getData()['urlparams'];
        $listData = App::getApp()->FileRepository->find(App::getApp()->FileService->getNewDirPath(App::getApp()->FileRepository->listDir($id)));
        return $listData;
    }

    public function deleteDirectory(object $request): void
    {
        $id = $request->getData()['urlparams'];
        $deletedPath = App::getApp()->FileRepository->deleteDirectory($id);
        $filesAndDir = App::getApp()->FileRepository->findAll($deletedPath);

        foreach ($filesAndDir as $element) {
            if ($element['is_dir'] == false || $element['is_dir'] == null) {
                $this->deleteFileId($element['id']);
            } elseif ($element['is_dir'] == true) {
                $this->deleteDirectoryId($element['id']);
            }
        }

        $response = new Response('200, ok');
        $response->addHeader(['Location' => '/files']);
        $response->sendResponse();
    }

    public function deleteDirectoryId(int $id): void
    {
        $deletedPath = App::getApp()->FileRepository->deleteDirectory($id);
        $filesAndDir = App::getApp()->FileRepository->findAll($deletedPath);

        foreach ($filesAndDir as $element) {
            if ($element['is_dir'] == false || $element['is_dir'] == null) {
                $this->deleteFileId($element['id']);
            } elseif ($element['is_dir'] == true) {
                $this->deleteDirectoryId($element['id']);
            }
        }
    }

    public function shareFileUsers(object $request): array
    {
        $idFile = $request->getData()['urlparams'];
        return App::getApp()->FileRepository->shareFileUsers($idFile);
    }

    public function setShareFile(object $request): void
    {
        $idFile = $request->getData()['twoparams'][0];
        $idUser = $request->getData()['twoparams'][1];
        App::getApp()->FileRepository->setShareFile($idFile, $idUser);
    }

    public function removeShareFile(object $request): void
    {
        $idFile = $request->getData()['twoparams'][0];
        $idUser = $request->getData()['twoparams'][1];
        App::getApp()->FileRepository->unsetShareFile($idFile, $idUser);
    }

    public function processProcessor(object $request): void
    {
        $idFile = (int) $request->getData()['params']['idFile'];
        $idUser = (int) $request->getData()['params']['user-ids'];
        $action = $request->getData()['params']['permissions'];

        if ($action == 'true') {
            App::getApp()->FileRepository->setShareFile($idFile, $idUser);
        } else {
            App::getApp()->FileRepository->unsetShareFile($idFile, $idUser);
        }

        $response = new Response('200, ok');
        $response->addHeader(['Location' => '/files']);
        $response->sendResponse();
    }
}