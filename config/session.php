<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default session "driver" that will be used on
    | requests. By default, we will use the lightweight "file" driver but
    | you may specify any of the other wonderful drivers provided here.
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that will be allowed to
    | elapse before the user is required to re-authenticate. This will
    | greatly reduce security risks when forgetting to log out.
    |
    */

    'lifetime' => env('SESSION_LIFETIME', 120), // 2 hours - reduced from default for security

    'expire_on_close' => true, // Close session when browser closes

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    |
    | When using the native session driver, we need a location where session
    | files may be stored. A default has been set for you but a different
    | location may be specified. This is only needed for file sessions.
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection
    |--------------------------------------------------------------------------
    |
    | When using the "database" or "redis" session drivers and you want to
    | use a different connection than the application's default, you may
    | specify the connection options below. By default, it will use the
    | default connection type for each backend.
    |
    */

    'connection' => env('SESSION_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table we
    | should use to manage the sessions. Of course, a sensible default is
    | provided for you; however, you are free to change this as needed.
    |
    */

    'table' => 'sessions',

    /*
    |--------------------------------------------------------------------------
    | Session Cache Store
    |--------------------------------------------------------------------------
    |
    | When using the "apc", "dynamodb", or "memcached" session drivers you
    | may list a cache store that should be used for these sessions. This
    | value must match with one of the application's configured stores.
    |
    */

    'store' => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | Some session drivers must manually sweep their storage location to get
    | rid of old sessions from storage. Here are the chances that it will
    | happen on a given request. By default, the odds are 2 out of 100.
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Settings
    |--------------------------------------------------------------------------
    |
    | Here you may change the properties of the cookie that is used to store
    | the session ID. The secure option should be set to true if you are
    | running the application over a secure HTTPS connection.
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        strtolower(env('APP_NAME', 'Laravel')).'_session'
    ),

    'path' => env('SESSION_PATH', '/'),

    'domain' => env('SESSION_DOMAIN'),

    'secure' => env('SESSION_SECURE_COOKIES', true), // HTTPS only

    'http_only' => true, // JavaScript cannot access the session cookie

    'same_site' => env('SESSION_SAME_SITE', 'strict'), // CSRF protection - strict mode

    /*
    |--------------------------------------------------------------------------
    | Partitioned Cookies
    |--------------------------------------------------------------------------
    |
    | Setting the value to true will tie the cookie to the top-level site for
    | a cross-site context. Alternatively, you may change the value to
    | "low" to use the less restrictive "Lax" partitioned cookie setting.
    |
    */

    'partitioned' => false,

];
