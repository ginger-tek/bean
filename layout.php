<html>
  <head>
    <title>Bean</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/styles.css">
  </head>
  <body>
    <header>
      <nav>
        <a href="/" style="margin-right:1em"><b>Bean</b></a>
        <?php if (isset($_SESSION['user'])) { ?>
          <a onclick="newPost.showModal()">New Post</a>
          <a onclick="account.showModal()">Account</a>
          <dialog id="newPost">
            <header>New Post</header>
            <section>
              <form method="POST">
                <label><span id="counter">0</span>/140
                  <textarea name="body" placeholder="New post..." oninput="counter.innerText = this.value.length" maxlength="140" rows="5" required></textarea>
                </label>
                <div class="grid">
                  <button type="reset" onclick="newPost.close()">Cancel</button>
                  <button type="submit" onclick="if(counter.innerText*1 > 0) {this.setAttribute('aria-busy','true');newPost.close()}">Submit</button>
                </div>
              </form>
            </section>
          </dialog>
          <dialog id="account">
            <header>Account (@<?= $_SESSION['user']->username ?>)</header>
            <section>
              <div class="grid">
                <a role="button" href="/user?id=<?= $_SESSION['user']->username ?>">My Profile</a>
                <a role="button" href="?logout=1">Logout</a>
              </div>
              <hr>
              <form method="POST">
                <input name="save" value="1" hidden required>
                <label>Display Name
                  <input name="displayName" value="<?= $_SESSION['user']->displayName ?>" maxlength="25" required>
                </label>
                <label>Bio
                  <textarea name="bio" rows="4" maxlength="100"><?= $_SESSION['user']->bio ?></textarea>
                </label>
                <p>Joined <?= date('j M y @ g:i:s A', $_SESSION['user']->created) ?></p>
                <div class="grid">
                  <button type="button" onclick="account.close()">Close</button>
                  <button type="submit" onclick="this.setAttribute('aria-busy','true');account.close()">Save</button>
                </div>
              </form>
            </section>
          </dialog>
        <?php } else { ?>
          <div>
            <a href="/signup">Signup</a>
            <a href="/login">Login</a>
          </div>
        <?php } ?>
      </nav>
    </header>
    <main>
      <?php include $view; ?>
    </main>
  </body>
</html>