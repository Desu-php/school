<?php

return [
    'api_user_id' => env('SENDPULSE_API_USER_ID', 'b12601c6c3f861ec6c7d4629c2dbc81c'),
    'api_secret' => env('SENDPULSE_API_SECRET', '1384b902b9b87fb2375f825ed8472820'),

    /*
     *  Define where script will save access token
     *
     *  Types: session, file
     */
    'token_storage' => env('TOKEN_STORAGE', 'file'),

    'storages' => [
        'file' => [
            'path' => '/app/'
        ]
    ]
];
