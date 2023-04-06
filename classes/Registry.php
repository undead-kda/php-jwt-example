<?php

namespace app\classes;

class Registry {

  use MultitonTrait;

  private $registry = [];

  public function set(string $key, $value) {
    $this->registry[$key] = $value;
  }

  public function get(string $key, $default = false) {
		if (isset($this->registry[$key])) {
      return $this->registry[$key];
    } else {
      return $default;
    }		
	}

  public function getAll() {
		return $this->registry;
	}

  public function unset(string $key) {
		if (isset($this->registry[$key])) {
      unset($this->registry[$key]);
    }
	}

}