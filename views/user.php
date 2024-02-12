<h2 style="margin-bottom:0">
  <?= $user->displayName ?>
</h2>
<div>
  <a href="/@<?= $user->username ?>">@<?= $user->username ?></a>
</div>
<pre class="bio"><?= $user->bio ? \Services\Utils::parse(htmlspecialchars($user->bio)) : '<i>Hi! I\'m new here!</i>' ?></pre>
<p class="joined">Joined
  <?= date('j M y', $user->created) ?>
</p>
<hr>
<h3>Posts</h3>
<?php foreach ($posts as $post) { ?>
  <div class="post root" onclick="location.href='/posts/<?= $post->id ?>'">
    <?php if ($post->parent) {
      $parent = $postsSvc->get($post->parent); ?>
      <div class="post parent" onclick="location.href='/posts/<?= $parent->id ?>'">
        <div class="header">
          <div class="author">
            <?= $parent->authorDisplayName ?> (<a href="/@<?= $parent->authorUsername ?>">@
              <?= $parent->authorUsername ?>
            </a>)
          </div>
          <div class="created" title="<?= date('Y-m-d g:i:s A', $parent->created) ?>">
            <?= date('j M y', $parent->created) ?>
          </div>
        </div>
        <div class="body">
          <?= $parent->body ?>
        </div>
        <?= $parent->commentsCount > 0 ? "<div class=\"metrics\">$parent->commentsCount Replies</div>" : '' ?>
      </div>
    <?php } ?>
    <div class="header">
      <div class="author">
        <?= $post->authorDisplayName ?> (<a href="/@<?= $post->authorUsername ?>">@
          <?= $post->authorUsername ?>
        </a>)
      </div>
      <div class="created" title="<?= date('Y-m-d g:i:s A', $post->created) ?>">
        <?= date('j M y', $post->created) ?>
      </div>
    </div>
    <div class="body">
      <?= \Services\Utils::parse($post->body) ?>
    </div>
    <?= $post->commentsCount > 0 ? "<div class=\"metrics\">$post->commentsCount Replies</div>" : '' ?>
  </div>
<?php }
if (count($posts) == 0) { ?>
  <div style="text-align:center">
    <p>No posts yet</p>
  </div>
<?php } ?>