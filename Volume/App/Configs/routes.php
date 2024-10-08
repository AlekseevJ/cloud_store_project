<?php 

use App\Controllers\FileController;
use App\Controllers\ViewController;
use App\Controllers\UserController;
use App\Controllers\Admin\UserController as AdminController;

$urlList = [
    '/user/list' => ['GET' => [UserController::class, 'list'], 'Access' => ['auth'=> false, 'role' => 2]],   //'Auth' => 'true', 'role' => 2
    '/user/update' => ['POST' => [UserController::class, 'update'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/user/login' => ['POST' => [UserController::class, 'login'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/user/logout' => ['GET' => [UserController::class, 'logout'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/user/resetpassword' => ['GET' => [UserController::class, 'reset_password'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/user/get' => ['GET' => [UserController::class, 'get'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/user/register' => ['POST' => [UserController::class, 'register'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/admin/users/list' => ['GET' => [AdminController::class, 'list'], 'Access' => ['auth'=> true, 'role' => 1]],
    '/admin/users/get' => ['GET' => [AdminController::class, 'getUser'], 'Access' => ['auth'=> true, 'role' => 1]],
    '/admin/users/delete' => ['POST' => [AdminController::class, 'deleteUser'], 'Access' => ['auth'=> true, 'role' => 1]],
    '/admin/users/update' => ['POST' => [AdminController::class, 'updateUser'], 'Access' => ['auth'=> true, 'role' => 1]],// param=>val
    '/admin/users/create' => ['POST' => [AdminController::class, 'createUser'], 'Access' => ['auth'=> true, 'role' => 1]],
    '/files/list' => ['GET' => [FileController::class, 'listFiles'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/get' => ['GET' => [FileController::class, 'getFile'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/add' => ['POST' => [FileController::class, 'createFile'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/rename' => ['PUT' => [FileController::class, 'renameFile'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/remove' => ['GET' => [FileController::class, 'deleteFile'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/processor' => ['POST' => [FileController::class,'processProcessor'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/directories/add' => ['POST' => [FileController::class, 'createDirectory'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/directories/rename' => ['PUT' => [FileController::class, 'renameDirectory'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/directories/get' => ['GET' => [FileController::class, 'listInDirs'], 'Access' => ['auth'=> false, 'role' => 2]], //{id}
    '/directories/delete' => ['GET' => [FileController::class, 'deleteDirectory'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/share' => ['GET' => [FileController::class, 'shareFileUsers'], 'Access' => ['auth'=> false, 'role' => 2]],
    '/files/share/add' => ['PUT' => [FileController::class, 'setShareFile'], 'Access' => ['auth'=> false, 'role' => 2]], //two params
    '/files/share/remove' => ['DELETE' => [FileController::class, 'removeShareFile'], 'Access' => ['auth'=> false, 'role' => 2]], //two params
];
$templates = [
    '/' => [ViewController::class, 'homePage', 'Access' => ['auth'=> false, 'role' => 2]],
    '/files' => [ViewController::class, 'filePage', 'Access' => ['auth'=> true, 'role' => 2]],
    '/filesaccess' => [ViewController::class, 'fileAccess', 'Access' => ['auth'=> true, 'role' => 2]],
    '/createfile' => [ViewController::class, 'createFile', 'Access' => ['auth'=> true, 'role' => 2]],
    '/adminpanel' => [ViewController::class, 'adminPanel', 'Access' => ['auth'=> true, 'role' => 1]],
    '/adminupdate' => [ViewController::class, 'adminUpdate', 'Access' => ['auth'=> true, 'role' => 1]],
];