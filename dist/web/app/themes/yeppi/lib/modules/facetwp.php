<?php

namespace Yeppi\Modules\FacetWP;

use Timber;

// Enable FacetWP's built-in a11y supprt
add_filter( 'facetwp_load_a11y', '__return_true' );

// Hide counts in dropdown facets
add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

/*
 * Don't load FacetWP css
 */
add_filter( 'facetwp_assets', function( array $assets ): array {
  unset( $assets['front.css'] );
  return $assets;
});

/*
 * Show upcoming events by default
 */
add_filter( 'facetwp_preload_url_vars', function( array $url_vars ): array {
  if ( 'events' === FWP()->helper->get_uri() && empty( $url_vars['event_type'] ) ) {
    $url_vars['event_type'] = [ 'upcoming' ];
  }
  return $url_vars;
} );

function register_facets( array $facets ): array {

  // Reset button
  $facets[] = [
    'name'         => 'reset',
    'label'        => 'Reset',
    'type'         => 'reset',
    'reset_ui'     => 'button',
    'reset_text'   => __('Clear all', 'yeppi'),
    'reset_mode'   => 'off',
    'auto_hide'    => 'yes',
    'reset_facets' => []
  ];

  // Search
  $facets[] = [
    'name'          => 'search',
    'label'         => 'Search',
    'type'          => 'search',
    'source'        => '',
    'source_other'  => '',
    'search_engine' => 'relevanssi',
    'placeholder'   => __('Search...', 'yeppi'),
    'auto_refresh'  => 'no',
  ];

  // Results count
  $facets[] = [
    'name'                => 'results_count',
    'label'               => 'Results Count',
    'type'                => 'pager',
    'source'              => '',
    'pager_type'          => 'counts',
    'inner_size'          => '2',
    'dots_label'          => '\u2026',
    'prev_label'          => '\u00ab Prev',
    'next_label'          => 'Next \u00bb',
    'count_text_plural'   => 'Showing [lower]&ndash;[upper] of [total] results',
    'count_text_singular' => '1 result',
    'count_text_none'     => 'No results',
    'load_more_text'      => 'Load more',
    'loading_text'        => 'Loading...',
    'per_page_options'    => '10, 25, 50, 100'
  ];

  // Event Type
  $facets[] = [
    'name'   => 'event_type',
    'label'  => 'Event Type',
    'type'   => 'event_date',
    'source' => 'acf/field_62448883398cf/field_6244897450b8a'
  ];

  // Sort Events
  $facets[] = [
    'name'          => 'sort_events',
    'label'         => 'Sort Events',
    'type'          => 'sort',
    'default_label' => __('Sort by', 'yeppi'),
    'sort_options'  => [
      0 => [
        'label'   => __('Upcoming', 'yeppi'),
        'name'    => 'upcoming',
        'orderby' => [
          0 => [
            'key'   => 'cf/event_start_date',
            'order' => 'ASC',
            'type'  => 'NUMERIC',
          ],
        ],
      ],
      1 => [
        'label'   => __('Past', 'yeppi'),
        'name'    => 'past',
        'orderby' => [
          0 => [
            'key'   => 'cf/event_start_date',
            'order' => 'DESC',
            'type'  => 'NUMERIC',
          ],
        ],
      ],
    ],
  ];

  // Categories
  $facets[] = [
    'name'            => 'categories',
    'label'           => 'Categories',
    'type'            => 'checkboxes',
    'source'          => 'tax/category',
    'hierarchical'    => 'yes',
    'show_expanded'   => 'yes',
    'ghosts'          => 'yes',
    'preserve_ghosts' => 'yes',
    'operator'        => 'or',
    'orderby'         => 'term_order',
    'count'           => '-1',
    'soft_limit'      => '-1',
  ];

  // Event Categories
  $facets[] = [
    'name'            => 'event_categories',
    'label'           => 'Categories',
    'type'            => 'checkboxes',
    'source'          => 'tax/event-category',
    'hierarchical'    => 'yes',
    'show_expanded'   => 'yes',
    'ghosts'          => 'yes',
    'preserve_ghosts' => 'yes',
    'operator'        => 'or',
    'orderby'         => 'term_order',
    'count'           => '-1',
    'soft_limit'      => '-1',
  ];

  // Topics
  $facets[] = [
    'name'            => 'topics',
    'label'           => 'Topics',
    'type'            => 'checkboxes',
    'source'          => 'tax/topic',
    'ghosts'          => 'yes',
    'preserve_ghosts' => 'yes',
    'operator'        => 'or',
    'orderby'         => 'term_order',
    'count'           => '-1',
    'soft_limit'      => '-1',
  ];

  // Disease Focus Areas
  $facets[] = [
    'name'            => 'focus_areas',
    'label'           => 'Disease Focus Areas',
    'type'            => 'checkboxes',
    'source'          => 'tax/focus-area',
    'ghosts'          => 'yes',
    'preserve_ghosts' => 'yes',
    'operator'        => 'or',
    'orderby'         => 'term_order',
    'count'           => '-1',
    'soft_limit'      => '-1',
  ];

  // Formats
  $facets[] = [
    'name'            => 'formats',
    'label'           => 'Formats',
    'type'            => 'checkboxes',
    'source'          => 'tax/format',
    'ghosts'          => 'yes',
    'preserve_ghosts' => 'yes',
    'operator'        => 'or',
    'orderby'         => 'term_order',
    'count'           => '-1',
    'soft_limit'      => '-1',
  ];

  // Series
  $facets[] = [
    'name'            => 'series',
    'label'           => 'Series',
    'type'            => 'checkboxes',
    'source'          => 'tax/series',
    'ghosts'          => 'yes',
    'preserve_ghosts' => 'yes',
    'operator'        => 'or',
    'orderby'         => 'term_order',
    'count'           => '-1',
    'soft_limit'      => '-1',
  ];

  return $facets;
}
add_filter( 'facetwp_facets', __NAMESPACE__ . '\\register_facets' );

/*
 * Remove () in counts
 */
add_filter( 'facetwp_facet_html', function( string $html, array $params ): string {
  $html = preg_replace( '/\(([0-9]+)\)/', '$1', $html );
  return $html;
}, 10, 2);


/*
 * Custom pagination
 * Uses the same pagination component as everything else for standardized markup/styling.
 */
function pager_item( int $page_num, int $current_page, string $classes = '' ): array {
  $is_current = $page_num == $current_page;

  $item = [
    'title' => $page_num,
    'text' => $page_num,
    'name' => $page_num,
    'current' => $is_current,
  ];

  if ( $classes ) {
    $item['class'] = $classes;
  } else {
    $item['class'] = 'page-number page-numbers' . ( $is_current ? ' current' : '' );
  }

  if ( ! $is_current ) {
    $item['data_page'] = $page_num;
  }

  return $item;
}

function pager_dots(): array {
  return [
    'class' => 'dots',
    'title' => '…',
  ];
}

function facetwp_pager_html( string $output, array $params ): string {
  $mid_size = 2; // Controls maximum number of pages to one side of the current page

  $page = $params['page'];
  $total_pages = $params['total_pages'];

  $context = Timber::context();

  // Construct a pagination array equivalent to Timber\Pagination for Twig's benefit
  $context['pagination'] = [
    'current' => $page,
    'total' => $total_pages,
    'pages' => [],
    'next' => '',
    'prev' => '',
  ];

  if ( $total_pages > 1 ) {
    $mid_numbers = range(
      max( 1, $page - $mid_size ),
      min( $total_pages, $page + $mid_size )
    );

    // Begin pages with the first page, if it wouldn't be naturally included
    if ( $page > $mid_size + 1 ) {
      $context['pagination']['pages'][] = pager_item( 1, $page );
    }

    // Follow with ellipses if there is a gap
    if ( $page > $mid_size + 2 ) {
      $context['pagination']['pages'][] = pager_dots();
    }

    foreach ( $mid_numbers as $a_mid_number ) {
      $is_current = $a_mid_number == $page;
      $context['pagination']['pages'][] = pager_item( $a_mid_number, $page );
    }

    // Lead with ellipses if there is a gap
    if ( $page + $mid_size + 1 < $total_pages ) {
      $context['pagination']['pages'][] = pager_dots();
    }

    // Always show last page
    if ( $page + $mid_size < $total_pages ) {
      $context['pagination']['pages'][] = pager_item( $total_pages, $page );
    }

    // Previous/Next
    if ( $total_pages > 1 ) {
      if ( $page < $total_pages ) {
        $context['pagination']['next'] = [
          'classes' => 'page-numbers next',
          'data_page' => $page + 1,
        ];
      } else {
        $context['pagination']['next'] = '';
      }

      if ( $page > 1 ) {
        $context['pagination']['prev'] = [
          'classes' => 'page-numbers prev',
          'data_page' => $page - 1,
        ];
      } else {
        $context['pagination']['prev'] = '';
      }
    }
  }

  $output = Timber::compile( 'src/components/pagination/pagination.twig', $context );

  return $output;
}
add_filter( 'facetwp_pager_html', __NAMESPACE__ . '\\facetwp_pager_html', 10, 2 );
