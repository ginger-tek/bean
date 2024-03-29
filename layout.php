<!DOCTYPE html>
<html lang="en">

<head>
  <title>Bean</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/styles.css">
  <link rel="icon" href="/favicon.ico">
</head>

<body>
  <header>
    <nav>
      <a href="/" class="brand">
        <img src="/favicon.ico"><b>Bean</b>
      </a>
      <?php if (isset($_SESSION['user'])) { ?>
        <dialog id="newPost">
          <header>New Post</header>
          <section>
            <form method="POST" action="/posts">
              <label><span id="newPostCounter">0</span>/140
                <textarea name="body" placeholder="New post..." oninput="newPostCounter.innerText = this.value.length" maxlength="140" rows="5" required></textarea>
              </label>
              <div class="grid">
                <button type="reset" onclick="newPost.close()">Cancel</button>
                <button type="submit" onclick="if(counter.innerText*1 > 0) {this.setAttribute('aria-busy','true');newPost.close()}">Submit</button>
              </div>
            </form>
          </section>
        </dialog>
        <dialog id="account">
          <header>Hello,
            <?= $_SESSION['user']->username ?>
          </header>
          <section>
            <div class="grid">
              <a role="button" href="/@<?= $_SESSION['user']->username ?>">My Profile</a>
              <a role="button" href="/logout">Logout</a>
            </div>
            <hr>
            <form method="POST" action="/profile?next=<?= $_SERVER['REQUEST_URI'] ?>">
              <input name="save" value="1" hidden required>
              <label>Display Name
                <input name="displayName" value="<?= $_SESSION['user']->displayName ?>" maxlength="25" required>
              </label>
              <label>Bio
                <textarea name="bio" rows="4" maxlength="140"><?= $_SESSION['user']->bio ?></textarea>
              </label>
              <p>Joined
                <?= date('j M y @ g:i:s A', $_SESSION['user']->created) ?>
              </p>
              <div class="grid">
                <button type="button" onclick="account.close()">Close</button>
                <button type="submit" onclick="this.setAttribute('aria-busy','true');account.close()">Save</button>
              </div>
            </form>
          </section>
        </dialog>
        <a onclick="newPost.showModal()">New Post</a>
        <a onclick="account.showModal()">Account</a>
      <?php } else { ?>
        <div>
          <a href="/signup">Signup</a>
          <a href="/login">Login</a>
        </div>
      <?php } ?>
    </nav>
  </header>
  <main>
    <?php include_once $view; ?>
  </main>
</body>

</html>
