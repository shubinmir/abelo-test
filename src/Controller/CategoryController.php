<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Core\Paginator;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

final class CategoryController
{
    public function __construct(
        private readonly View $view,
        private readonly CategoryRepository $categories,
        private readonly ArticleRepository $articles,
        private readonly int $perPage,
    ) {
    }

    public function show(string $slug): string
    {
        $category = $this->categories->findBySlug($slug) ?? throw new NotFoundException();

        $paginator = new Paginator(
            $this->articles->countByCategory($category['id']),
            $this->perPage,
            (int) ($_GET['page'] ?? 1),
        );

        return $this->view->render('category.tpl', [
            'category'  => $category,
            'articles'  => $this->articles->findByCategory($category['id'], $paginator->perPage, $paginator->offset()),
            'paginator' => $paginator,
        ]);
    }
}
