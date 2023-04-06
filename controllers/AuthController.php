<?php

namespace app\controllers;

use app\classes\DB;
use app\classes\Registry;
use app\models\Request;
use ArgumentCountError;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

enum TokenType {
  case Access;
  case Refresh;
}

class AuthController extends AbstractController {

  private $db;
  private $key;

  public function __construct() {
    parent::__construct();
    header('Content-Type: application/json; charset=utf-8');
    $this->db = DB::getInstance();
    $tokenConfig = Registry::getInstance('JWT');
    $this->key = $tokenConfig->get('key');
  }

  public function show() {
    $sql = 'SELECT * FROM Users';
    $result = $this->db->getData($sql);

    return $result;
  }

  public function authorization() {
    $authResult = $this->login();
    
    if ($authResult['auth']) {
      return $this->response->json([
        'accessToken' => $this->signin($authResult, TokenType::Access),
        'refreshToken' => $this->signin($authResult, TokenType::Refresh),
        'result' => 'ok'
      ]);
    } else {
      return $this->response->json([
        'result' => 'error'
      ]);
    }
  }

  public function signin(array $authParams, TokenType $tokenType): string {
    if (!$authParams) return false;

    $tokenHeader = 'none';
    $modifyInterval =  '+2 minutes';

    if ($tokenType === TokenType::Access) {
      $modifyInterval = '+5 minutes';
      $tokenHeader = 'access';
    } elseif ($tokenType === TokenType::Refresh) {
      $modifyInterval = '+1 week';
      $tokenHeader = 'refresh';
    }

    $id = $authParams['userId'];
    $role = $authParams['role'];

    $config = Configuration::forSymmetricSigner(
      new Sha256(),
      InMemory::plainText($this->key)
    );
    $now   = new DateTimeImmutable();
   
    $token = $config->Builder()
        ->issuedBy('http://restapiexample.local')
        ->permittedFor('http://restapiexample.local')
        ->identifiedBy(md5("user_id_{$id}"), true)
        ->issuedAt($now)
        ->expiresAt($now->modify($modifyInterval))
        ->withClaim('uid', $id)
        ->withClaim('role', $role)
        ->withHeader('tokenType', $tokenHeader)
        ->getToken($config->signer(), $config->signingKey());

    return $token->toString();
  }

  private function login() {
    $authResult = false;

    $input = json_decode(file_get_contents("php://input"), true);
    if ($input) {
      if (array_key_exists('email', $input) && array_key_exists('password', $input)) {
        $result = $this->db->findUser(trim($input['email']));
        if ($result) {
          $user = $result[0];
          if (md5(trim($input['password'])) === $user['password']) {
            $authResult = true;
          }
        }
      }
    }

    if ($authResult) {
      return [
        'auth' => true,
        'userId' => $user['UserID'],
        'role' => $user['role']
      ];
    }
    
    return ['auth' => false];
  }

  private function validate() {

  }
}