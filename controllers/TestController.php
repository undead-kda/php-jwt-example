<?php
declare(strict_types=1);

namespace app\controllers;

use Pecee\Http\Request;
use Pecee\Http\Response;
use Pecee\SimpleRouter\SimpleRouter as Router;
use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class TestController {
  public function index() {
    $input = json_decode(file_get_contents("php://input"), true);

    return $input['user'];
  }

  public function signin() {
    $key = InMemory::base64Encoded(
      'hiG8DlOKvtih6AxlZn5XKImZ06yu8I3mkOzaJrEuW8yAv8Jnkw330uMt8AEqQ5LB'
    );
  
    $token = (new JwtFacade())->issue(
        new Sha256(),
        $key,
        static fn (
            Builder $builder,
            DateTimeImmutable $issuedAt
        ): Builder => $builder
            ->issuedBy('https://api.my-awesome-app.io')
            ->permittedFor('https://client-app.io')
            ->expiresAt($issuedAt->modify('+10 minutes'))
    );
  
    //var_dump($token->claims()->all());
    echo $token->toString();
  }
}