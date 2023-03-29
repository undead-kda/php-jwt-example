<?php

namespace app\controllers;

use app\classes\DB;

class AuthController extends AbstractController {

  public function __construct() {
    header('Content-Type: application/json; charset=utf-8');
  }

  public function show() {
    $sql = 'SELECT * FROM Users';
    $db = DB::getInstance();
    $result = $db->getData($sql);

    return $result;
  }
}