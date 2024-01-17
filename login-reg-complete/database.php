<?php
require_once 'config.php';
class Database {
  private const DSN = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
  private $conn;

  // method to connect to the database
  public function __construct() {
    try {
      $this->conn = new PDO(self::DSN, DB_USER, DB_PASS);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
    }
  }

  // method to register a user
  public function register($name, $email, $password) {
    $sql = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'name' => $name,
      'email' => $email,
      'password' => $password
    ]);
  }

  // method to login a user
  public function login($email, $password) {
    $sql = 'SELECT * FROM users WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      if (password_verify($password, $user['password'])) {
        return $user;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  // method to check if email already exists
  public function getUserByEmail($email) {
    $sql = 'SELECT * FROM users WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
  }

  // method to set token
  public function setToken($email, $token) {
    $sql = 'UPDATE users SET token = :token WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'token' => $token,
      'email' => $email
    ]);

    return true;
  }

  // method to get user by token
  public function getUserByToken($token) {
    $sql = 'SELECT * FROM users WHERE token = :token';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
  }


  // method to update password
  public function updatePassword($email, $password) {
    $sql = 'UPDATE users SET password = :password, token = :token WHERE email = :email';
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      'password' => $password,
      'token' => null,
      'email' => $email
    ]);

    return true;
  }
}