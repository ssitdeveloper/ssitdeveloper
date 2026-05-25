<?php

namespace App\Helpers;

/**
 * Override asset() helper to serve assets from /public/ folder on production
 * This is necessary when the document root is not set to /medical/public/
 */
if (!function_exists('asset')) {
    function asset($path = null)
    {
        // For production servers where .htaccess rewrite doesn't work,
        // prepend /public/ to asset paths
        if (strpos($path, 'public/') === false && !empty($path)) {
            $path = 'public/' . $path;
        }

        return app('url')->asset($path);
    }
}
