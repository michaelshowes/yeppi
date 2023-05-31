<?php

namespace Yeppi\Modules\RegisterMenus;

/**
 * Register menus
 */
register_nav_menus( [
  'main_nav' => __( 'Main Navigation', 'yeppi' ),
  'utility_nav' => __( 'Utility Navigation', 'yeppi' ),
  'legal_nav' => __( 'Legal Navigation', 'yeppi' )
] );

/**
 * Add menus to Twig context
 */
function add_menus_to_context( $context ) {
  $context['main_nav'] = new \Timber\Menu( 'main_nav' );
  $context['utility_nav'] = new \Timber\Menu( 'utility_nav' );
  $context['legal_nav'] = new \Timber\Menu( 'legal_nav' );

  return $context;
}
add_filter( 'timber_context', __NAMESPACE__ . '\\add_menus_to_context' );

/**
 * Don't allow Tags and Category taxonomy terms in menus
 */
function no_default_taxonomy_in_menus( array $args, string $taxonomy ) {
  if ( $taxonomy === 'category' || $taxonomy === 'post_tag' ) {
    $args['show_in_nav_menus'] = FALSE;
  }

  return $args;
}
add_filter( 'register_taxonomy_args', __NAMESPACE__ . '\\no_default_taxonomy_in_menus', 10, 2 );

/**
 * For users without menu visibility preference - mainly new users - hide posts
 * and only posts by default.
 */
function nav_menu_default_hidden_meta_boxes( $result ) {
  // Return a default only if result is boolean FALSE, which would indicate
  // user preference has not yet been set
  if ( $result === FALSE ) {
    return [ 'add-post-type-post' ];
  }

  return $result;
}
add_filter( 'get_user_option_metaboxhidden_nav-menus', __NAMESPACE__ . '\\nav_menu_default_hidden_meta_boxes', 100, 1 );


/**
 * Add new location rule for menu items of a particular depth
 */
class ACF_Location_Menu_Depth extends \ACF_Location {

  /**
   * Initializes props.
   *
   * @param	void
   * @return void
   */
  public function initialize() {
    $this->name           = 'menu_depth';
    $this->label          = __( 'Menu Depth', 'yeppi' );
    $this->category       = 'forms';
    $this->object_type    = 'post';
    $this->object_subtype = 'page';
  }

  /**
   * Matches the provided rule against the screen args returning a bool result.
   *
   * @param	array $rule The location rule.
   * @param	array $screen The screen args.
   * @param	array $field_group The field group settings.
   * @return bool
   */
  public function match( $rule, $screen, $field_group ): bool {

    // Check screen args and get depth.
    if ( ! isset( $screen['nav_menu_item_depth'] ) ) {
      return FALSE;
    } else {
      $depth = $screen['nav_menu_item_depth'];
    }

    // Compare.
    switch ( $rule['value'] ) {
      case 'top_level':
        $result = ( $depth === 0 );
        break;

      case 'second_level':
        $result = ( $depth === 1 );
        break;
    }

    // Reverse result for "!=" operator.
    if ( $rule['operator'] === '!=' ) {
      return ! $result;
    }

    return $result;
  }

  /**
   * Returns an array of possible values for this rule type.
   *
   * @param	array $rule A location rule.
   * @return array
   */
  public function get_values( $rule ): array {

    return [
      'top_level'    => __( 'Top Level', 'yeppi' ),
      'second_level' => __( 'Second Level', 'yeppi' )
    ];
  }
}

function custom_acf_location_rules() {
  acf_register_location_type( __NAMESPACE__ . '\\ACF_Location_Menu_Depth' );
}
add_action('acf/init', __NAMESPACE__ . '\\custom_acf_location_rules');
