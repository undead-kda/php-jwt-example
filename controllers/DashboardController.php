<?php

namespace app\controllers;

class DashboardController extends AbstractController {

  public function index() {

    return $this->response->json([
      'result' => 'Dashboard'
    ]);
  }

}