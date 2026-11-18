<?php

return [

    /*
    |--------------------------------------------------------------------------
    | First Admin Account
    |--------------------------------------------------------------------------
    |
    | Password of the "admin" owner account created by the database seeder
    | outside local development (locally it is always "admin"). Without a
    | password no admin account is seeded.
    |
    */

    'admin_password' => env('CACHETRAIL_ADMIN_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Languages
    |--------------------------------------------------------------------------
    |
    | Languages visitors can switch between. Texts use English keys with a
    | German translation in lang/de.json; English needs no translation file.
    |
    */

    'locales' => [
        'de' => 'Deutsch',
        'en' => 'English',
    ],

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
    | ("Dein Standort: Moers") and to find places for the radius search.
    | Requests are sent by the server only.
    |
    */

    'geocoding' => [
        'reverse_url' => env('CACHETRAIL_REVERSE_GEOCODING_URL', 'https://nominatim.openstreetmap.org/reverse'),

        // Used for "routes within X km of a place" in the route directory.
        'search_url' => env('CACHETRAIL_PLACE_GEOCODING_URL', 'https://nominatim.openstreetmap.org/search'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Data and Feedback Account
    |--------------------------------------------------------------------------
    |
    | With demo data switched on, the database seeder adds demo routes, a demo
    | community and a shared account for testers. Its login details are shown
    | on the login page; email address, password and deletion are locked.
    |
    */

    'feedback' => [
        // Seed demo routes, a demo community and this shared account (development and test servers only).
        'demo_data' => (bool) env('CACHETRAIL_DEMO_DATA', false),

        'email' => env('CACHETRAIL_FEEDBACK_EMAIL', 'feedback@example.com'),
        'password' => env('CACHETRAIL_FEEDBACK_PASSWORD', 'cachetrail-testen'),

        // Floating "Feedback" button on every page; messages appear under Administration → Feedback.
        'button' => (bool) env('CACHETRAIL_FEEDBACK_BUTTON', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Imprint and Privacy Policy
    |--------------------------------------------------------------------------
    |
    | Details of the operator shown in the imprint and privacy policy. They
    | are set in the .env file, so no personal data ends up in the code.
    | Missing values are highlighted on the pages outside production.
    |
    */

    'legal' => [
        'name' => env('CACHETRAIL_LEGAL_NAME'),
        'street' => env('CACHETRAIL_LEGAL_STREET'),
        'city' => env('CACHETRAIL_LEGAL_CITY'),
        'email' => env('CACHETRAIL_LEGAL_EMAIL'),
        'phone' => env('CACHETRAIL_LEGAL_PHONE'),
        'hosting' => env('CACHETRAIL_LEGAL_HOSTING'),
        'mail_provider' => env('CACHETRAIL_LEGAL_MAIL_PROVIDER'),
        'privacy_updated_at' => '2026-11-18',
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
