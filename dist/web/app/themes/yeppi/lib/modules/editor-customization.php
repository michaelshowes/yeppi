<?php

namespace Yeppi\Modules\EditorCustomization;

/**
 * Customize WP Editor
 *
 */


/**
 * Registers an editor stylesheet for the theme.
 */
function custom_editor_css() {
  add_editor_style(
    get_stylesheet_directory_uri() .
    '/assets/css/style.css?ver='.
    filemtime( get_template_directory() . '/assets/css/style.css' )
  );
}
add_action( 'admin_init', __NAMESPACE__ . '\\custom_editor_css' );


/**
 * Add "Styles" drop-down
 */
function style_select_button( $buttons ) {
  array_unshift( $buttons, 'styleselect' );
  return $buttons;
}
add_filter( 'mce_buttons', __NAMESPACE__ . '\\style_select_button' );


/**
 * Modify MCE
 */
function mce_before_init( $settings ) {

  global $tinymce_version;

  // Add block formats
  $settings['formats'] = json_encode( [
    'p-large' => [
      'selector' => 'p',
      'block'    => 'p',
      'classes'  => '-large',
    ],
  ], JSON_THROW_ON_ERROR );

  $block_formats = [
    'Paragraph=p',
    'Large Paragraph=p-large',
    'Heading 2=h2',
    'Heading 3=h3',
    'Heading 4=h4',
    'Heading 5=h5',
    'Heading 5=h5',
  ];

  $settings['block_formats'] = implode( ';', $block_formats );

  // Add style formats
  $settings['style_formats'] = json_encode( [
    [
      'title' => 'Buttons',
      'items' => [
        [
          'title' => 'Button',
          'selector' => 'a',
          'classes' => 'button-blue'
        ],
        // [
        //   'title' => 'Button (Secondary)',
        //   'selector' => 'a',
        //   'classes' => 'button -secondary'
        // ],
      ]
    ],
  ] );

  // Control paste behavior
  $settings['paste_as_text']                 = TRUE;
  $settings['paste_block_drop']              = TRUE;
  $settings['paste_retain_style_properties'] = '';


  return $settings;
}
add_filter( 'tiny_mce_before_init', __NAMESPACE__ . '\\mce_before_init' );


function custom_wysiwyg_toolbars( $toolbars ) {

  if ( ( $key = array_search( 'alignleft' , $toolbars['Full'][1] ) ) !== FALSE ) {
    unset( $toolbars['Full'][1][ $key ] );
  }

  if ( ( $key = array_search( 'aligncenter' , $toolbars['Full'][1] ) ) !== FALSE ) {
    unset( $toolbars['Full'][1][ $key ] );
  }

  if ( ( $key = array_search( 'alignright' , $toolbars['Full'][1] ) ) !== FALSE ) {
    unset( $toolbars['Full'][1][ $key ] );
  }

  if ( ( $key = array_search( 'wp_more' , $toolbars['Full'][1] ) ) !== FALSE ) {
    unset( $toolbars['Full'][1][ $key ] );
  }

  if ( ( $key = array_search( 'wp_adv' , $toolbars['Full'][1] ) ) !== FALSE ) {
    unset( $toolbars['Full'][1][ $key ] );
  }

  if ( ( $key = array_search( 'fullscreen' , $toolbars['Full'][1] ) ) !== FALSE ) {
    unset( $toolbars['Full'][1][ $key ] );
  }

  if ( ( $key = array_search( 'forecolor' , $toolbars['Full'][2] ) ) !== FALSE ) {
    unset( $toolbars['Full'][2][ $key ] );
  }

  if ( ( $key = array_search( 'wp_help' , $toolbars['Full'][2] ) ) !== FALSE ) {
    unset( $toolbars['Full'][2][ $key ] );
  }

  $toolbars['Basic Text Styles'] = [
    1 => [
      'bold',
      'italic',
      'underline',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Basic Text Styles & Lists'] = [
    1 => [
      'bold',
      'italic',
      'underline',
      'bullist',
      'numlist',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'indent',
      'outdent',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Basic Text Styles & Links'] = [
    1 => [
      'bold',
      'italic',
      'underline',
      'link',
      'unlink',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Basic Text Styles, Links & Lists'] = [
    1 => [
      'bold',
      'italic',
      'underline',
      'link',
      'unlink',
      'bullist',
      'numlist',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'indent',
      'outdent',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Everything but headers'] = [
    1 => [
      'styleselect',
      'bold',
      'italic',
      'underline',
      'link',
      'unlink',
      'bullist',
      'numlist',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'indent',
      'outdent',
      'undo',
      'redo',
    ],
  ];

  $toolbars['Only Links'] = [
    1 => [
      'link',
      'unlink',
      'superscript',
      'subscript',
      'charmap',
      'pastetext',
      'removeformat',
      'undo',
      'redo',
    ],
  ];

  return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars' , __NAMESPACE__ . '\\custom_wysiwyg_toolbars'  );


/*
 * Remove Text Editor from TinyMCE
 */
function my_editor_settings( $settings ) {
  $settings['quicktags'] = FALSE;
  return $settings;
}
add_filter( 'wp_editor_settings', __NAMESPACE__ . '\\my_editor_settings' );


/**
 * Alter buttons
 */
function tinymce_buttons_remove_first_row( $buttons ) {

  // Moves buttons from the second row of the tiny mce editor to the top
  $buttons[] = 'strikethrough, superscript, subscript, charmap, hr, indent, outdent, pastetext, removeformat, redo, undo';

  // Add underline
  array_splice( $buttons, 4, 0, 'underline' );

  // Removes buttons from the first row of the tiny mce editor
  $remove = [ 'alignleft', 'aligncenter', 'alignright', 'wp_more', 'wp_adv', 'fullscreen' ];
  return array_diff( $buttons, $remove );

}
add_filter( 'mce_buttons', __NAMESPACE__ . '\\tinymce_buttons_remove_first_row' );


/**
 * Removes buttons from the second row of the tiny mce editor
 */
function tinymce_buttons_remove_second_row( $buttons ) {
  $remove = [ 'styleselect', 'forecolor', 'wp_help', 'strikethrough', 'hr', 'pastetext', 'removeformat', 'charmap', 'indent', 'outdent', 'redo', 'undo' ];
  return array_diff( $buttons, $remove );
}
add_filter( 'mce_buttons_2', __NAMESPACE__ . '\\tinymce_buttons_remove_second_row' );
