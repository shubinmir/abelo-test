<?php

declare(strict_types=1);

use App\Core\Database;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';
$pdo = Database::connect($config['db']);

echo 'DB: ' . $pdo->query('SELECT 1')->fetchColumn();
