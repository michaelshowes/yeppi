<?php

namespace Yeppi\Modules\Resources;

/**
 * Change Posts to Resources for Clarity
 */
function change_post_label() {
  global $menu;
  global $submenu;
  $menu[5][0] = 'Resources';
  $submenu['edit.php'][5][0] = 'Resources';
  $submenu['edit.php'][10][0] = 'Add New Resource';

  remove_meta_box( 'categorydiv' , 'post' , 'side' );
}
add_action( 'admin_menu', __NAMESPACE__ . '\\change_post_label' );

function change_post_object() {
  global $wp_post_types;
  $labels = &$wp_post_types['post']->labels;
  $labels->name = 'Resources';
  $labels->singular_name = 'Resource';
  $labels->add_new = 'Add Resource';
  $labels->add_new_item = 'Add New Resource';
  $labels->edit_item = 'Edit Resource';
  $labels->new_item = 'Resource';
  $labels->view_item = 'View Resource';
  $labels->search_items = 'Search Resources';
  $labels->not_found = 'No Resources found';
  $labels->not_found_in_trash = 'No Resources found in Trash';
  $labels->all_items = 'All Resources';
  $labels->menu_name = 'Resources';
  $labels->name_admin_bar = 'Resources';
}
add_action( 'init', __NAMESPACE__ . '\\change_post_object' );


/**
 * Get rid of default taxonomies on posts.
 */
function unregister_taxonomies() {
  global $wp_taxonomies;
  unregister_taxonomy_for_object_type( 'post_tag', 'post' );
  if ( taxonomy_exists( 'post_tag'))
    unset( $wp_taxonomies['post_tag']);
  unregister_taxonomy( 'post_tag' );
}
add_action( 'init', __NAMESPACE__ . '\\unregister_taxonomies' );


/**
 * Rename "Uncategorized" to "Insights" on initial install
 */
function rename_default_category () {
  wp_update_term(1, 'category', array(
    'name' => __('Insights', 'yeppi'),
    'slug' => __('insights', 'yeppi'),
    'description' => ''
  ));
}
add_action('after_switch_theme', __NAMESPACE__ . '\\rename_default_category');

/**
 * Remove the 'description' column from the admin table
 */
function remove_description_column( array $columns ): array {
  unset( $columns['description'] );
  return $columns;
}
add_filter( 'manage_edit-category_columns', __NAMESPACE__ . '\\remove_description_column', 50, 1 );
