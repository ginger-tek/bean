<h2 style="margin-bottom:0">
  <?= $user->displayName ?>
</h2>
<div>
  <a href="/@<?= $user->username ?>">@
    <?= $user->username ?>
  </a>
</div>
<pre
  class="bio"><?= $user->bio ? \Services\Utils::parse(htmlspecialchars($user->bio)) : '<i>Hi! I\'m new here!</i>' ?></pre>
<p class="joined">Joined
  <?= date('j M y', $user->created) ?>
</p>
<hr>
<h3>Posts</h3>
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
    <p>No posts yet</p>
  </div>
<?php } ?>