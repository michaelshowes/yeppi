<?php

namespace Yeppi\Modules\Timber;

use Timber;
use Timber\Twig_Function;
use Timber\Twig_Filter;
use Yeppi\Modules\Utils;

/**
 * Additional twig context to use globally
 */
function add_to_context( array $context ): array {
  // general sugar
  $context['WP_ENVIRONMENT_TYPE'] = WP_ENVIRONMENT_TYPE;
  $context['site']                = new Timber\Site;
  $context['site_url']            = get_site_url();
  $context['assets']              = Utils\root_relative_url( get_stylesheet_directory_uri() . '/assets' );

  $options                        = get_fields( 'options' );
  $context['options']             = $options;
  $context['page_template']       = basename( get_page_template() );

  $context['is_home']             = is_front_page();
  $context['is_posts_page']       = is_home();

  $context['archive_pages']       = Utils\get_archive_page_details();

  // Assigns Amelia REST API results to twig global variables
  $amelia = array(
    'services',
    'categories',
    'staff'
  );

  foreach ($amelia as $val) {
    $response = wp_remote_get(
      "https://yeppinail.com/wp-json/amelia/v1/{$val}",
      array('sslverify' => FALSE)
    );
    if ( is_wp_error( $response ) ) {
      return false;
    }
    $body = wp_remote_retrieve_body( $response );
    $json = json_decode( $body );

    $context["amelia_{$val}"] = $json;
  }

  // // social media info
  // if ( ! empty( $options ) ) {
  //   $social_field_values    = $options['social_media'];
  //   $social_field_object    = get_field_object( 'social_media', 'options' );
  //   $social_field_subfields = $social_field_object['sub_fields'];
  //   $social                 = [];
  //   if ( ! empty( $social_field_values ) ) {
  //     foreach ( $social_field_values as $key => $value ) {
  //       if ( ! empty( $value ) ) {
  //         $numerical_index = array_search( $key, array_keys( $social_field_values ) );

  //         $social[ $key ] = [
  //           'name' => $social_field_subfields[ $numerical_index ]['label'],
  //           'url'  => $value
  //         ];
  //       }
  //     }
  //   }
  //   $context['social'] = $social;
  // }

  return $context;
}
add_filter( 'timber_context', __NAMESPACE__ . '\\add_to_context' );

/**
 * Automatically build a template path based off an ACF layout name, and render
 * the template if found.
 */
function render_component( array $component_data ): void {
  global $timber;

  if ( empty( $component_data['acf_fc_layout'] ) ) {
    trigger_error(
      'No ACF layout name',
      E_USER_WARNING
    );
    echo '<!-- NO TEMPLATE: UNKNOWN COMPONENT NAME -->';
    return;
  }

  $component_backend_name = $component_data['acf_fc_layout'];
  $component_name = str_replace( '_', '-', $component_backend_name );
  $component_name = apply_filters( 'yeppi_component_name', $component_name );

  $template_path = 'components' . DIRECTORY_SEPARATOR . $component_name . DIRECTORY_SEPARATOR . $component_name . '.twig';

  $theme_dir = get_template_directory();
  $dirnames = Timber::$dirname;
  if ( ! is_array( $dirnames ) ) {
    $dirnames = [ $dirnames ];
  }

  $template_exists = FALSE;
  foreach ( $dirnames as $root_path ) {
    // NOTE: assumes relative paths in Timber::$dirname
    $test_path = $theme_dir . DIRECTORY_SEPARATOR . $root_path .
      DIRECTORY_SEPARATOR . $template_path;

    if ( file_exists( $test_path ) ) {
      $template_exists = TRUE;
      break;
    }
  }

  if ( $template_exists ) {
    $context = Timber::context();
    $context['component'] = $component_data;
    $context = apply_filters( 'yeppi_component_context', $context, $component_backend_name );
    $context = apply_filters( 'yeppi_component_context/' . $component_backend_name, $context );

    $timber->render( $template_path, $context );
  } else {
    trigger_error(
      'Could not find template for component ' . $component_name,
      E_USER_WARNING
    );
    echo '<!-- NO TEMPLATE ' . $component_name . '.twig -->';
  }
}


/**
 * Set placeholder image color for resource cards
 */
function set_placeholder_color( string $previous_color ): string {

  switch ( $previous_color ) {
    case 'orange':
      $color = 'blue';
      break;
    case 'blue':
      $color = 'blue-secondary';
      break;
    case 'blue-secondary':
      $color = 'red';
      break;
    case 'red':
      $color = 'orange';
      break;
  }

  return $color;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array(
  'src',
  'src/pages',
  'src/components'
);

/**
 * Add filters and extentions to Twig.
 */
function add_to_twig( \Twig\Environment $twig ): \Twig\Environment {

  $twig->addExtension( new \Twig_Extension_StringLoader() );

  // Return file name without extension
  $twig->addFilter( new Twig_Filter( 'remove_extension', function( string $string ): string {
    return pathinfo( $string, PATHINFO_FILENAME );
  } ) );

  // $twig->addGlobal('amelia', $data);

  // Get Yoast Primary Term
  $twig->addFunction( new Twig_Function( 'get_primary_taxonomy_term', 'yeppi\\Modules\\Utils\\get_primary_taxonomy_term' ) );

  // Add custom trunc filter
  $twig->addFilter( new Twig_Filter( 'trim_characters', 'yeppi\\Modules\\Utils\\trim_characters' ) );

  // Add event date function
  $twig->addFunction( new Twig_Function( 'event_date', 'yeppi\\Modules\\Utils\\event_date' ) );

  // Render a component based on its ACF Layout name
  $twig->addFunction( new Twig_Function( 'render_component', __NAMESPACE__ . '\\render_component' ) );

  // Set placeholder color for resource cards
  $twig->addFunction( new Twig_Function( 'set_placeholder_color', __NAMESPACE__ . '\\set_placeholder_color' ) );

  // Return clean CSS class/id string
  $twig->addFilter( new Twig_Filter( 'clean_css_identifier', 'yeppi\Modules\Utils\\clean_css_identifier' ) );

  // Expose the Kint dev plugin's d() function, if present
  if ( function_exists( 'd' ) ) {
    $twig->addFunction( new Twig_Function( 'd', 'd' ) );
  }

  return $twig;
}
add_filter( 'get_twig', __NAMESPACE__ . '\\add_to_twig' );
