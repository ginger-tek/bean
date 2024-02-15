<div class="post-wrap">
<?php

if ($post->parent)
  echo \Services\Utils::renderPost($postsSvc->get($post->parent), ['type' => 'parent']);
echo \Services\Utils::renderPost($post, ['type' => 'root view', 'noclick' => true]);

?>
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

<?php foreach ($replies as $reply)
  echo '<div class="post-wrap">' . \Services\Utils::renderPost($reply, ['type' => 'child']) . '</div>'; ?>