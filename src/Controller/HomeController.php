<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;

final class HomeController
{
    public function __construct(private readonly View $view)
    {
    }

    public function index(): string
    {
        return $this->view->render('home.tpl');
    }
}
