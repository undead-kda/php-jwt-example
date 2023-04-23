<?php

namespace app\models;

use app\classes\DB;
use app\classes\Registry;
use app\models\User;

class Auth {

  private $user;

  public function __construct() {} 

  public function login(array $input): array {  
    $authResult = false;
    if (!($input)) return ['auth' => false];
    if (!(array_key_exists('email', $input) && array_key_exists('password', $input))) return ['auth' => false];
    
    $this->user = new User(email: $input['email']);

    if ($this->user->password && $input['password']) {
      if (md5(trim($input['password'])) === $this->user->password) $authResult = true; 
    }

    if ($authResult) {
      return [
        'auth' => true,
        'userId' => $this->user->id,
        'role' => $this->user->role
      ];
    }

    return ['auth' => false];
  }

  public function getUserInfo(int $id): array {
    $this->user = new User(id: $id);

    if ($this->user) {
      return [
        'auth' => true,
        'userId' => $this->user->id,
        'role' => $this->user->role,
        'refresh' => $this->user->refresh
      ];
    } else {
      return ['auth' => false];
    }
  }

  public function setTokenHash(string $token): void {
    $this->user->setRefresh($token);
  }

}