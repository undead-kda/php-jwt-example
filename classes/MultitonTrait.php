<?php

namespace app\classes;

trait MultitonTrait {

  private static $list;

  public static function getInstance(string $instance = 'default') {
    if (empty(self::$list[$instance])) self::$list[$instance] = new static();

    return self::$list[$instance];
  }

  private function __construct() {}

  private function __clone() {}

  public function __wakeup() {}

}