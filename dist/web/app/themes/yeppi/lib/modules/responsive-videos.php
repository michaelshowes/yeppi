<?php

namespace Yeppi\Modules\ResponsiveVideos;

/**
 * Add responsive video wrapper for videos from known providers such as YouTube
 */
function video_embed_wrap( $cache, $url, $attr ) {

  // Check for videos, give them a responsive wrapper
  $video_domains =[
    'vimeo.com',
    'player.vimeo.com',
    'youtube.com',
    'youtu.be',
  ];

  if ( ! in_array( preg_replace( '#^www\.(.+\.)#i', '$1', parse_url( $url, PHP_URL_HOST ) ), $video_domains ) ) {
    return $cache;
  }

  $markup = new \DOMDocument();
  $markup->LoadHTML(
    preg_replace( '/&(?!amp;)/', '&amp;', $cache )
  );

  $markup_altered = FALSE;
  if ( $markup ) {
    $wrapper_div_template = $markup->createElement( 'div' );
    $wrapper_div_template->setAttribute( 'class', 'video-wrapper' );

    $container_div_template = $markup->createElement( 'div' );
    $container_div_template->setAttribute( 'class', 'video-container' );

    $iframes = $markup->getElementsByTagName( 'iframe' );
    foreach( $iframes as $iframe ) {
      $src = $iframe->attributes->getNamedItem( 'src' );
      if ( $src ) {
        $src_domain = parse_url( $src->value, PHP_URL_HOST );
        if ( strpos( $src_domain, 'www.' ) === 0 ) {
          $src_domain = substr( $src_domain, 4 );
        }

        if ( in_array( $src_domain, $video_domains ) ) {
          $wrapper_div = $wrapper_div_template->cloneNode();
          $container_div = $container_div_template->cloneNode();

          $src_width = $iframe->attributes->getNamedItem( 'width' );
          $src_height = $iframe->attributes->getNamedItem( 'height' );
          if ( $src_width && $src_height ) {
            $video_padding_ratio = round( $src_height->value / $src_width->value, 7 );

            if ( $video_padding_ratio < 0.56 || $video_padding_ratio > 0.565 ) {
              // Not a 16:9 ratio video, apply a custom ratio based on iframe dimensions
              $container_div->setAttribute( 'style', 'padding-bottom: ' . $video_padding_ratio * 100 . '%;' );
            }
          }

          $iframe->parentNode->replaceChild( $wrapper_div, $iframe );
          $wrapper_div->appendChild( $container_div );
          $container_div->appendChild( $iframe );

          $markup_altered = TRUE;
        }
      }
    }
  }

  if ( $markup_altered ) {
    $output = $markup->saveHTML();

    // saveHTML() inserts basic DOM container tags, gotta strip them out
    if ( $markup_start = strpos( $output, '<body>' ) ) {
      $output = substr( $output, $markup_start + 6 );
    }
    if ( $markup_end = strpos( $output, '</body>' ) ) {
      $output = substr( $output, 0, $markup_end );
    }

    return $output;
  }

  return $cache;
}
add_filter( 'embed_oembed_html', __NAMESPACE__ . '\\video_embed_wrap', 9, 3 );
