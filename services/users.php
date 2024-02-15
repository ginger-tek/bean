<?php

namespace Services;

class Users
{
  private DB $db;

  public function __construct(DB $db)
  {
    $this->db = $db;
  }

  function create(string $username, string $password, string $displayName = null): object|bool
  {
    $id = uniqid();
    $this->db->run("insert into users (id, username, password, displayName, created) values (?, ?, ?, ?, ?)", [
      $id,
      $username,
      password_hash($password, PASSWORD_BCRYPT),
      ($displayName ?: $username),
      time()
    ]);
    return $this->get($id);
  }

  function get(string $id): object|bool
  {
    return $this->db->run("select * from users where id = ?", [$id])->fetch();
  }

  function find(string $username): object|bool
  {
    return $this->db->run("select * from users where username = ?", [$username])->fetch();
  }

  function update(string $displayName, string $bio): bool
  {
    return (bool)$this->db->run("update users set displayName = ?, bio = ? where id = ?", [
      $displayName,
      $bio,
      $_SESSION['user']->id
    ])->rowCount();
  }
}