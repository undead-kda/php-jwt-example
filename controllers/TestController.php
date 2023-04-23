<?php
declare(strict_types=1);

namespace app\controllers;

use app\classes\Registry;

use Pecee\Http\Request;
use Pecee\Http\Response;
use Pecee\SimpleRouter\SimpleRouter as Router;
use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\Clock\SystemClock;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;

use app\exceptions\NotAuthorizedHttpException;


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

  public function validate() {
    $input = json_decode(file_get_contents("php://input"), true);
    $jwt = Registry::getInstance('JWT');
    $key = $jwt->get('key');

    $parser = new Parser(new JoseEncoder());
    
    try {
      $token = $parser->parse($input['token']);
    } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
      throw new NotAuthorizedHttpException($e->getMessage());
    }
    
    $validator = new Validator();

    var_dump($token->headers()->get('tokenType'));
    var_dump($token->claims()->get('exp'));
    var_dump($token->claims()->get('exp')->getTimestamp());
    var_dump(SystemClock::fromUTC());

    if (! $validator->validate($token, new StrictValidAt(SystemClock::fromUTC()))) {
      return 'Invalid token!';
    } else {
      return 'Good token!';
    }
    /*
    if (! $validator->validate($token, new SignedWith(new Sha256(), InMemory::plainText($key)))) {
      return 'Invalid token!';
    } else {
      return 'Good token!';
    }
    */
  }

  public function validate2() {

    $input = json_decode(file_get_contents("php://input"), true);
    $jwt = Registry::getInstance('JWT');
    $key = $jwt->get('key');

    $clock = new FrozenClock(new DateTimeImmutable());

    $parser = new Parser(new JoseEncoder());
    
    try {
      $token = $parser->parse($input['token']);
    } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
      throw new NotAuthorizedHttpException($e->getMessage());
    }
    
    $validator = new Validator();

    if (!$validator->validate($token, new LooseValidAt($clock))) {
      return 'Invalid token!';
    } else {
      return 'Good token!';
    }

  }
  
  
}