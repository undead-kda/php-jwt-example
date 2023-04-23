<?php

namespace app\middlewares;

use Pecee\Http\Middleware\IMiddleware;
use app\exceptions\NotAuthorizedHttpException;
use DateTimeImmutable;
use Pecee\Http\Request;
use app\classes\Registry;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;

class RefreshTokenValidate implements IMiddleware {

  public function handle(Request $request): void {
    $headers = getallheaders();
    $tokenString = substr($headers['Authorization'] ?? '', 7);
    
    $jwt = Registry::getInstance('JWT');
    $key = $jwt->get('key');

    $clock = new FrozenClock(new DateTimeImmutable());

    $parser = new Parser(new JoseEncoder());

    try {
      $token = $parser->parse($tokenString);
    } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
      throw new NotAuthorizedHttpException($e->getMessage());
    }

    $userRole = $token->claims()->get('role');
    $tokenType = $token->headers()->get('tokenType');

    if ($tokenType !== 'refresh') throw new NotAuthorizedHttpException('Токен не поддерживается');

    $validator = new Validator();

    if (!$validator->validate($token, new LooseValidAt($clock))) {
      throw new NotAuthorizedHttpException('Токен доступа не валиден или просрочен');
    }
    
    $request->uid =  $token->claims()->get('uid');
    $request->tokenHash = hash('sha256', $tokenString);

  }
}