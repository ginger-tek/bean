<?php

session_start(['read_and_close' => true]);

class DB {
  public PDO $conn;
  
  public function __construct() {
    $this->conn = new PDO('sqlite:data/bean.db', null, null, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ]);
    $this->conn->exec(file_get_contents('data/schema.sql'));
  }

  function run($sql, $params = []) {
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }
}

class Users {
  private DB $db;

  public function __construct(DB $db) {
    $this->db = $db;
  }

  function create($username, $password, $displayName = '') {
    $id = uniqid();
    $this->db->run("insert into users (id, username, password, displayName, created) values (?, ?, ?, ?, ?)", [
      $id, $username, password_hash($password, PASSWORD_BCRYPT), $displayName, time()
    ]);
    return $this->get($id);
  }

  function get($id) {
    return $this->db->run("select * from users where id = ?", [$id])->fetch();
  }

  function find($username) {
    return $this->db->run("select * from users where username = ?", [$username])->fetch();
  }

  function update($displayName, $bio): bool {
    return (bool)$this->db->run("update users set displayName = ?, bio = ? where id = ?", [
      $displayName, $bio, $_SESSION['user']->id
    ])->rowCount();
  }
}

class Posts {
  private DB $db;

  public function __construct(DB $db) {
    $this->db = $db;
  }

  function create($body, $parent = null) {
    $id = uniqid();
    $this->db->run("insert into posts (id, body, parent, author, created) values (?, ?, ?, ?, ?)", [
      $id, $body, $parent, $_SESSION['user']->id, time()
    ]);
    return $this->get($id);
  }

  function get($id) {
    return $this->db->run("select * from v_posts where id = ?", [$id])->fetch();
  }

  function all() {
    return $this->db->run("select * from v_posts order by created desc")->fetchAll();
  }

  function allByParent($id) {
    return $this->db->run("select * from v_posts where parent = ? order by created desc", [$id])->fetchAll();
  }

  function allByAuthor($userId) {
    return $this->db->run("select * from v_posts where author = ? order by created desc", [$userId])->fetchAll();
  }
}

function auth() {
  if (!isset($_SESSION['user']))
    header('location: /login');
}

function render($view, $variables = []) {
  $variables['view'] = $view;
  extract($variables);
  include 'layout.php';
}

function parse($body) {
  if (preg_match_all('#(https://\S+)#m', $body, $links)) {
    foreach ($links[0] as $link) {
      if (preg_match('#(youtube\.com/watch\?v=|youtu\.be)(\w+)#', $link, $vid)) {
        $body = str_replace($link, '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/'.$vid[2].'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', $body);
      } elseif (preg_match('#\.(jpe?g|png|gif|webp)#', $link)) {
        $body = str_replace($link, "<img src=\"$link\" target=\"_blank\" loading=\"lazy\">", $body);
      } else $body = str_replace($link, "<a href=\"$link\" target=\"_blank\">$link</a>\r\n", $body);
    }
  }
  return "<pre>$body</pre>";
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

if (isset($_SESSION['user'])) {
  if ($body = @$_POST['body']) {
    $svc = new Posts(new DB());
    $post = $svc->create(substr(htmlspecialchars($body), 0, 140), @$_POST['parent']);
    header("location: /post?id=$post->id");
  }
  
  if (isset($_POST['save'])) {
    $svc = new Users(new DB());
    $svc->update($_POST['displayName'], $_POST['bio']);
    session_start();
    $_SESSION['user']->displayName = substr($_POST['displayName'], 0, 25);
    $_SESSION['user']->bio = substr($_POST['bio'], 0, 100);
    header('location: ' . $_SERVER['REQUEST_URI']);
    exit;
  }

  if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header('location: /');
    exit;
  }
}

// create_user('admin', 'secretpassword', 'Admin');

try {
  (match($method.$uri) {
    'GET/' => function() {
      $svc = new Posts(new DB());
      $posts = $svc->all();
      render('views/feed.php', ['posts' => $posts, 'svc' => $svc]);
    },
    'GET/login',
    'POST/login' => function() {
      if (($username = @$_POST['username']) && ($password = @$_POST['password'])) {
        $svc = new Users(new DB());
        if (!($user = $svc->find($username))) $err = 'User not found';
        else if (!password_verify($password, $user->password)) $err = 'Incorrect password';
        else {
          session_start();
          $_SESSION['user'] = $user;
          header('location: ' . (@$_GET['next'] ?? '/'));
          exit;
        }
      }
      render('views/login.php', ['err' => @$err]);
    },
    'GET/signup',
    'POST/signup' => function() {
      if (($displayName = @$_POST['displayName']) && ($username = @$_POST['username']) && ($password = @$_POST['password'])) {
        $svc = new Users(new DB());
        if ($svc->find($username)) $err = 'Username taken';
        else {
          $user = $svc->create(substr($username,0,15), substr($password,0,25), substr($displayName,0,25));
          session_start();
          $_SESSION['user'] = $user;
          header('location: /');
          exit;
        }  
      }
      render('views/signup.php', ['err' => @$err]);
    },
    'GET/user' => function() {
      $db = new DB();
      $user = (new Users($db))->find($_GET['id']);
      $svc = new Posts($db);
      $posts = $svc->allByAuthor($user->id);
      render('views/user.php', [
        'user' => $user,
        'posts' => $posts,
        'svc' => $svc
      ]);
    },
    'GET/post' => function() {
      $svc = new Posts(new DB());
      $post = $svc->get($_GET['id']);
      $posts = $svc->allByParent($post->id);
      render('views/post.php', [
        'post' => $post,
        'replies' => $posts,
        'svc' => $svc
      ]);
    },
    default => function() {
      render('views/404.php');
    }
  })();
} catch(Exception $ex) {
  render('views/error.php', ['msg' => $ex->getMessage()]);
}