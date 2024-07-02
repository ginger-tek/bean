<?php

namespace Controllers;

use GingerTek\Routy\Routy;
use Services\Users;

class AuthController
{
  static function signup(Routy $app)
  {
    if ($app->method == 'POST') {
      $body = $app->getBody();
      $svc = new Users;
      if ($svc->find($body->username))
        $err = 'Username taken';
      else {
        $user = $svc->create(substr($body->username, 0, 15), substr($body->password, 0, 25), substr($body->displayName, 0, 25));
        session_start();
        $_SESSION['user'] = $user;
        $app->redirect('/');
      }
    }
    $app->render('views/signup.php', ['model' => ['error' => @$err]]);
  }

  static function login(Routy $app)
  {
    if ($app->method == 'POST') {
      $body = $app->getBody();
      $svc = new Users;
      if (!($user = $svc->find($body->username)) || !password_verify($body->password, $user->password))
        $err = 'Incorrect username or password';
      else {
        session_start();
        $_SESSION['user'] = $user;
        $app->redirect(@$_GET['next'] ?? '/');
      }
    }
    $app->render('views/login.php', ['model' => ['error' => @$err]]);
  }

  static function logout(Routy $app)
  {
    session_start();
    session_destroy();
    $app->redirect('/login');
  }
}
