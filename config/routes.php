<?php

use Pecee\{
  SimpleRouter\SimpleRouter as Router
};

Router::setDefaultNamespace('app\controllers');
Router::get('/', 'HomeController@run');
Router::get('/info', 'AuthController@show');