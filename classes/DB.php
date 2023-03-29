<?php

namespace app\classes;

class DB {

  private $host;
  private $dbname;
  private $charset;
  private $user;
  private $password;
  private $opt = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
  ];
  private $pdo;
  protected static $instance = null;

  static public function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }

    return self::$instance;
  }
  
  private function __construct() {
    $dbsettings = require __DIR__ . '/../config/dbconfig.php';
    $this->host = $dbsettings['host'];
    $this->dbname = $dbsettings['dbname'];
    $this->charset = $dbsettings['charset'];
    $this->user = $dbsettings['user'];
    $this->password = $dbsettings['password'];

    $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";

    try {
      $this->pdo = new \PDO($dsn, $this->user, $this->password, $this->opt);
    } catch (PDOException $e) {
      die('Connection error: ' . $e->getMessage());
    }
  }

  private function __clone() {}

  public function __wakeup() {
    throw new Exception("Cannot unserialize singleton");
  }

  public function getData(string $data) {
    
    $query = $this->pdo->query($data);
    $result = $query->fetchAll();

    return json_encode($result, JSON_UNESCAPED_UNICODE);
  }
  
}