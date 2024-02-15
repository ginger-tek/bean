<?php foreach ($posts as $post) { ?>
  <div class="post root" onclick="location.href='/posts/<?= $post->id ?>'">
    <?php if ($post->parent) {
      $parent = $postsSvc->get($post->parent); ?>
      <div class="post parent" href="/posts/<?= $parent->id ?>">
        <div class="header">
          <div class="author">
            <?= $parent->authorDisplayName ?> (<a href="/@<?= $parent->authorUsername ?>">@<?= $parent->authorUsername ?></a>)
          </div>
          <div class="created" title="<?= date('Y-m-d g:i:s A', $parent->created) ?>">
            &bull; <?= date('j M y', $parent->created) ?>
          </div>
        </div>
        <div class="body">
          <?= $parent->body ?>
        </div>
        <div class="metrics"><?= $parent->commentsCount > 0 ? "<span>$parent->commentsCount Replies</span>" : '' ?></div>
      </div>
    <?php } ?>
    <div class="header">
      <div class="author">
        <?= $post->authorDisplayName ?> (<a href="/@<?= $post->authorUsername ?>">@<?= $post->authorUsername ?></a>)
      </div>
      <div class="created" title="<?= date('Y-m-d g:i:s A', $post->created) ?>">
        &bull; <?= date('j M y', $post->created) ?>
      </div>
    </div>
    <div class="body">
      <?= \Services\Utils::parse($post->body) ?>
    </div>
    <div class="metrics"><?= $post->commentsCount > 0 ? "<span>$post->commentsCount Replies</span>" : '' ?></div>
  </div>
<?php }
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