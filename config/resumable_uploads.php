<?php

return [
    'disk'                  => env('RESUMABLE_DISK', 'private'),
    'chunk_size'            => (int) env('RESUMABLE_CHUNK_SIZE', 8 * 1024 * 1024), // 8 Mo
    'max_file_size'         => (int) env('RESUMABLE_MAX_FILE_SIZE', 5 * 1024 * 1024 * 1024), // 5 Go
    'max_chunks'            => (int) env('RESUMABLE_MAX_CHUNKS', 1000),
    'temporary_directory'   => env('RESUMABLE_TEMP_DIR', '.uploads'),
    'expiration_minutes'    => (int) env('RESUMABLE_EXPIRATION_MINUTES', 1440), // 24h
    'max_retries'           => (int) env('RESUMABLE_MAX_RETRIES', 5),
    'small_file_threshold'  => (int) env('RESUMABLE_SMALL_FILE_THRESHOLD', 10 * 1024 * 1024), // 10 Mo
    'allowed_extensions'    => array_filter(array_map('trim', explode(',',
        env('RESUMABLE_ALLOWED_EXTENSIONS', '')))),
    'blocked_extensions'    => ['php', 'php3', 'php4', 'php5', 'phtml', 'phar', 'cgi', 'pl',
                                'py', 'sh', 'exe', 'dll', 'bin', 'htaccess', 'bat', 'cmd'],
];
