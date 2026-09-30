<?php

declare(strict_types=1);

use ScssPhp\ScssPhp\Compiler;

require __DIR__ . '/../vendor/autoload.php';

$source = __DIR__ . '/../scss/style.scss';
$target = __DIR__ . '/../public/css/style.css';

$css = (new Compiler())->compileFile($source)->getCss();

if (!is_dir(dirname($target))) {
    mkdir(dirname($target), 0775, true);
}
file_put_contents($target, $css);

echo "CSS собран: public/css/style.css\n";
