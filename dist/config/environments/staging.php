<?php
/**
 * Configuration overrides for WP_ENVIRONMENT_TYPE === 'staging'
 */

use Roots\WPConfig\Config;

Config::define( 'SAVEQUERIES', TRUE );
Config::define( 'WP_DEBUG', FALSE );
Config::define( 'WP_DEBUG_DISPLAY', FALSE );
Config::define( 'SCRIPT_DEBUG', FALSE );

Config::define( 'WP_CACHE', TRUE );

ini_set( 'display_errors', 0 );

// Disable plugin and theme updates and installation from the admin
Config::define( 'DISALLOW_FILE_MODS', TRUE );
