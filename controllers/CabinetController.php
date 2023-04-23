<?php

namespace app\controllers;

class CabinetController extends AbstractController {

  public function index() {

    return $this->response->json([
      'result' => 'Cabinet'
    ]);
  }

}