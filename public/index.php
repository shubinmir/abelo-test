<?php

declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Core\Database;
use App\Core\NotFoundException;
use App\Core\Router;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$view = new View(__DIR__ . '/../templates', sys_get_temp_dir() . '/smarty');
$pdo = Database::connect($config['db']);

$categoryRepository = new CategoryRepository($pdo);
$articleRepository = new ArticleRepository($pdo);

$home = new HomeController($view, $categoryRepository);
$category = new CategoryController($view, $categoryRepository, $articleRepository, $config['per_page']);
$article = new ArticleController($view, $articleRepository, $categoryRepository);

$router = new Router();
$router->get('/', fn () => $home->index());
$router->get('/category/{slug}', fn (array $p) => $category->show($p['slug']));
$router->get('/article/{slug}', fn (array $p) => $article->show($p['slug']));

try {
    echo $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (NotFoundException) {
    http_response_code(404);
    echo $view->render('404.tpl');
}
