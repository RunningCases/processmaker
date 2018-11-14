<?php

use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\View\ViewServiceProvider;

return [
    'name' => env('APP_NAME', 'ProcessMaker'),
    'url' => env('APP_URL', 'http://localhost'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'log' => env('APP_LOG', 'single'),
    'log_level' => env('APP_LOG_LEVEL', 'debug'),
    'cache_lifetime' => env('APP_CACHE_LIFETIME', 60),
    'key' => env('APP_KEY', 'base64:rU28h/tElUn/eiLY0qC24jJq1rakvAFRoRl1DWxj/kM='),
    'cipher' => 'AES-256-CBC',
    'providers' => [
        CacheServiceProvider::class,
        FilesystemServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        ViewServiceProvider::class
    ],
    'aliases' => [
        'Crypt' => Illuminate\Support\Facades\Crypt::class
    ],

];
