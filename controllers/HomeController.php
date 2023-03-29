<?php

namespace app\controllers;

class HomeController extends AbstractController {
  public function run() {
    return $this->renderTemplate('../views/home.php');
  }
}