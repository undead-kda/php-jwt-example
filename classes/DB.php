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
  
  public function get(string $sql, array $values): array {
    if (!($sql || $values)) return [];

    $query = $this->pdo->prepare($sql);
    $query->execute($values);
    $result = $query->fetchAll();

    return $result;
  }

  public function update(string $sql, array $values): void {
    if (!($sql || $values)) exit();

    $query = $this->pdo->prepare($sql);
    $query->execute($values);
  }

}