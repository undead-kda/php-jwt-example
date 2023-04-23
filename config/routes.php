<?php
use app\exceptions\{
  NotAuthorizedHttpException
};

use app\middlewares\{
  AdminAuthenticate,
  UserAuthenticate,
  RefreshTokenValidate
};

use Pecee\{
  Http\Request,
  SimpleRouter\SimpleRouter as Router
};

const PROD = false;

Router::setDefaultNamespace('app\controllers');

Router::get('/', 'HomeController@run');

Router::group(['prefix' => '/auth'], function() {
  Router::post('/login', 'AuthController@authorization');
  Router::group(['middleware' => [RefreshTokenValidate::class]], function() {
    Router::get('/refresh', 'AuthController@renewTokens');
  });
});

Router::group(['prefix' => '/admin', 'middleware' => [AdminAuthenticate::class]], function() {
  Router::get('/dashboard', 'DashboardController@index');
});

Router::group(['prefix' => '/user', 'middleware' => [UserAuthenticate::class]], function() {
  Router::get('/cabinet', 'CabinetController@index');
});

Router::error(function(Request $request, Exception $exception) {
  $response = Router::response();
  switch (get_class($exception)) {
    case NotAuthorizedHttpException::class: {
        $response->httpCode(401);
        break;
    }
    case Exception::class: {
        $response->httpCode(500);
        break;
    }
  }
  if (PROD) {
      return $response->json([]);
  } else {
      return $response->json([
          'status' => 'error',
          'message' => $exception->getMessage()
      ]);
  }
});