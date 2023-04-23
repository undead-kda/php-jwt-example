<?php

use Pecee\SimpleRouter\SimpleRouter as Router;
use app\classes\Registry as Registry;

require_once __DIR__ . '/../config/settings.php';
require_once APPDIR . 'vendor/autoload.php';
require_once CONF . 'routes.php';

// JWT values
$jwtVariables = Registry::getInstance('JWT');
$jwtVariables->set('key', 'key-9888TyuRReqGwst555sdfsJJKihww45JNKkJB');
// DataBase values
$dbVariables = Registry::getInstance('DB');
$dbVariables->set('host', '127.0.0.1');
$dbVariables->set('dbname', 'restapiexample');
$dbVariables->set('user', 'root');
$dbVariables->set('password', 'Pass');
$dbVariables->set('charset', 'utf8');

try {
  Router::start();
} catch (Throwable $e) {}
