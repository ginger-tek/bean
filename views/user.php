<?php if (isset($model['canEdit'])) { ?>
  <h2>Account</h2>
  <div class="grid mb-1">
    <a role="button" href="/@<?= $model['user']->username ?>">View Profile</a>
    <a role="button" href="/logout">Logout</a>
  </div>
  <label>Username
    <input value="<?= $model['user']->username ?>" readonly>
  </label>
  <form method="post">
    <label>Display Name
      <input name="displayName" value="<?= $model['user']->displayName ?>">
    </label>
    <label>Bio
      <textarea name="bio" placeholder="Hi! I'm new here!"><?= $model['user']->bio ?></textarea>
    </label>
    <button type="submit" onclick="this.setAttribute('aria-busy','true')">Save</button>
  </form>
<?php } else { ?>
  <h2 style="margin-bottom:0">
    <?= $model['user']->displayName ?>
  </h2>
  <div>
    <a href="/@<?= $model['user']->username ?>">@<?= $model['user']->username ?></a>
  </div>
  <pre class="bio"><?= $model['user']->bio ? \Services\Utils::parse(htmlspecialchars($model['user']->bio)) : '<i>Hi! I\'m new here!</i>' ?></pre>
  <p class="joined">Joined
    <?= date('j M y', $model['user']->created) ?>
  </p>
  <hr>
  <h3>Posts</h3>
  <?php
  foreach ($model['posts'] as $post) {
    echo '<div class="post-wrap">';
    if ($post->parent)
      echo \Services\Utils::renderPost($model['postsSvc']->get($post->parent), ['type' => 'parent', 'noclick' => true]);
    echo \Services\Utils::renderPost($post, ['postsSvc' => $postsSvc]);
    echo '</div>';
  }

  if (count($model['posts']) == 0) { ?>
    <div style="text-align:center">
      <p>No posts yet</p>
    </div>
<?php }
} ?>