<?php

namespace Yeppi\Modules\IconFont;

/**
 * Update icon file css path.
 *
 * @return string
 */
function override_acf_icomoon_filepath() {
  return get_stylesheet_directory() . '/assets/admin/icons.css';
}
add_filter( 'icomoon_filepath', __NAMESPACE__ . '\\override_acf_icomoon_filepath' );

/**
 * Update icon file css url
 *
 * @return string
 */
function override_acf_icomoon_fileurl() {
  return get_stylesheet_directory_uri() . '/assets/admin/icons.css';
}
add_filter( 'icomoon_fileurl', __NAMESPACE__ . '\\override_acf_icomoon_fileurl' );

/**
 * Update icon files.
 *
 * @return array
 */
function override_acf_icomoon_fonts() {
  return [
    'woff2' => get_stylesheet_directory_uri() . '/assets/fonts/icomoon.woff2?twldeg',
    'ttf'  => get_stylesheet_directory_uri() . '/assets/fonts/icomoon.ttf?twldeg',
    'woff' => get_stylesheet_directory_uri() . '/assets/fonts/icomoon.woff?twldeg',
    'svg'  => get_stylesheet_directory_uri() . '/assets/fonts/icomoon.svg?twldeg#icomoon'
  ];
}
add_filter( 'icomoon_fonts', __NAMESPACE__ . '\\override_acf_icomoon_fonts' );

/**
 * font family for theme
 */
function override_acf_icomoon_font_family() {
  return 'icomoon';
}
add_filter( 'icomoon_font_family_name', __NAMESPACE__ . '\\override_acf_icomoon_font_family' );
