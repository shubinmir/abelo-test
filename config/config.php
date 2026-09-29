<?php

declare(strict_types=1);

return [
    'db' => [
        'host'     => getenv('DB_HOST') ?: '127.0.0.1',
        'port'     => (int) (getenv('DB_PORT') ?: 3306),
        'name'     => getenv('DB_NAME') ?: 'blog',
        'user'     => getenv('DB_USER') ?: 'blog',
        'password' => getenv('DB_PASSWORD') ?: 'secret',
    ],
];
