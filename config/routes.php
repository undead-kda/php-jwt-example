<?php

use Pecee\{
  SimpleRouter\SimpleRouter as Router
};

Router::setDefaultNamespace('app\controllers');
Router::get('/', 'HomeController@run');
Router::group(['prefix' => '/auth'], function() {
  Router::get('/info', 'AuthController@show');
  Router::post('/login', 'AuthController@authorization');
});