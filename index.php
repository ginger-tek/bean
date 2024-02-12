<?php

session_start(['read_and_close' => true]);

require 'vendor/autoload.php';

spl_autoload_register(fn($c) => include "$c.php");

use GingerTek\Routy\Routy;
use Services\DB;
use Services\Users;
use Services\Posts;
use Services\Utils;

$app = new Routy();

$app->get('/', function (Routy $app) {
  $postsSvc = new Posts(new DB());
  $posts = $postsSvc->all();
  Utils::render('views/feed.php', ['posts' => $posts, 'postsSvc' => $postsSvc]);
});

$app->route('GET|POST', '/signup', function (Routy $app) {
  if ($app->method == 'POST') {
    $body = $app->getBody();
    $svc = new Users(new DB());
    if ($svc->find($body->username))
      $err = 'Username taken';
    else {
      $user = $svc->create(substr($body->username, 0, 15), substr($body->password, 0, 25), substr($body->displayName, 0, 25));
      session_start();
      $_SESSION['user'] = $user;
      header('location: /');
      exit;
    }
  }
  Utils::render('views/signup.php', ['err' => @$err]);
});

$app->route('GET|POST', '/login', function (Routy $app) {
  if ($app->method == 'POST') {
    $body = $app->getBody();
    $svc = new Users(new DB());
    if (!($user = $svc->find($body->username)))
      $err = 'User not found';
    else if (!password_verify($body->password, $user->password))
      $err = 'Incorrect password';
    else {
      session_start();
      $_SESSION['user'] = $user;
      header('location: ' . (@$_GET['next'] ?? '/'));
      exit;
    }
  }
  Utils::render('views/login.php', ['err' => @$err]);
});

$app->get('/logout', '\Services\Utils::auth', function (Routy $app) {
  session_start();
  session_destroy();
  $app->sendRedirect('/');
});

$app->group('/posts', function (Routy $app) {
  $app->get('/:id', function (Routy $app) {
    $postsSvc = new Posts(new DB());
    $post = $postsSvc->get($app->params->id);
    $parent = $postsSvc->get($post->parent);
    if (!$post) {
      $app->setStatus(404);
      return Utils::render('views/404.php');
    }
    $replies = $postsSvc->allByParent($post->id);
    Utils::render('views/post.php', [
      'post' => $post,
      'parent' => $parent,
      'replies' => $replies
    ]);
  });

  $app->post('/', '\Services\Utils::auth', function (Routy $app) {
    $body = $app->getBody();
    $svc = new Posts(new DB());
    $post = $svc->create(substr(htmlspecialchars($body->body), 0, 140), @$body->parent);
    $app->sendRedirect("/posts/$post->id");
  });
});

$app->get('/@:username', function (Routy $app) {
  $db = new DB();
  $user = (new Users($db))->find($app->params->username);
  if (!$user) {
    $app->setStatus(404);
    return Utils::render('views/404.php');
  }
  $postsSvc = new Posts($db);
  $posts = $postsSvc->allByAuthor($user->id);
  Utils::render('views/user.php', [
    'user' => $user,
    'posts' => $posts,
    'postsSvc' => $postsSvc
  ]);
});

$app->post('/profile', '\Services\Utils::auth', function (Routy $app) {
  (new Users(new DB()))->update($_POST['displayName'], $_POST['bio']);
  session_start();
  $_SESSION['user']->displayName = substr($_POST['displayName'], 0, 25);
  $_SESSION['user']->bio = substr($_POST['bio'], 0, 140);
  $app->sendRedirect($_SERVER['REQUEST_URI']);
});

$app->notFound(function (Routy $app) {
  $app->setStatus(404);
  Utils::render('views/404.php');
});