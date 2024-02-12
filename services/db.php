<?php

namespace Services;

class DB
{
  public \PDO $conn;

  public function __construct()
  {
    $this->conn = new \PDO('sqlite:data/bean.db', null, null, [
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
    ]);
    $this->conn->exec(file_get_contents('data/schema.sql'));
  }

  function run($sql, $params = [])
  {
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}