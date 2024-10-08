<?php

namespace App\Services;

use Core\Interfaces\ServiceInterface;

class FileService implements ServiceInterface
{
    public function getAllFiles(): array
    {
        $answer = [];
        $files = scandir(StoragePath);
        $files = array_diff($files, ['.', '..']);

        foreach ($files as $file) {
            if (is_file(StoragePath . '/' . $file)) {
                $answer[] = ['file' => $file];
            } elseif (is_dir(StoragePath . '/' . $file)) {
                $answer[] = ['dir' => $file];
            }
        }

        return $answer;

    }

    public function getFile(array $file): array
    {
        $is_dir = is_dir(StoragePath . '/' . $file);

        if (!$is_dir) {
            return ['is_dir' => 0, 'filesize' => filesize(StoragePath . '/' . $file)];
        }

        $filesize = $this->getFilesSize(StoragePath . '/' . $file);
        return ['is_dir' => $is_dir, 'filesize' => $filesize];
    }

    public function getFilesSize(string $path): int
    {
        $fileSize = 0;
        $dir = scandir($path);

        foreach ($dir as $file) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($path . '/' . $file)) {
                    $fileSize += $this->getFilesSize($path . '/' . $file);
                } else {
                    $fileSize += filesize($path . '/' . $file);
                }
            }
        }

        return $fileSize;
    }

    public function addFile(array $file): void
    {
        $fileTmpPath = file_get_contents($file['file']['tmp_name']);
        file_put_contents(StoragePath . '/' . $file['file']['name'], $fileTmpPath);
    }

    public function renameFile(string $oldFileName, string $newFileName): void
    {
        rename(StoragePath . '/' . $oldFileName, StoragePath . '/' . $newFileName);
    }

    public function addDir(string $dirName): void
    {
        mkdir(StoragePath . '/' . $dirName);
    }

    public function renameDir(string $oldFileName, string $newFileName): void
    {
        rename(StoragePath . '/' . $oldFileName, StoragePath . '/' . $newFileName);
    }

    public function createFile(array $file, int $id): bool
    {
        $fileTmpPath = file_get_contents($file['file']['tmp_name']);
        return file_put_contents(StoragePath . '/' . $id, $fileTmpPath);
    }

    public function deleteFile(int $id): void
    {
        unlink(StoragePath . '/' . $id);
    }

    public function getNewDirPath(array $queryDir): string
    {
        if (!isset($queryDir['path'])) {
            $queryDir = array_shift($queryDir);
        }

        return $queryDir['path'] . $queryDir['name'] . '/';
    }
}