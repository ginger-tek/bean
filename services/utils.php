<?php

namespace Services;

class Utils
{
  static function render($view, $variables = [])
  {
    $variables['view'] = $view;
    extract($variables);
    include_once 'layout.php';
    exit;
  }

  static function parse($body)
  {
    if (preg_match_all('#(https://\S+|\S+\.\S{2,9}\S+)#m', $body, $links)) {
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

  static function auth()
  {
    if (!isset($_SESSION['user']))
      header('location: /login');
  }
}