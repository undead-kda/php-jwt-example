<?php

namespace app\models;

use app\classes\Registry;
use app\models\Request;
use ArgumentCountError;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class Token {

  private string $key;
  const ACCESS = 1;
  const REFRESH = 0;

  public function __construct() {
    $tokenConfig = Registry::getInstance('JWT');
    $this->key = $tokenConfig->get('key');
  }

  public function signin(array $authParams, int $tokenType): string {
    if (!$authParams) return false;

    $tokenHeader = 'none';
    $modifyInterval =  '+2 minutes';

    if ($tokenType === self::ACCESS) {
      $modifyInterval = '+55 minutes';
      $tokenHeader = 'access';
    } elseif ($tokenType === self::REFRESH) {
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

}