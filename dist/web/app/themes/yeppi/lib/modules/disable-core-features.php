<?php

namespace Yeppi\Modules\DisableCoreFeatures;

/*
 * Disable Gutenberg
 */
add_filter( 'use_block_editor_for_post_type', '__return_false', 10);


/*
 * Remove full-site editing stuff
 */
function remove_fse_support() {

  // remove SVG and global styles
  remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );

  // remove wp_footer actions which add's global inline styles
  remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

  // remove render_block filters which adding unnecessary stuff
  remove_filter( 'render_block', 'wp_render_duotone_support' );
  remove_filter( 'render_block', 'wp_restore_group_inner_container' );
  remove_filter( 'render_block', 'wp_render_layout_support_flag' );
}
add_action( 'init', __NAMESPACE__ . '\\remove_fse_support', 10 );


/*
 * Remove post formats and the default editor
 */
function remove_support() {
  remove_post_type_support( 'post', 'post-formats' );
  remove_post_type_support( 'page', 'post-formats' );
  remove_post_type_support( 'post', 'editor' );
  remove_post_type_support( 'page', 'editor' );
  remove_post_type_support( 'post', 'comments' );
  remove_post_type_support( 'page', 'comments' );
  remove_post_type_support( 'post', 'trackbacks' );
  remove_post_type_support( 'page', 'trackbacks' );

  // Disable Gutenberg theme support
  remove_theme_support( 'core-block-patterns' );
  remove_theme_support( 'widgets-block-editor' );
}
add_action( 'init', __NAMESPACE__ . '\\remove_support', 10 );


/**
 * Disable pingback XMLRPC method
 */
function filter_xmlrpc_method( $methods ) {
  unset( $methods['pingback.ping'] );
  return $methods;
}
add_filter( 'xmlrpc_methods', __NAMESPACE__ . '\\filter_xmlrpc_method', 10, 1);


/**
 * Remove pingback header
 */
function filter_headers( $headers ) {
  if ( isset( $headers['X-Pingback'] ) ) {
    unset( $headers['X-Pingback'] );
  }
  return $headers;
}
add_filter( 'wp_headers', __NAMESPACE__ . '\\filter_headers', 10, 1);


/**
 * Kill trackback rewrite rule
 */
function filter_rewrites( $rules ) {
  foreach ( $rules as $rule => $rewrite ) {
    if ( preg_match( '/trackback\/\?\$$/i', $rule ) ) {
      unset( $rules[ $rule ] );
    }
  }
  return $rules;
}
add_filter( 'rewrite_rules_array', __NAMESPACE__ . '\\filter_rewrites' );


/**
 * Kill bloginfo( 'pingback_url')
 */
function kill_pingback_url( $output, $show ) {
  if ( $show === 'pingback_url' ) {
    $output = '';
  }
  return $output;
}
add_filter( 'bloginfo_url', __NAMESPACE__ . '\\kill_pingback_url', 10, 2);


/**
 * Disable XMLRPC call
 */
function kill_xmlrpc( $action ) {
  if ( $action === 'pingback.ping' ) {
    wp_die( 'Pingbacks are not supported', 'Not Allowed!', ['response' => 403] );
  }
}
add_action( 'xmlrpc_call', __NAMESPACE__ . '\\kill_xmlrpc' );


/*
 * Set comment status to closed by default
 */
function default_comments_off(): void {
  update_option( 'default_comment_status', 'closed' );
}
add_action( 'after_switch_theme', __NAMESPACE__ . '\\default_comments_off', 0, 0 );


/**
 * Remove admin bar items
 */
function remove_admin_bar_menus( \WP_Admin_Bar $wp_admin_bar ): void {
  $wp_admin_bar->remove_node( 'wp-logo' );
  $wp_admin_bar->remove_node( 'comments' );
  $wp_admin_bar->remove_node( 'search' );
}
add_action( 'admin_bar_menu', __NAMESPACE__ . '\\remove_admin_bar_menus', 71, 1 );

/**
 * Remove comments from admin menu
 *
 * Disable default dashboard widgets and page/post widgets for comments
 */
function disable_menu_and_meta_boxes(): void {
  remove_menu_page( 'edit-comments.php' );
  remove_submenu_page( 'options-general.php', 'options-discussion.php' );
  remove_meta_box( 'commentsdiv', 'page', 'normal' );
  remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
  remove_meta_box( 'commentsdiv', 'post', 'normal' );
  remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
  remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\disable_menu_and_meta_boxes' );


/**
 * Don't show comments as a column in admin UI
 */
function manage_comment_columns( array $columns ): array {
  unset( $columns['comments'] );
  return $columns;
}
add_filter( 'manage_posts_columns', __NAMESPACE__ . '\\manage_comment_columns', 0, 1 );
add_filter( 'manage_page_posts_columns', __NAMESPACE__ . '\\manage_comment_columns', 0, 1 );
