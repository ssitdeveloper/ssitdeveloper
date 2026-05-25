<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RewriteAssetUrls
{
    /**
     * Rewrite asset URLs to include /public/ path for production servers
     * where document root is not set to /public/
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only process HTML responses
        if (str_contains($response->headers->get('content-type', ''), 'text/html')) {
            $content = $response->getContent();

            // Rewrite asset URLs from /medical/css/ to /medical/public/css/
            // and from /medical/js/ to /medical/public/js/, etc.
            $base_path = rtrim(config('app.url'), '/');

            $patterns = [
                '/(href|src)=["\'](' . preg_quote($base_path, '/') . '\/(css|js|images|fonts)\/([^"\']+)["\'])/i',
            ];

            foreach ($patterns as $pattern) {
                $content = preg_replace_callback($pattern, function ($matches) use ($base_path) {
                    $attr = $matches[1];
                    $url = $matches[2];

                    // Add /public/ if not already present
                    if (strpos($url, '/public/') === false) {
                        $url = str_replace($base_path, $base_path . '/public', $url);
                    }

                    return $attr . '="' . $url . '"';
                }, $content);
            }

            $response->setContent($content);
        }

        return $response;
    }
}
