<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Premium Access
    |--------------------------------------------------------------------------
    |
    | Until the premium check against Geocaching.com is confirmed, premium
    | access is granted to every user or to nobody. Administrators always
    | have premium access.
    |
    */

    'premium' => [
        'grant_to_all_users' => (bool) env('CACHETRAIL_PREMIUM_FOR_ALL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Geocache Provider
    |--------------------------------------------------------------------------
    |
    | Source of geocache data. Only "mock" exists until the Geocaching.com
    | API has been approved; it serves fictional caches from a JSON file.
    |
    */

    'geocaching' => [
        'provider' => env('CACHETRAIL_GEOCACHE_PROVIDER', 'mock'),

        'mock' => [
            'data_file' => database_path('data/mock-geocaches.json'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Reverse Geocoding
    |--------------------------------------------------------------------------
    |
    | Used to show the place name of the visitor's shared location
    | ("Dein Standort: Moers"). Requests are sent by the server only.
    |
    */

    'geocoding' => [
        'reverse_url' => env('CACHETRAIL_REVERSE_GEOCODING_URL', 'https://nominatim.openstreetmap.org/reverse'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    */

    'trails' => [
        'min_caches_to_publish' => 2,

        // Slug of the route shown to visitors as an example (home page and /beispielroute).
        // Falls back to the most liked published route.
        'example' => env('CACHETRAIL_EXAMPLE_TRAIL', 'waldabenteuer-am-niederrhein'),
    ],

];
