<?php

namespace Controllers;

use GingerTek\Routy\Routy;
use Services\Users;
use Services\Posts;

class AccountController
{
  static function view(Routy $app)
  {
    $user = (new Users)->get($_SESSION['user']->id);
    $app->render('views/user.php', ['model' => ['user' => $user, 'canEdit' => true]]);
  }

  static function viewByUsername(Routy $app)
  {
    $user = (new Users)->find($app->params->username);
    $svc = new Posts;
    $posts = $svc->listByAuthor($user->id);
    $app->render('views/user.php', ['model' => ['user' => $user, 'posts' => $posts, 'postsSvc' => $svc]]);
  }

  static function save(Routy $app)
  {
    $body = $app->getBody();
    (new Users)->update($body->displayName, $body->bio);
    $app->redirect('/account');
  }
}
