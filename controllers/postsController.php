<?php

namespace Controllers;

use GingerTek\Routy\Routy;
use Services\Posts;

class PostsController
{
  static function create(Routy $app)
  {
    $post = (new Posts)->create($app->getBody());
    $app->redirect("/posts/$post->id");
  }

  static function list(Routy $app)
  {
    $svc = new Posts;
    $posts = $svc->list();
    $app->render('views/feed.php', ['model' => ['posts' => $posts, 'postsSvc' => $svc]]);
  }

  static function view(Routy $app)
  {
    $svc = new Posts;
    $post = $svc->get($app->params->id);
    if (!$post)
      $app->render('views/404.php');
    $parent = $post->parent ? $svc->get($post->parent) : null;
    $replies = $svc->listByParent($post->id);
    $app->render('views/post.php', ['model' => ['post' => $post, 'parent' => $parent, 'replies' => $replies]]);
  }
}
