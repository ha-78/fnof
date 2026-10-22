<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Device remember cookie
    |--------------------------------------------------------------------------
    |
    | Per-device long-lived login. Each visit inside the lifetime window
    | rolls the expiry forward.
    |
    */

    'cookie_name' => env('DEVICE_REMEMBER_COOKIE', 'fnof_device'),

    'lifetime_days' => (int) env('DEVICE_REMEMBER_DAYS', 7),

];
