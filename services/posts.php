<?php

namespace Services;

class Posts
{
  private DB $db;

  public function __construct(DB $db)
  {
    $this->db = $db;
  }

  function create($body, $parent = null)
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

  function get($id)
  {
    return $this->db->run("select * from v_posts where id = ?", [$id])->fetch();
  }

  function all()
  {
    return $this->db->run("select * from v_posts order by created desc")->fetchAll();
  }

  function allByParent($id)
  {
    return $this->db->run("select * from v_posts where parent = ? order by created desc", [$id])->fetchAll();
  }

  function allByAuthor($userId)
  {
    return $this->db->run("select * from v_posts where author = ? order by created desc", [$userId])->fetchAll();
  }
}