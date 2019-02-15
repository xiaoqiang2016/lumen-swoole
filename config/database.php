<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => env('DB_PREFIX', ''),
        ],
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_SINOCLICK_HOST', '127.0.0.1'),
            'port' => env('DB_SINOCLICK_PORT', 3306),
            'database' => env('DB_SINOCLICK_DATABASE', 'forge'),
            'username' => env('DB_SINOCLICK_USERNAME', 'forge'),
            'password' => env('DB_SINOCLICK_PASSWORD', ''),
            'unix_socket' => env('DB_SINOCLICK_SOCKET', ''),
            'charset' => env('DB_SINOCLICK_CHARSET', 'utf8mb4'),
            'collation' => env('DB_SINOCLICK_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_SINOCLICK_PREFIX', ''),
            'strict' => env('DB_SINOCLICK_STRICT_MODE', true),
            'engine' => env('DB_SINOCLICK_ENGINE', null),
            'timezone' => env('DB_SINOCLICK_TIMEZONE', '+00:00'),
        ],
        'sinoclick' => [
            'driver' => 'mysql',
            'host' => env('DB_SINOCLICK_HOST', '127.0.0.1'),
            'port' => env('DB_SINOCLICK_PORT', 3306),
            'database' => env('DB_SINOCLICK_DATABASE', 'forge'),
            'username' => env('DB_SINOCLICK_USERNAME', 'forge'),
            'password' => env('DB_SINOCLICK_PASSWORD', ''),
            'unix_socket' => env('DB_SINOCLICK_SOCKET', ''),
            'charset' => env('DB_SINOCLICK_CHARSET', 'utf8mb4'),
            'collation' => env('DB_SINOCLICK_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_SINOCLICK_PREFIX', ''),
            'strict' => env('DB_SINOCLICK_STRICT_MODE', true),
            'engine' => env('DB_SINOCLICK_ENGINE', null),
            'timezone' => env('DB_SINOCLICK_TIMEZONE', '+00:00'),
        ],

        'facebook' => [
            'driver' => 'mysql',
            'host' => env('DB_FACEBOOK_HOST', '127.0.0.1'),
            'port' => env('DB_FACEBOOK_PORT', 3306),
            'database' => env('DB_FACEBOOK_DATABASE', 'forge'),
            'username' => env('DB_FACEBOOK_USERNAME', 'forge'),
            'password' => env('DB_FACEBOOK_PASSWORD', ''),
            'unix_socket' => env('DB_FACEBOOK_SOCKET', ''),
            'charset' => env('DB_FACEBOOK_CHARSET', 'utf8mb4'),
            'collation' => env('DB_FACEBOOK_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_FACEBOOK_PREFIX', ''),
            'strict' => env('DB_FACEBOOK_STRICT_MODE', true),
            'engine' => env('DB_FACEBOOK_ENGINE', null),
            'timezone' => env('DB_FACEBOOK_TIMEZONE', '+00:00'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

//    'redis' => [
//
//        'client' => 'predis',
//
//        'cluster' => env('REDIS_CLUSTER', false),
//
//        'default' => [
//            'host' => env('REDIS_HOST', '127.0.0.1'),
//            'password' => env('REDIS_PASSWORD', null),
//            'port' => env('REDIS_PORT', 6379),
//            'database' => env('REDIS_DB', 0),
//        ],
//
//        'cache' => [
//            'host' => env('REDIS_HOST', '127.0.0.1'),
//            'password' => env('REDIS_PASSWORD', null),
//            'port' => env('REDIS_PORT', 6379),
//            'database' => env('REDIS_CACHE_DB', 1),
//        ],
//
//    ],

];
