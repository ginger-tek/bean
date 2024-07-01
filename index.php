<?php

if (preg_match('#\.(?:css|js|png|jpeg|jpg|gif|webp)$#', $_REQUEST['REQUEST_URI'])) return false;

session_start(['read_and_close' => true]);

require 'vendor/autoload.php';
spl_autoload_register(fn($c) => include "$c.php");

use GingerTek\Routy\Routy;
use Services\DB;
use Services\Users;
use Services\Posts;
use Services\Utils;

$app = new Routy();

try {
  $app->route('GET|POST', '/signup', AuthController::signup(...));
  $app->route('GET|POST', '/login' AuthController::login(...));

  $app->group('/', Middleware::auth(...), function (Routy $app) {
    $app->get('/feed', PostsController::list(...));
    $app->get('/account', AccountController::me(...));
    $app->post('/posts', PostsController::creat(...));
    $app->get('/@:username', )

    $app->get('/logout', AuthController::logout(...));
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
    Utils::renderView('views/signup.php', ['err' => @$err]);
  });

  $app->route('GET|POST', '/login', function (Routy $app) {
    if ($app->method == 'POST') {
      $body = $app->getBody();
      $svc = new Users(new DB());
      if (!($user = $svc->find($body->username)) || !password_verify($body->password, $user->password))
        $err = 'Incorrect username or password';
      else {
        session_start();
        $_SESSION['user'] = $user;
        header('location: ' . (@$_GET['next'] ?? '/'));
        exit;
      }
    }
    Utils::renderView('views/login.php', ['err' => @$err]);
  });

  $app->get('/logout', '\Services\Utils::auth', function (Routy $app) {
    session_start();
    session_destroy();
    $app->redirect('/');
  });

  $app->group('/posts', function (Routy $app) {
    $app->get('/:id', function (Routy $app) {
      $postsSvc = new Posts(new DB());
      if (!($post = $postsSvc->get($app->params->id))) {
        $app->status(404);
        return Utils::renderView('views/404.php');
      }
      $replies = $postsSvc->allByParent($post->id);
      Utils::renderView('views/post.php', [
        'post' => $post,
        'postsSvc' => $postsSvc,
        'replies' => $replies
      ]);
    });

    $app->post('/', '\Services\Utils::auth', function (Routy $app) {
      $body = $app->getBody();
      $postsSvc = new Posts(new DB());
      $post = $postsSvc->create(substr(htmlspecialchars($body->body), 0, 140), @$body->parent);
      $app->redirect("/posts/$post->id");
    });
  });

  $app->get('/@:username', function (Routy $app) {
    $db = new DB();
    $user = (new Users($db))->find($app->params->username);
    if (!$user) {
      $app->status(404);
      return Utils::renderView('views/404.php');
    }
    $postsSvc = new Posts($db);
    $posts = $postsSvc->allByAuthor($user->id);
    Utils::renderView('views/user.php', [
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
    $app->redirect($_GET['next'] ?: '/');
  });

  $app->get('/unauthorized', '\Services\Utils::auth', function (Routy $app) {
    $app->status(401);
    Utils::renderView('views/401.php');
  });

  $app->notFound(function (Routy $app) {
    $app->status(404);
    Utils::renderView('views/404.php');
  });
} catch (\Exception $ex) {
  Utils::renderView('views/error.php');
}
