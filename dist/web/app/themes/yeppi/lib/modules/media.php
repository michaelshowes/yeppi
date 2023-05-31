<?php

namespace Yeppi\Modules\Media;

/*
 * Register custom image sizes
 */
add_image_size( 'full-width', 990 );
add_image_size( 'left-right', 350 );


/*
 * Set image quality
 */
function set_quality() {
  return 100;
}
add_filter( 'jpeg_quality',  __NAMESPACE__ . '\\set_quality' );
add_filter( 'wp_editor_set_quality',  __NAMESPACE__ . '\\set_quality' );


/*
 * Set image links to "None"
 */
function default_attachment_display_settings() {
  update_option( 'image_default_link_type', 'none' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\default_attachment_display_settings' );


/*
 * Add WYSIYG image sizes
 */
function add_editor_image_sizes( array $sizes ): array {
  $custom_sizes = [
    'full-width' => 'Full-Width',
    'left-right' => 'Left or Right'
  ];

  unset( $sizes['full'] );

  return array_merge( $sizes, $custom_sizes );
}
add_filter( 'image_size_names_choose', __NAMESPACE__ . '\\add_editor_image_sizes' );

/**
 * Wrap embedded media as suggested by Readability
 */
function embed_wrap( $cache ) {
  return '<div class="oembed">' . $cache . '</div>';
}
add_filter( 'embed_oembed_html', __NAMESPACE__ . '\\embed_wrap' );


/**
 * Remove srcset and stuff on images
 */
add_filter( 'wp_calculate_image_srcset_meta', '__return_empty_array' );


/*
 * Remove default image sizes
 */
function remove_default_image_sizes( $sizes, $metadata ) {
  $disabled_sizes = [
    'thumbnail',
    'medium',
    'medium_large',
    'large',
    '1536x1536',
    '2048x2048'
  ];

  // unset disabled sizes
  foreach ( $disabled_sizes as $size ) {
    if ( ! isset( $sizes[ $size ] ) ) {
      continue;
    }
    unset( $sizes[ $size ] );
  }
  return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced',  __NAMESPACE__ . '\\remove_default_image_sizes', 10, 2 );


/**
 * Modify markup for WYSIWYG images with a caption
 */
function modify_captioned_image_markup( $empty, $attr, $content = null ) {

  $atts = shortcode_atts(
    [
      'id'         => '',
      'caption_id' => '',
      'align'      => 'none',
      'width'      => '',
      'caption'    => '',
      'class'      => '',
    ],
    $attr,
    'caption'
  );

  $atts['width'] = (int) $atts['width'];

  if ( $atts['width'] < 1 || empty( $atts['caption'] ) ) {
    return $content;
  }

  $atts['id'] = sanitize_html_class( $atts['id'] );
  $id         = esc_attr( $atts['id'] );
  $raw_id     = preg_replace( '/attachment_/', '', $atts['id'] );

  $alignment = preg_replace( '/align/', '', $atts['align'] );
  preg_match( '/class="[^"]*size-([^\s"]+)[^"]*"/', $content, $matches );

  if ( isset( $matches[1] ) ) {
    $size = $matches[1];
  } else {
    $size = 'full-width';
  }

  $img_data = wp_get_attachment_image_src( $raw_id, $size );

  $src    = $img_data[0];
  $width  = $img_data[1];
  $height = $img_data[2];
  $alt    = get_post_meta( $raw_id, '_wp_attachment_image_alt', TRUE );

  $caption    = htmlentities( $atts['caption'] );
  $caption_id = esc_attr( 'caption-' . str_replace( '_', '-', $id ) );

  $html  = "<figure id='$id' aria-describedby='$caption_id' class='wp-caption' data-alignment='$alignment' data-size='$size'>";
  $html .= "  <div class='figure-image-wrapper'>";
  $html .= "    <img src='$src' loading='lazy' alt='$alt' width='$width' height='$height'>";
  $html .= "  </div>";
  $html .= "  <figcaption id='$caption_id' class='wp-caption-text'>$caption</figcaption>";
  $html .= "</figure>";

  return $html;
}
add_filter( 'img_caption_shortcode', __NAMESPACE__ . '\\modify_captioned_image_markup', 10, 3 );
