<?php

namespace Yeppi\Modules\UserRoles;
use Yeppi\Modules\Utils;

function yeppi_user_role() {
  global $wp_roles;

  // Rename Administrator role
  $wp_roles->roles['administrator']['name'] = 'Yeppi Nail Admin';
  $wp_roles->role_names['administrator'] = 'Yeppi Nail Admin';

  // TEMP: delete after next deployment
  remove_role( 'client_admin' );

  // Create Client Admin role if not created
  if ( get_option( 'yeppi_admin_role_created' ) === FALSE ) {
    add_role( 'yeppi_admin', 'yeppi Admin', [

      // core
      'read'                   => TRUE,
      'edit_theme_options'     => TRUE,
      'create_users'           => TRUE,
      'delete_users'           => TRUE,
      'edit_users'             => TRUE,
      'list_users'             => TRUE,
      'promote_users'          => TRUE,
      'remove_users'           => TRUE,
      'manage_categories'      => TRUE,
      'edit_others_posts'      => TRUE,
      'edit_pages'             => TRUE,
      'edit_others_pages'      => TRUE,
      'edit_published_pages'   => TRUE,
      'publish_pages'          => TRUE,
      'delete_pages'           => TRUE,
      'delete_others_pages'    => TRUE,
      'delete_published_pages' => TRUE,
      'delete_others_posts'    => TRUE,
      'delete_private_posts'   => TRUE,
      'edit_private_posts'     => TRUE,
      'read_private_posts'     => TRUE,
      'delete_private_pages'   => TRUE,
      'edit_private_pages'     => TRUE,
      'read_private_pages'     => TRUE,
      'edit_published_posts'   => TRUE,
      'upload_files'           => TRUE,
      'publish_posts'          => TRUE,
      'delete_published_posts' => TRUE,
      'edit_posts'             => TRUE,
      'delete_posts'           => TRUE,
      'unfiltered_html'        => TRUE,

      // gravity forms
      'gravityforms_view_entries'     => TRUE,
      'gravityforms_edit_entries'     => TRUE,
      'gravityforms_delete_entries'   => TRUE,
      'gravityforms_export_entries'   => TRUE,
      'gravityforms_view_entry_notes' => TRUE,
      'gravityforms_edit_entry_notes' => TRUE,

      // yoast
      'wpseo_manage_options'   => TRUE,
      'wpseo_manage_redirects' => TRUE,
    ] );
    update_option( 'yeppi_admin_role_created', TRUE );
  }
}
add_action( 'admin_init', __NAMESPACE__ . '\\yeppi_user_role' );

function disable_menus_for_yeppi(): void {
  $role = Utils\get_user_role();

  if ( $role !== 'administrator' ) {
    remove_menu_page( 'edit.php?post_type=acf-field-group' );
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'themes.php' );
    add_menu_page( 'Menus', 'Menus', 'create_users', 'nav-menus.php', '', 'dashicons-menu', 60 );
    add_menu_page( 'Redirects', 'Redirects', 'edit_posts', 'tools.php?page=redirection.php', '', 'dashicons-redo', 60 );
    remove_submenu_page( 'gf_edit_forms','gf_help' );
  }
}
add_action( 'admin_menu', __NAMESPACE__ . '\\disable_menus_for_yeppi', 9999 );


add_filter( 'redirection_role', function( $role ) {
  return 'edit_posts';
} );
