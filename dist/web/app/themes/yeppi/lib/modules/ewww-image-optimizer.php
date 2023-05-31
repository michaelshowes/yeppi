<?php

namespace Yeppi\Modules\EWWW;
use Roots\WPConfig\Config;

// See https://docs.ewww.io/article/40-override-options for all override options.
Config::define( 'EWWW_IMAGE_OPTIMIZER_NOAUTO', FALSE );
Config::define( 'EWWW_IMAGE_OPTIMIZER_METADATA_REMOVE', TRUE );

Config::define( 'EWWW_IMAGE_OPTIMIZER_JPG_LEVEL', 10 );
Config::define( 'EWWW_IMAGE_OPTIMIZER_PNG_LEVEL', 10 );
Config::define( 'EWWW_IMAGE_OPTIMIZER_GIF_LEVEL', 10 );

Config::define( 'EWWW_IMAGE_OPTIMIZER_JPG_QUALITY', 90 );

Config::define( 'EWWW_IMAGE_OPTIMIZER_PDF_LEVEL', 0 );

Config::define( 'EWWW_IMAGE_OPTIMIZER_PARALLEL_OPTIMIZATION', TRUE );
Config::define( 'EWWW_IMAGE_OPTIMIZER_AUTO', TRUE );
Config::define( 'EWWW_IMAGE_OPTIMIZER_INCLUDE_MEDIA_PATHS', TRUE );

Config::define( 'EWWW_IMAGE_OPTIMIZER_DISABLE_CONVERT_LINKS', TRUE );
Config::define( 'EWWW_IMAGE_OPTIMIZER_ENABLE_HELP', FALSE );
Config::define( 'EWWW_IMAGE_OPTIMIZER_ALLOW_TRACKING', FALSE );
Config::define( 'EWWW_IMAGE_OPTIMIZER_GIF_TO_PNG', TRUE );

Config::define( 'EWWW_IMAGE_OPTIMIZER_WEBP', FALSE );
