<?php

namespace app\controllers;

use app\models\User;
use app\models\Auth;
use app\models\Token;
use app\classes\Registry;
use app\models\Request;

class AuthController extends AbstractController {

  const ACCESS = 1;
  const REFRESH = 0;
  private object $auth;
  private object $user;

  public function __construct() {
    parent::__construct();
    header('Content-Type: application/json; charset=utf-8');
    $this->auth = new Auth();
  }

  public function authorization() {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input) {
      return $this->response->json([
        'status' => 'error'
      ]);
    }

    $authResult = $this->auth->login($input);
    
    if (!$authResult['auth']) {
      return $this->response->json([
        'status' => 'error'
      ]);
    }
    
    if (isset($authResult['auth'])) {
      return $this->getTokens($authResult);
    } else {
      return $this->response->json([
        'status' => 'error'
      ]); 
    }
  }

  private function getTokens(array $authValues): array {
    $token = new Token();
    $accessToken = $token->signin($authValues, self::ACCESS);
    $refreshToken = $token->signin($authValues, self::REFRESH);

    $this->auth->setTokenHash($refreshToken);
    
    return $this->response->json([
      'accessToken' => $accessToken,
      'refreshToken' => $refreshToken,
      'status' => 'ok'
    ]);
  }

  private function verifyRefreshToken(string $tokenHash): bool {
    if (!$tokenHash) return false;
    if (!$this->user->refresh) return false;
    
    $result = ($tokenHash === $this->user->refresh) ? true : false;
    
    return $result;
  }

  public function renewTokens() {
    $uid = $this->request->uid;
    $tokenHash = $this->request->tokenHash;

    $authValues = $this->auth->getUserInfo($uid);

    if (!$authValues['auth']) {
      return $this->response->json([
        'status' => 'error'
      ]);
    }
  
    if ($tokenHash === $authValues['refresh']) {
      return $this->getTokens($authValues);
    } else {
      return $this->response->json([
        'status' => 'error'
      ]);
    }
  }

}