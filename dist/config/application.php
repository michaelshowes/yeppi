<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENVIRONMENT_TYPE}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;
use function Env\env;

/** @var string Directory containing all of the site's files */
$root_dir = dirname( __DIR__ );

/** @var string Document Root */
$webroot_dir = $root_dir . '/web';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = Dotenv\Dotenv::createUnsafeImmutable( $root_dir );
if ( file_exists( $root_dir . '/.env' ) ) {
  $dotenv->load();
  $dotenv->required( [
    'DB_NAME',
    'DB_USER',
    'DB_PASSWORD',
    'WP_HOME',
    'WP_SITEURL'
    // 'GRAVITY_FORMS_KEY'
  ] );
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
Config::define( 'WP_ENVIRONMENT_TYPE', env( 'WP_ENVIRONMENT_TYPE' ) ?: 'production' );

/**
 * URLs
 */
if ( Config::get( 'WP_ENVIRONMENT_TYPE' ) === 'local' ) {
  Config::define( 'WP_HOME', env( 'WP_HOME' ) );
  Config::define( 'WP_SITEURL', env( 'WP_SITEURL' ) );
}

/**
 * Custom Content Directory
 */
Config::define( 'CONTENT_DIR', '/app' );
Config::define( 'WP_CONTENT_DIR', $webroot_dir . Config::get( 'CONTENT_DIR' ) );
Config::define( 'WP_CONTENT_URL', Config::get( 'WP_HOME' ) . Config::get( 'CONTENT_DIR' ) );

/**
 * DB settings
 */
if ( Config::get( 'WP_ENVIRONMENT_TYPE' ) === 'local' ) {
  Config::define( 'DB_NAME', env( 'DB_NAME') );
  Config::define( 'DB_USER', env( 'DB_USER') );
  Config::define( 'DB_PASSWORD', env( 'DB_PASSWORD') );
  Config::define( 'DB_HOST', env( 'DB_HOST') ?: 'localhost' );
  Config::define( 'DB_CHARSET', 'utf8mb4' );
  Config::define( 'DB_COLLATE', '' );
}

$table_prefix = env( 'DB_PREFIX') ?: 'wp_';

/**
 * Authentication Unique Keys and Salts
 */
if ( Config::get( 'WP_ENVIRONMENT_TYPE' ) === 'local' ) {
  Config::define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
  Config::define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
  Config::define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
  Config::define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
  Config::define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
  Config::define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
  Config::define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
  Config::define( 'NONCE_SALT', env( 'NONCE_SALT' ) );
}

/**
 * Custom Settings
 */
Config::define( 'AUTOMATIC_UPDATER_DISABLED', TRUE );
Config::define( 'DISABLE_WP_CRON', env( 'DISABLE_WP_CRON' ) ?: FALSE );

// Set WP Memory Limit
Config::define('WP_MEMORY_LIMIT', '256M');

set_time_limit(300);

// Disable the plugin and theme file editor in the admin
Config::define( 'DISALLOW_FILE_EDIT', TRUE );

// Limit the number of post revisions that Wordpress stores (TRUE (default WP): store every revision)
Config::define('WP_POST_REVISIONS', env('WP_POST_REVISIONS') ?: TRUE);

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
  $_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . Config::get( 'WP_ENVIRONMENT_TYPE' ) . '.php';

if ( file_exists( $env_config ) ) {
  require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if ( ! defined( 'ABSPATH' ) ) {
  define( 'ABSPATH', $webroot_dir . '/wp/' );
}
