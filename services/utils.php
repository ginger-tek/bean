<?php

namespace Services;

class Utils
{
  static function parse(string $body): string
  {
    if (preg_match_all('#((https://){0,1}[-a-zA-Z0-9@:%._\+~\#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~\#?&//=]*))#m', $body, $links, PREG_PATTERN_ORDER)) {
      foreach ($links[0] as $link) {
        if (preg_match('#(youtube\.com/watch\?v=|youtu\.be)(\w+)#', $link, $vid)) {
          $body = str_replace($link, '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/' . $vid[2] . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>', $body);
        } elseif (preg_match('#\.(jpe?g|png|gif|webp)#', $link)) {
          $body = str_replace($link, "<img src=\"$link\" target=\"_blank\" loading=\"lazy\">", $body);
        } else {
          $body = str_replace($link, "<a onclick=\"event.stopPropagation()\" href=\"" . (!str_starts_with($link, 'https://') ? "https://$link" : $link) . "\" target=\"_blank\">$link</a>", $body);
        }
      }
    }
    return "<pre>$body</pre>";
  }

  static function renderPost(object $post, array $options = []): string
  {
    $createdD = date('j M y', $post->created);
    $createdDT = date('j M y g:i:s A', $post->created);
    $type = $options['type'] ?? '';
    $classes = join(' ', ['post', $type]);
    $body = Utils::parse($post->body);
    $commentsCount = $post->commentsCount > 0 ? "<span>$post->commentsCount Replies</span>" : '';
    $onclick = $options['noclick'] ? '' : "onclick=\"location.href='/posts/{$post->id}'\"";
    $html = <<<EOT
    <div class="{$classes}" {$onclick}>
      <div class="header">
        <div class="author">
          {$post->authorDisplayName} (<a href="/@{$post->authorUsername}">@{$post->authorUsername}</a>)
        </div>
        <div class="created" title="{$createdDT}">&bull; {$createdD}</div>
      </div>
      <div class="body">
        {$body}
      </div>
      <div class="metrics">
        {$commentsCount}
      </div>
    </div>
    EOT;
    return $html;
  }

  static function auth()
  {
    if (!isset($_SESSION['user']))
      header('location: /login');
  }
}