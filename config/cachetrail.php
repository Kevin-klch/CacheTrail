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

];
