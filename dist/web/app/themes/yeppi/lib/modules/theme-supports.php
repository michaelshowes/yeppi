<?php

namespace Yeppi\Modules\ThemeSupports;

/*
 * Let WordPress manage the document title.
 * By adding theme support, we declare that this theme does not use a
 * hard-coded <title> tag in the document head, and expect WordPress to
 * provide it for us.
 */
add_theme_support( 'title-tag' );

/*
 * Switch default core markup for search form, comment form, and comments
 * to output valid HTML5.
 */
add_theme_support(
  'html5', [
    'gallery',
    'caption',
    'search-form'
  ]
);

/*
 * Enable CSS to be included in the WYSIWYG
 */
add_theme_support( 'editor-styles' );

/*
 * Add filetime as query arg to bust cache in admin
 */
function fresh_editor_style( $mce_css_string ) {
  global $editor_styles;
  $mce_css_list = explode( ',', $mce_css_string );

  if ( ! empty( $editor_styles ) ) {
    foreach ( $editor_styles as $filename ) {
      foreach ( $mce_css_list as $key => $fileurl ) {
        if ( strstr( $fileurl, '/' . $filename ) ) {
          $filetime = filemtime( get_stylesheet_directory() . '/' . $filename );
          $mce_css_list[ $key ] = add_query_arg( 'ver', $filetime, $fileurl );
        }
      }
    }
  }

  return implode( ',', $mce_css_list );
}
add_filter( 'mce_css', __NAMESPACE__ . '\\fresh_editor_style' );

/**
 * Allow SVG Upload
 */
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes ) {

  $filetype = wp_check_filetype( $filename, $mimes );

  return [
    'ext'             => $filetype['ext'],
    'type'            => $filetype['type'],
    'proper_filename' => $data['proper_filename']
  ];

}, 10, 4 );

function add_svg_to_mime_types( $mimes ) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter( 'upload_mimes', __NAMESPACE__ . '\\add_svg_to_mime_types' );

function fix_svg_thumb_display() {
  echo '<style> .media-icon img[src$=".svg"], img[src$=".svg"].attachment-post-thumbnail { width: 100% !important; height: auto !important; } </style>';
}
add_action( 'admin_head', __NAMESPACE__ . '\\fix_svg_thumb_display' );
