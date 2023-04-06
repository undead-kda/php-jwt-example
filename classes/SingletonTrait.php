<?php

namespace app\classes;

trait SingletonTrait {

  private static $instance = null;

  public static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function __construct() {}

  private function __clone() {}

  public function __wakeup() {
    throw new \Exception("Cannot unserialize singleton");
  }
}