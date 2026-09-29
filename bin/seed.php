<?php

declare(strict_types=1);

/**
 * Наполнение базы тестовыми данными.
 * Запуск: php bin/seed.php [--articles=60]
 */

use App\Core\Database;
use App\Database\Seeder;

require __DIR__ . '/../vendor/autoload.php';

$options = getopt('', ['articles::']);
$articles = max(1, (int) ($options['articles'] ?? 60));

$config = require __DIR__ . '/../config/config.php';

(new Seeder(Database::connect($config['db'])))->run($articles);

echo "Создано {$articles} статей.\n";
