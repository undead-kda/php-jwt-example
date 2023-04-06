<?php

namespace app\classes;

use app\classes\Registry;
class DB {

  use SingletonTrait;

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

  private function __construct() {
    $dbsettings = Registry::getInstance('DB');
    $this->host = $dbsettings->get('host');
    $this->dbname = $dbsettings->get('dbname');
    $this->charset = $dbsettings->get('charset');
    $this->user = $dbsettings->get('user');
    $this->password = $dbsettings->get('password');

    $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";

    try {
      $this->pdo = new \PDO($dsn, $this->user, $this->password, $this->opt);
    } catch (\PDOException $e) {
      die('Connection error: ' . $e->getMessage());
    }
  }
  
 
  public function getData(string $data) {
    $query = $this->pdo->query($data);
    $result = $query->fetchAll();

    return json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  public function findUser(string $user): array {
    $sql = "SELECT * FROM Users WHERE email = ? LIMIT 1";
    $query = $this->pdo->prepare($sql);
    $query->execute(array($user));
    $result = $query->fetchAll();

    return $result;
  }
  
}