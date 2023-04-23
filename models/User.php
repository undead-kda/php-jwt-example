<?php

namespace app\models;

use app\classes\DB;

class User {

  private int $id;
  private string $name;
  private string $email;
  private string $password;
  private string $role;
  private string $refresh;
  private $db;

  public function __construct(string $email = null, int $id = null) {
    $this->db = DB::getInstance();
    
    if ($email) {
      $result = $this->getUserByEmail($email);
    } elseif ($id) {
      $result = $this->getUserByID($id);
    }

    if ($result) {
      $this->setAllProperties($result);
    }
  }

  private function getUserByEmail(string $email): array {
    $sql = "SELECT * FROM Users WHERE email = ? LIMIT 1";
    $result = $this->db->get($sql, array($email));
    
    if ($result) {
      return $result[0];
    } else {
      return false;
    }
  }

  private function getUserByID(int $id): array {
    $sql = "SELECT * FROM Users WHERE UserID = ? LIMIT 1";
    $result = $this->db->get($sql, array($id));

    if ($result) {
      return $result[0];
    } else {
      return false;
    }
  }

  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    }
  }

  private function setAllProperties(array $values): void {
    $this->id = (int) $values['UserID'];
    $this->name = $values['name'];
    $this->email = $values['email'];
    $this->password = $values['password'];
    $this->role = $values['role'];
    $this->refresh = $values['refresh'];
  }

  public function setRefresh(string $token): void {
    $tokenHash = hash('sha256', $token);

    $sql = "UPDATE Users SET refresh = ? WHERE UserID = ?";
    $this->db->update($sql, array($tokenHash, $this->id));
  }

}