<?php

namespace Services;

class Posts
{
  private DB $db;

  public function __construct(DB $db)
  {
    $this->db = $db;
  }

  function create(string $body, string $parent = null): object|bool
  {
    $id = uniqid();
    $this->db->run("insert into posts (id, body, parent, author, created) values (?, ?, ?, ?, ?)", [
      $id,
      $body,
      $parent,
      $_SESSION['user']->id,
      time()
    ]);
    return $this->get($id);
  }

  function get(string $id): object|bool
  {
    return $this->db->run("select * from v_posts where id = ?", [$id])->fetch();
  }

  function all(): array
  {
    return $this->db->run("select * from v_posts order by created desc")->fetchAll();
  }

  function allByParent(string $id): array
  {
    return $this->db->run("select * from v_posts where parent = ? order by created desc", [$id])->fetchAll();
  }

  function allByAuthor(string $userId): array
  {
    return $this->db->run("select * from v_posts where author = ? order by created desc", [$userId])->fetchAll();
  }

  function delete(string $id): bool
  {
    return (bool)$this->db->run("delete from posts where id = ? limit 1", [$id])->rowCount();
  }
}