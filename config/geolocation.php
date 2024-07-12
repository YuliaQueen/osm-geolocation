<?php

return [
    'osm'   => [
        'base_url'   => env('GEOLOCATION_OSM_BASE_URL', 'https://nominatim.openstreetmap.org'),
        'timeout'    => env('GEOLOCATION_TIMEOUT', 10),
        'user_agent' => env('GEOLOCATION_USER_AGENT', ''),
    ],
    'cache' => [
        'enabled' => env('GEOLOCATION_CACHE_ENABLED', false),
        'minutes' => env('GEOLOCATION_CACHE_MINUTES', 60),
    ],
    'rate_limit' => [
        'enabled' => env('RATE_LIMIT_ENABLED', true),
        'seconds' => env('RATE_LIMIT_SECONDS', 1),
    ],
];