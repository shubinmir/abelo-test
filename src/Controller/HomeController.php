<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\CategoryRepository;

final class HomeController
{

    private const LATEST_PER_CATEGORY = 3;

    public function __construct(
        private readonly View $view,
        private readonly CategoryRepository $categories,
    ) {
    }

    public function index(): string
    {
        return $this->view->render('home.tpl', [
            'categories' => $this->categories->findWithLatestArticles(self::LATEST_PER_CATEGORY),
        ]);
    }
}
