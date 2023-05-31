<?php

namespace Yeppi\Modules\Login;

/** Add login wall if enabled on an environment */
function login_wall() {
  if ( ( $_ENV['WP_LOGIN_WALL'] === 'TRUE' || $_ENV['WP_LOGIN_WALL'] === 'true' ) && ! is_user_logged_in() ) {
    auth_redirect();
  }
}
add_action( 'template_redirect', __NAMESPACE__ . '\\login_wall' );

/**
 * Customize the login css
 *
 * @wp-hook login_enqueue_scripts
 */
function customize_login() { ?>
  <style type="text/css">
    body.login div#login h1 a {
      pointer-events: none;
      position: relative;
      width: 245px;
      height: 70px;
      background-image: url('/app/themes/yeppi/assets/images/yeppi-logo.svg');
      background-size: cover;
    }
  </style>
<?php }
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\customize_login' );
