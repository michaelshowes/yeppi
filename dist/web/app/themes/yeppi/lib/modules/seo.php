<?php

namespace Yeppi\Modules\SEO;

// Customization for the Yoast WordPress SEO plugin

/**
 * Move WordPress SEO Metabox to Bottom
 */
add_filter( 'wpseo_metabox_prio', function() { return 'low'; } );
