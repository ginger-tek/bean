<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Bean</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/styles.css">
  <link rel="icon" href="/assets/favicon.ico">
</head>

<body>
  <header>
    <nav>
      <a href="/" class="brand">
        <img src="/assets/favicon.ico"><b>Bean</b>
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
        <span role="link" onclick="newPost.showModal()">New Post</span>
        <a href="/account">Account</a>
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
  <script>
    navigation.onnavigate = () => document.body.style.opacity = 0
    window.onload = () => document.body.style.opacity = 1
  </script>
</body>

</html>