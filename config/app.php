<?php

return [
    'name' => env('APP_NAME', 'ProcessMaker'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'log' => env('APP_LOG', 'single'),
    'log_level' => env('APP_LOG_LEVEL', 'debug'),

    'providers' => [
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
    ],

    'aliases' => [
    ],

];