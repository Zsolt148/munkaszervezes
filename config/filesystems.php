<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    'image_max_size' => 10 * 1024, // 10 MB

    'image_resize_driver' => env('IMAGE_RESIZE_DRIVER', 'intervention'),

    'image_extensions' => ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'],

    'thumbnail_sizes' => [265, 265],

    'og_thumbnail_sizes' => [200, 200],

    'general_file_sizes' => [
        'sm' => [
            'thumbnail' => [85, 75],
            'original' => [680, null],
        ],
        'md' => [
            'thumbnail' => [400, 400],
            'original' => [1080, null],
        ],
    ],

    'filemanager' => [

        'default_disk' => 'public',

        /*
        | Default superuser (god) for filemanager
        | Used in spatie hasRole()
        | can be multiple
        */
        'role' => 'superadmin',

        /*
         | Uploadable files count for a folder
         | Set to null for infinite
         */
        'max_file_count' => 100,

        /*
         | Here you may turn off if it should create new folders
         | for admin users when they register
         */
        'create_folders_for_new_admins' => false,

        /*
         | Here you may define a path for new admin folders
         | Must null or string. ex: 'admins'
         */
        'folders_path_for_new_admins' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage', // php artisan storage:link to public folder
            'visibility' => 'public',
            'throw' => false,
        ],

        'media' => [
            'driver' => 'local',
            'root' => storage_path('app/public/media'),
            'url' => env('APP_URL').'/storage/media',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
