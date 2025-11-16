<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | API key được sử dụng để xác thực các request từ client.
    | Client phải gửi header X-Api-Key với giá trị này.
    |
    */

    'key' => env('API_KEY', 'your-api-key-here'),

    /*
    |--------------------------------------------------------------------------
    | API Key Header Name
    |--------------------------------------------------------------------------
    |
    | Tên header mà client phải gửi API key.
    |
    */

    'header_name' => env('API_KEY_HEADER', 'X-Api-Key'),
];


