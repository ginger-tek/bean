<?php

namespace Services;

class Posts
{
  private DB $db;

  public function __construct(DB $db = new DB)
  {
    $this->db = $db;
  }

  function create(object $data): object|bool
  {
    $id = uniqid();
    $this->db->run("insert into posts (id, body, parent, author, created) values (?, ?, ?, ?, ?)", [
      $id,
      $data->body,
      $data->parent ?? null,
      $_SESSION['user']->id,
      time()
    ]);
    return $this->get($id);
  }

  function get(string $id): object|bool
  {
    return $this->db->run("select * from v_posts where id = ?", [$id])->fetch();
  }

  function list(): array
  {
    return $this->db->run("select * from v_posts order by created desc")->fetchAll();
  }

  function listByParent(string $id): array
  {
    return $this->db->run("select * from v_posts where parent = ? order by created desc", [$id])->fetchAll();
  }

  function listByAuthor(string $userId): array
  {
    return $this->db->run("select * from v_posts where author = ? order by created desc", [$userId])->fetchAll();
  }
}