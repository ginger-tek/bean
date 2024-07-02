<?php

if (preg_match('#\.(?:css|js|png|jpeg|jpg|gif|ico|webp)$#', $_SERVER['REQUEST_URI'])) return false;

session_start(['read_and_close' => true]);

require 'vendor/autoload.php';
spl_autoload_register(fn ($c) => include "$c.php");

use GingerTek\Routy\Routy;
use Controllers\Middleware;
use Controllers\AuthController;
use Controllers\AccountController;
use Controllers\PostsController;

$app = new Routy(['layout' => 'views/_layout.php']);

try {
  $app->route('GET|POST', '/signup', AuthController::signup(...));
  $app->route('GET|POST', '/login', AuthController::login(...));

  $app->group('/', Middleware::auth(...), function (Routy $app) {
    $app->get('/', PostsController::list(...));
    $app->get('/account', AccountController::view(...));
    $app->post('/account', AccountController::save(...));
    $app->get('/posts/:id', PostsController::view(...));
    $app->post('/posts', PostsController::create(...));
    $app->get('/@:username', AccountController::viewByUsername(...));

    $app->get('/logout', AuthController::logout(...));
  });

  $app->notFound(fn (Routy $app) => $app->render('views/404.php'));
} catch (\Exception $ex) {
  $app->status(500)->render('views/error.php');
}
