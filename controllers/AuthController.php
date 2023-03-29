<?php

namespace app\controllers;

use app\models\DB;

class AuthController extends AbstractController {

  private $sql = 'SELECT * FROM Users';

  public function show() {
    $sql = 'SELECT * FROM Users';
    $db = DB::getInstance();
    $result = $db->getData($sql);

    //return json_encode($db->query($sql));
    return $result;
  }
}