<?php

foreach ($posts as $post) {
  echo '<div class="post-wrap">';
  if ($post->parent)
    echo \Services\Utils::renderPost($postsSvc->get($post->parent), ['type' => 'parent', 'noclick' => true]);
  echo \Services\Utils::renderPost($post, ['postsSvc' => $postsSvc]);
  echo '</div>';
}

if (count($posts) == 0) { ?>
  <div style="text-align:center">
    <h2>Pretty quiet in here</h2>
    <p>There are no posts yet</p>
    <?php if (!isset($_SESSION['user'])) { ?>
      <br>
      <p>But you can help with that!</p>
      <p><a href="/login">Login</a> or <a href="/signup">Signup</a></p>
    <?php } ?>
  </div>
<?php } ?>

<script>
  setInterval(() => {
    if (document.visibilityState == 'visible' && !newPost.hasAttribute('open') && !account.hasAttribute('open'))
      location.reload()
  }, 10000)
</script>