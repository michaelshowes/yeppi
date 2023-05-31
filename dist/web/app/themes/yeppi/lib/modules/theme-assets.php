<?php

namespace Yeppi\Modules\ThemeAssets;

/** Theme assets */
function theme_assets() {

  /** De-register default jQuery and include from our repo */
  wp_deregister_script( 'jquery' );
  wp_enqueue_script( 'jquery', get_template_directory_uri() . '/assets/js/jquery.min.js', [], '3.6.0', FALSE );

  /** Add custom JS */
  wp_enqueue_script( 'custom-js', get_template_directory_uri() . '/assets/js/bundle.js', ['jquery'], filemtime( get_template_directory() . '/assets/js/bundle.js' ), FALSE );

  /** Remove Gutenberg block CSS */
  wp_dequeue_style( 'wp-block-library' );

  /** Add custom CSS */
  wp_enqueue_style( 'custom-css', get_template_directory_uri() . '/assets/css/style.css', [], filemtime( get_template_directory() . '/assets/css/style.css' ), FALSE );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\theme_assets' );

/**
 * Add Admin CSS and JS
 */
// function admin_theme_scripts() {
//   wp_enqueue_style( 'yeppi-admin-style', get_template_directory_uri() . '/assets/admin/admin.css', [], filemtime( get_template_directory() . '/assets/admin/admin.css' ), FALSE );
  // wp_enqueue_script( 'yeppi-admin-js', get_template_directory_uri() . '/assets/admin/admin.js', [ 'jquery' ], filemtime(get_template_directory() . '/assets/admin/admin.js' ), FALSE);
// }
// add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_theme_scripts' );

/**
 * Change folder for ACF Flexible Content Field Extended Preview Images
 *
 * @return string
 */
function override_acf_flexible_preview_images_directory() {
  return 'src/admin/acf-flexible-content-extended';
}
add_filter( 'acf-flexible-content-extended.images_path', __NAMESPACE__ . '\\override_acf_flexible_preview_images_directory' );
