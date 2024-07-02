<?php

namespace Controllers;

use GingerTek\Routy\Routy;

class Middleware
{
  static function auth(Routy $app)
  {
    if (!isset($_SESSION['user']))
      $app->redirect('/login');
  }
}
