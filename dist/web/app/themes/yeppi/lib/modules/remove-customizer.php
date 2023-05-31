<?php

namespace Yeppi\Modules\RemoveCustomizer;

/**
 * Remove Theme Customizer from WP
 */

class remove_customizer {

  /**
   * @var remove_customizer
   */
  private static $instance;


  /**
   * Main Instance
   *
   * Allows only one instance of remove_customizer in memory.
   *
   * @static
   * @staticvar array $instance
   * @return Big mama, remove_customizer
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! self::$instance instanceof remove_customizer ) {

      // Start your engines!
      self::$instance = new remove_customizer;

      // Load the structures to trigger initially
      add_action( 'init', [ self::$instance, 'init' ], 10 ); // was priority 5
      add_action( 'admin_init', [ self::$instance, 'admin_init' ], 10 ); // was priority 5

    }
    return self::$instance;
  }

  /**
   * Run all plugin stuff on init.
   *
   * @return void
   */
  public function init() {

    // Remove customize capability
    add_filter( 'map_meta_cap', [ self::$instance, 'filter_to_remove_customize_capability' ], 10, 4 );
  }

  /**
   * Run all of our plugin stuff on admin init.
   *
   * @return void
   */
  public function admin_init() {

    // Drop some customizer actions
    remove_action( 'plugins_loaded', '_wp_customize_include', 10 );
    remove_action( 'admin_enqueue_scripts', '_wp_customize_loader_settings', 11 );

    // Manually override Customizer behaviors
    add_action( 'load-customize.php', [ self::$instance, 'override_load_customizer_action' ] );
  }

  /**
   * Remove customize capability
   *
   * This needs to be in public so the admin bar link for 'customize' is hidden.
   */
  public function filter_to_remove_customize_capability( $caps = [], $cap = '', $user_id = 0, $args = [] ) {
    if ( $cap == 'customize' ) {
      return ['nope'];
    }

    return $caps;
  }

  /**
   * Manually overriding specific Customizer behaviors
   */
  public function override_load_customizer_action() {
    // If accessed directly
    wp_die( __( 'The Customizer is currently disabled.', 'yeppi' ) );
  }

} // End Class

/**
* The main function. Use like a global variable, except no need to declare the global.
*
* @return object The one true remove_customizer Instance
*/
function remove_customizer() {
  return remove_customizer::instance();
}

// GO!
remove_customizer();
