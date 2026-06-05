<?php

$parseOrigins = static function (?string $value): array {
    if (! $value) {
        return [];
    }

    return array_map(
        static fn (string $origin): string => rtrim(trim($origin), '/'),
        preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: []
    );
};

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_unique(array_filter(array_merge(
        [
            rtrim(env('FRONTEND_URL', 'http://localhost:8080'), '/'),
            rtrim(env('FRONTEND_URL_ALT', 'http://127.0.0.1:8080'), '/'),
            rtrim(env('FRONTEND_URL_LAN1', ''), '/'),
            rtrim(env('FRONTEND_URL_LAN2', ''), '/'),
            rtrim(env('APP_URL', ''), '/'),
        ],
        $parseOrigins(env('CORS_ALLOWED_ORIGINS'))
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
