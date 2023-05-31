<?php

namespace Yeppi\Modules\Cleanup;

/**
 * Filters WordPress' default menu order
 */
function change_admin_menu_order( $menu_order ) {
  // define your new desired menu positions here
  // for example, move built-in pages to position #2
  $new_positions = [
    'edit.php?post_type=page'           => 2,
    'edit.php'                          => 3,
    'edit.php?post_type=events'         => 5,
    'edit.php?post_type=people'         => 6,
    'edit.php?post_type=call_to_action' => 7,
    'edit.php?post_type=promo_splitter' => 8,
    'upload.php'                        => 9,
  ];
  // helper function to move an element inside an array
  function move_element( &$array, $a, $b ) {
    $out = array_splice( $array, $a, 1) ;
    array_splice( $array, $b, 0, $out );
  }
  // traverse through the new positions and move
  // the items if found in the original menu_positions
  foreach ( $new_positions as $value => $new_index ) {
    if ( $current_index = array_search( $value, $menu_order ) ) {
      move_element( $menu_order, $current_index, $new_index );
    }
  }
  return $menu_order;
};

/**
 * Activates the 'menu_order' filter and then hooks into 'menu_order'
 */
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', __NAMESPACE__ . '\\change_admin_menu_order' );


/**
 * Clean up wp_head()
 *
 * Remove unnecessary <link>'s
 * Remove inline CSS and JS from WP emoji support
 * Remove inline CSS used by Recent Comments widget
 * Remove inline CSS used by posts with galleries
 * Remove self-closing tag
 */
function head_cleanup() {
  // Originally from https://wpengineer.com/1438/wordpress-header/
  remove_action( 'wp_head', 'feed_links_extra', 3 );
  add_action( 'wp_head', 'ob_start', 1, 0 );
  add_action( 'wp_head', function () {
    $pattern = '/.*' . preg_quote( esc_url( get_feed_link( 'comments_' . get_default_feed() ) ), '/' ) . '.*[\r\n]+/';
    echo preg_replace( $pattern, '', ob_get_clean() );
  }, 3, 0 );
  remove_action( 'wp_head', 'rsd_link' );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
  remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
  remove_action( 'wp_head', 'wp_oembed_add_host_js' );
  remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'use_default_gallery_style', '__return_false' );
  add_filter( 'emoji_svg_url', '__return_false' );
  add_filter( 'show_recent_comments_widget_style', '__return_false' );
}
add_action( 'init', __NAMESPACE__ . '\\head_cleanup' );


/**
 * Remove the WordPress version from RSS feeds
 */
add_filter( 'the_generator', '__return_false' );

/**
 * Disable default dashboard widgets
 */
function disable_default_dashboard_widgets(): void {
  remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );
  remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );
  remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\disable_default_dashboard_widgets' );


/**
 * Remove things from the admin bar displayed on front-end for logged-in users
 */
function admin_bar_edit( $wp_admin_bar ): void {
  $wp_admin_bar->remove_node( 'wp-logo' );
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_edit', 999 );


/**
 * Custom WordPress footer
 */
function admin_footer() {
  echo '<img class="footer-logo" src="'. get_stylesheet_directory_uri() . '/src/admin/is-logo.svg" alt="Michael Showes" /> <span>A custom WordPress solution by <a href="http://www.mshowes.com" rel="noopener" target="_blank">Michael Showes</a></span>';
}
add_filter( 'admin_footer_text', __NAMESPACE__ . '\\admin_footer' );


/**
 * Clean up language_attributes() used in <html> tag
 *
 * Remove dir="ltr"
 */
function language_attributes() {
  $attributes = [];

  if ( is_rtl() ) {
    $attributes[] = 'dir="rtl"';
  }

  $lang = get_bloginfo( 'language' );

  if ( $lang ) {
    $attributes[] = "lang=\"$lang\"";
  }

  $output = implode( ' ', $attributes );
  $output = apply_filters( 'yeppi/language_attributes', $output );

  return $output;
}
add_filter('language_attributes', __NAMESPACE__ . '\\language_attributes');


/**
 * Clean up output of stylesheet <link> tags
 */
function clean_style_tag( $input ) {
  preg_match_all( "!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches );
  if ( empty( $matches[2] ) ) {
    return $input;
  }
  // Only display media if it is meaningful
  $media = $matches[3][0] !== '' && $matches[3][0] !== 'all' ? ' media="' . $matches[3][0] . '"' : '';
  return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}
add_filter( 'style_loader_tag', __NAMESPACE__ . '\\clean_style_tag' );


/**
 * Clean up output of <script> tags
 */
function clean_script_tag( $input ) {
  $input = str_replace( "type='text/javascript' ", '', $input );
  $input = \preg_replace_callback(
    '/document.write\(\s*\'(.+)\'\s*\)/is',
    function ( $m ) {
      return str_replace( $m[1], addcslashes( $m[1], '"'), $m[0] );
    },
    $input
  );
  return str_replace( "'", '"', $input );
}
add_filter( 'script_loader_tag', __NAMESPACE__ . '\\clean_script_tag' );


/**
 * Add and remove body_class() classes
 */
function body_class( $classes ) {

  $classes[] = pathinfo( basename( get_page_template() ), PATHINFO_FILENAME );
  if ( is_admin_bar_showing() ) {
    $classes[] = 'admin-bar';
  }

  $blocklist = ['no-customize-support'];

  // Remove classes from array
  $classes = array_diff( $classes, $blocklist );

  return $classes;
}
add_filter( 'body_class', __NAMESPACE__ . '\\body_class' );


/**
 * Remove unnecessary self-closing tags
 */
function remove_self_closing_tags( $input ) {
  return str_replace( ' />', '>', $input );
}
add_filter( 'get_avatar', __NAMESPACE__ . '\\remove_self_closing_tags' ); // <img />
add_filter( 'comment_id_fields', __NAMESPACE__ . '\\remove_self_closing_tags' ); // <input />
add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\\remove_self_closing_tags' ); // <img />


/**
 * Don't return the default description in the RSS feed if it hasn't been changed
 */
function remove_default_description( $bloginfo ) {
  $default_tagline = 'Just another WordPress site';
  return ( $bloginfo === $default_tagline ) ? '' : $bloginfo;
}
add_filter( 'get_bloginfo_rss', __NAMESPACE__ . '\\remove_default_description' );
