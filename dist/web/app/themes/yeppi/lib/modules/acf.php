<?php

namespace Yeppi\Modules\Acf;
use Roots\WPConfig\Config;

/** Hide ACF field group menu item if not in development */
if ( Config::get( 'WP_ENVIRONMENT_TYPE' ) !== 'local' && Config::get( 'WP_ENVIRONMENT_TYPE' ) !== 'development' ) {
  add_filter( 'acf/settings/show_admin', '__return_false' );
}

/**
 * Add Global Options Page
 */
acf_add_options_page( [
  'page_title' 	=> 'Global Options',
  'menu_title'	=> 'Global Options',
  'menu_slug' 	=> 'global-options',
  'capability'	=> 'edit_users'
] );


/**
 * Hide drafts from relationship and post object fields
 */
function filter_drafts_from_reference_fields( $args, $field, $post_id ): array {
  $args['post_status'] = ['publish'];
  return $args;
}
add_filter( 'acf/fields/relationship/query', __NAMESPACE__ . '\\filter_drafts_from_reference_fields', 10, 3 );
add_filter( 'acf/fields/post_object/query', __NAMESPACE__ . '\\filter_drafts_from_reference_fields', 10, 3 );


/**
 * Prevent Email Subscribe except in Global Options field
 */
// function validate_forms_field( $valid, $value, $field, $input_name ) {
//
//   // Bail early if value is already invalid.
//   if ( $valid !== TRUE || $field['key'] === 'field_60ae86f2cd98f' ) {
//     return $valid;
//   }
//
//   $email_subscribe_form = get_field('email_subscribe', 'option')['form'];
//
//   if ( is_string( $value ) ) {
//     $intValue = intval( $value );
//     if ( $intValue === $email_subscribe_form ) {
//       return __( 'Please select a form other than Email Subscribe', 'yeppi' );
//     }
//   }
//
//   return $valid;
// }
// add_action('acf/validate_value/type=forms',  __NAMESPACE__ . '\\validate_forms_field', 10, 4 );
