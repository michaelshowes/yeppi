<?php

namespace Yeppi\Modules\Search;


/**
 * Use Relevanssi for wp_link_query
 */
add_filter( 'wp_link_query_args', function( $args ) {
  $args['relevanssi'] = true;
  return $args;
} );


/**
* Change search results to 20 per page
*/
// function search_results_per_page( $limits ) {
// 	if ( is_search() ) {
// 		global $wp_query;
// 		$wp_query->query_vars['posts_per_page'] = 20;
// 	}
// 	return $limits;
// }
// add_filter( 'post_limits', __NAMESPACE__ . '\\search_results_per_page' );

/**
* Redirects search results from /?s=query to /search/query/, converts %20 to +.
*
* @link http://txfx.net/wordpress-plugins/nice-search/
* @global \WP_Rewrite $wp_rewrite WordPress Rewrite Component.
*/
function redirect(): void {
	global $wp_rewrite;

	$search_base = $wp_rewrite->search_base;
	if ( is_search_permalink( $search_base ) ) {
		wp_safe_redirect( get_search_link() );
		exit();
	}
}
add_action( 'template_redirect', __NAMESPACE__ . '\\redirect' );


/**
* Filter the WP SEO search URL.
*
* @param string $url Search URL passed by WPSEO.
* @return string
*/
function rewrite( string $url ): string {
	return str_replace( '/?s=', '/search/', $url );
}
add_filter( 'wpseo_json_ld_search_url', __NAMESPACE__ . '\\rewrite' );


/**
* Filter the search keywords
*
* @param string $keywords Search keywords
* @return string
*/
function filter_search_query( string $keywords ): string {
  return urldecode( $keywords );
}
add_filter( 'get_search_query', __NAMESPACE__ . '\\filter_search_query' );
add_filter( 'the_search_query', __NAMESPACE__ . '\\filter_search_query' );

function filter_search_query_vars( object $query ): object {
  if ( $query->is_search && ! is_admin() ) {
    $query->query_vars['s'] = urldecode( $query->query_vars['s'] );
  }
  return $query;
}
add_action( 'parse_query', __NAMESPACE__ . '\\filter_search_query_vars' );


/**
* Simple function to check if we're dealing with a search permalink.
*
* @param \WP_Rewrite $search_base Anything to be inserted before searches. Defaults to 'search/'.
* @return bool
*/
function is_search_permalink( string $search_base ): bool {
	return
    is_search() &&
    ! is_admin() &&
    strpos( filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL ), "/{$search_base}/" ) === FALSE &&
    strpos( filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL ), '&' ) === FALSE
  ;
}


/**
 * Add specific ACF fields that include a string in their field name for Relevanssi indexing
 *
 * @param array An array of custom field names.
 * @return array
 */
function filter_settings_fields( array $custom_fields ): array {

  $allowed = [
    'title',
    'text',
    'eyebrow',
    'location',
    'content',
    'description',
    'name',
    'stat',
    'stat_detail',
  ];

  foreach ( $custom_fields as $index => $name ) {
    foreach ( $allowed as $substring ) {
      if (
        str_ends_with( $name, $substring ) !== FALSE
        && $name !== 'components'
        && str_ends_with( $name, 'pdf_content' ) !== TRUE
        && str_ends_with( $name, 'resource_content' ) !== TRUE
      ) {
        continue 2;
      }
    }
    unset( $custom_fields[ $index ] );
  }

  return $custom_fields;

}
add_filter(
  'relevanssi_index_custom_fields',
  __NAMESPACE__ . '\\filter_settings_fields',
  50,
  1
);


/**
 * Modify the excerpt
 *
 * @param string $excerpt_text The excerpt text, not used.
 * @param array  $excerpt      The full excerpt array.
 *
 * @return string The modified excerpt.
 */
function modify_excerpt( string $excerpt_text, array $excerpt ): string {
  return  $excerpt['text'];
}
add_filter( 'relevanssi_excerpt_part', __NAMESPACE__ . '\\modify_excerpt', 10, 2 );
