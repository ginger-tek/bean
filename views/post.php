<div class="post-wrap">
  <?php

  if ($post->parent)
    echo \Services\Utils::renderPost($postsSvc->get($post->parent), ['type' => 'parent']);
  echo \Services\Utils::renderPost($post, ['type' => 'root view', 'noclick' => true]);

  ?>
</div>

<div class="grid">
  <?php if (isset($_SESSION['user'])) { ?>
    <dialog id="reply">
      <header>New Reply</header>
      <section>
        <form method="POST" action="/posts">
          <input name="parent" value="<?= $post->id ?>" hidden>
          <label><span id="replyCounter">0</span>/140
            <textarea name="body" placeholder="Reply..." oninput="replyCounter.innerText = this.value.length"
              maxlength="140" rows="5" required></textarea>
          </label>
          <div class="grid">
            <button type="reset" onclick="reply.close()">Cancel</button>
            <button type="submit"
              onclick="if(counter.innerText*1 > 0) {this.setAttribute('aria-busy','true');reply.close()}">Submit</button>
          </div>
        </form>
      </section>
    </dialog>
    <button onclick="reply.showModal()">Reply</button>
  <?php } else { ?>
    <button onclick="location.href='/login?next=<?= $_SERVER['REQUEST_URI'] ?>'">Reply</button>
  <?php } ?>
  <button onclick="navigator.share({title:'<?= $post->authorUsername ?> on Bean',text:'<?= $post->body ?>',url:location.href})">Share</button>
</div>
<hr>
<h3>Replies</h3>
<?php foreach ($replies as $reply)
  echo '<div class="post-wrap">' . \Services\Utils::renderPost($reply, ['type' => 'child']) . '</div>'; ?>