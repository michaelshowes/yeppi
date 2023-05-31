<?php

/**
 * Theme initialization
 */
class Yeppi {

  /**
   * Includes Timber and custom PHP files.
   */
  public function __construct() {
    global $timber;

    // Add util functions
    include_once(dirname(__FILE__) . '/lib/utils.php');

    // Initialize Timber.
    $timber = new Timber\Timber();

    // Sets the directories (inside your theme) to find .twig files
    Timber::$dirname = [
      'src',
      'src/page',
      'src/components'
    ];

    // By default, Timber does NOT autoescape values. This enables it.
    Timber::$autoescape = FALSE;

    // Include HTML comments showing start and end of Twig templates, if package
    // is available.
    if (function_exists('\Djboris88\Timber\initialize_filters')) {
      \Djboris88\Timber\initialize_filters();
    }

    /** Register custom post types */
    // $post_type_includes = $this->get_php_files( dirname( __FILE__ ) . '/lib/post-types' );
    // foreach ( $post_type_includes as $post_type_include ) {
    //   require_once $post_type_include;
    // }

    /** Register custom taxonomies */
    // $taxonomy_includes = $this->get_php_files( dirname( __FILE__ ) . '/lib/taxonomies' );
    // foreach ( $taxonomy_includes as $taxonomy_include ) {
    //   require_once $taxonomy_include;
    // }

    add_action( 'after_setup_theme', [ $this, 'theme_setup' ], 10, 0 );
	}

  /** Custom theme setup */
  public function theme_setup() {

    /** Add module includes */
    $module_includes = $this->get_php_files(dirname(__FILE__) . '/lib/modules');
    foreach ( $module_includes as $module_include ) {
      require_once $module_include;
    }

    /** Add component includes */
    $component_includes = $this->get_php_files( dirname( __FILE__ ) . '/src/components' );
    foreach ( $component_includes as $component_include ) {
      require_once $component_include;
    }
  }

  /**
   * Get all PHP files lying in a directory or anywhere under its subdirectory
   * tree
   *
   * @param string $path
   *
   * @return \RecursiveIteratorIterator
   */
  public function get_php_files( string $path ): \RecursiveIteratorIterator {
    $php_files = new \RecursiveCallbackFilterIterator(
      new \RecursiveDirectoryIterator( $path ),
      function ( $current, $key, $iterator ) {
        // Allow recursion
        if ( $iterator->hasChildren() ) {
          return TRUE;
        }

        // Check for PHP file
        if ( $current->isFile() && $current->getExtension() === 'php' ) {
          return TRUE;
        }

        return FALSE;
      }
    );

    return new \RecursiveIteratorIterator( $php_files );
  }

}

new Yeppi();
