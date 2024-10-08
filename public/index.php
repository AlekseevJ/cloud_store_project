<?php 

include_once ('/app/autoloader.php');

use Core\App;

session_start();
App::initConfigs();
App::initRouter();
App::initDb();
App::initApp();
App::initSecurity();
App::collectServices();
App::collectRepositories();
App::collectViews();
App::prepareRequest();
App::getApp()->Router->processRequest(App::getApp()->Request);
