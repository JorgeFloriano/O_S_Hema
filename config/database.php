<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'mysql' => [
        'driver' => 'mysql', // The database driver (MySQL in this case)
        'url' => env('DATABASE_URL'), // Optional: URL for the database connection
        'host' => env('DB_HOST', '127.0.0.1'), // Database host (default: '127.0.0.1')
        'port' => env('DB_PORT', '3306'), // Database port (default: '3306')
        'database' => env('DB_DATABASE', 'forge'), // Database name (default: 'forge')
        'username' => env('DB_USERNAME', 'forge'), // Database username (default: 'forge')
        'password' => env('DB_PASSWORD', ''), // Database password (default: '')
        'unix_socket' => env('DB_SOCKET', ''), // Unix socket (default: '')
        'charset' => 'utf8mb4', // Default character set (default: 'utf8mb4')
        'collation' => 'utf8mb4_unicode_ci', // Default collation (default: 'utf8mb4_unicode_ci')
        'prefix' => '', // Table prefix (default: '')
        'prefix_indexes' => true, // Whether to prefix indexes (default: true)
        'strict' => true, // Enable strict mode (default: true)
        'engine' => null, // Database engine (default: null)
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [], // Additional PDO options (default: empty array)
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
