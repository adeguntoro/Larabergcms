<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | All CMS routes (list, create, edit, media upload) are registered under
    | this prefix.
    |
    */

    'prefix' => 'laraberg',

    /*
    |--------------------------------------------------------------------------
    | Route middleware
    |--------------------------------------------------------------------------
    |
    | Applied to every CMS route. "web" is required so sessions, CSRF and
    | flash messages work. "auth" is enabled by default so only logged-in
    | users can use the CMS.
    |
    | When using the default Laravel auth, every route requires a logged-in
    | user. For admin-only access (when using a custom admin middleware),
    | replace "auth" with your own middleware:
    |
    |     'middleware' => ['web', 'auth', 'ensure.admin'],
    |
    | To expose the CMS publicly (NOT recommended), remove "auth":
    |
    |     'middleware' => ['web'],
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Upload disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk used by the media upload route. Use the "public"
    | disk and run `php artisan storage:link` for locally served files.
    |
    */

    'disk' => env('LARABERG_UPLOAD_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Maximum upload size
    |--------------------------------------------------------------------------
    |
    | In kilobytes.
    |
    */

    'max_upload_kb' => 51200,

    /*
    |--------------------------------------------------------------------------
    | Allowed file extensions
    |--------------------------------------------------------------------------
    |
    | Controls which file types the media upload endpoint accepts.
    |
    | If `enforce_allowed_extensions` is TRUE, only files with one of the
    | extensions listed in `allowed_extensions` can be uploaded. Anything
    | else is rejected. Use this when you want a strict whitelist.
    |
    | If `enforce_allowed_extensions` is FALSE, the extension whitelist is
    | ignored — any file extension is accepted. Note: the HTML/SVG content
    | check still always applies regardless of this setting.
    |
    */

    'enforce_allowed_extensions' => true,

    'allowed_extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'avif', 'ico',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'odp', 'rtf', 'txt',
        'md', 'markdown', 'csv', 'json', 'xml',
        'mp4', 'webm', 'mov', 'ogg', 'ogv', 'avi', 'mkv',
        'mp3', 'wav', 'm4a', 'aac', 'flac',
        'zip', 'rar', '7z', 'gz', 'tar',
    ],

];