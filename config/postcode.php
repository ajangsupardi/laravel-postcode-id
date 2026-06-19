<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | Path where the downloaded CSV file will be stored. By default, it uses
    | the Laravel storage directory. You may change this to any absolute path.
    |
    */

    'storage_path' => storage_path('app/postcode'),

    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for database table names. Set to null for no prefix.
    | Example: 'postcode_' will create tables like 'postcode_provinces'.
    |
    */

    'table_prefix' => null,

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Override the default models by specifying your own Eloquent models here.
    | This is useful if you want to add columns or customize the models.
    |
    */

    'models' => [
        'province' => Ajangsupardi\PostcodeId\Models\Province::class,
        'regency' => Ajangsupardi\PostcodeId\Models\Regency::class,
        'district' => Ajangsupardi\PostcodeId\Models\District::class,
        'village' => Ajangsupardi\PostcodeId\Models\Village::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Settings
    |--------------------------------------------------------------------------
    |
    | Configure the HTTP client used when downloading postcode data.
    |
    */

    'http' => [
        'timeout' => 60,
        'connect_timeout' => 10,
        'retry' => 3,
        'retry_delay' => 1000,
        'user_agent' => 'Mozilla/5.0 (compatible; LaravelPostcodeId/1.0)',
    ],

];
