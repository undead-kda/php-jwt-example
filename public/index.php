<?php

use Pecee\SimpleRouter\SimpleRouter as Router;

require_once __DIR__ . '/../config/settings.php';
require_once APPDIR . 'vendor/autoload.php';
require_once CONF . 'routes.php';

try {
  Router::start();
} catch (Throwable $e) {

}
