<div class="post view">
  <?php if (@$parent) { ?>
    <div class="post parent" onclick="location.href='/posts/<?= $parent->id ?>'">
      <div class="header">
        <div class="author">
          <?= $parent->authorDisplayName ?> (<a href="/@<?= $parent->authorUsername ?>">@<?= $parent->authorUsername ?></a>)
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
      <?= $post->authorDisplayName ?> (<a href="/@<?= $post->authorUsername ?>">@<?= $post->authorUsername ?></a>)
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

<?php if (isset($_SESSION['user'])) { ?>
  <button onclick="reply.showModal()">Reply</button>
  <dialog id="reply">
    <header>New Reply</header>
    <section>
      <form method="POST" action="/posts">
        <input name="parent" value="<?= $post->id ?>" hidden>
        <label><span id="counter">0</span>/140
          <textarea name="body" placeholder="Reply..." oninput="counter.innerText = this.value.length" maxlength="140"
            rows="5" required></textarea>
        </label>
        <div class="grid">
          <button type="reset" onclick="reply.close()">Cancel</button>
          <button type="submit"
            onclick="if(counter.innerText*1 > 0) {this.setAttribute('aria-busy','true');reply.close()}">Submit</button>
        </div>
      </form>
    </section>
  </dialog>
<?php } else { ?>
  <button onclick="location.href='/login?next=<?= $_SERVER['REQUEST_URI'] ?>'">Reply</button>
<?php } ?>

<?php foreach ($replies as $reply) { ?>
  <div class="post root" onclick="location.href='/posts/<?= $reply->id ?>'">
    <div class="header">
      <div class="author">
        <?= $reply->authorDisplayName ?> (<a href="/@<?= $reply->authorUsername ?>">@
          <?= $reply->authorUsername ?>
        </a>)
      </div>
      <div class="created" title="<?= date('Y-m-d g:i:s A', $reply->created) ?>">
        <?= date('j M y', $reply->created) ?>
      </div>
    </div>
    <div><i>Repling to <a href="/@<?= $reply->authorUsername ?>">@
          <?= $reply->authorUsername ?>
        </a></i></div>
    <div class="body">
      <?= \Services\Utils::parse($reply->body) ?>
    </div>
    <?= $reply->commentsCount > 0 ? "<div class=\"metrics\">$reply->commentsCount Replies</div>" : '' ?>
  </div>
<?php } ?>