<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Core\NotFoundException;
use App\Core\Router;
use App\Core\View;

require __DIR__ . '/../vendor/autoload.php';

$view = new View(__DIR__ . '/../templates', sys_get_temp_dir() . '/smarty');

$home = new HomeController($view);

$router = new Router();
$router->get('/', fn () => $home->index());

try {
    echo $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (NotFoundException) {
    http_response_code(404);
    echo $view->render('404.tpl');
}
