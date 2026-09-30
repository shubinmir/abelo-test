<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

final class ArticleController
{
    private const SIMILAR_LIMIT = 3;

    public function __construct(
        private readonly View $view,
        private readonly ArticleRepository $articles,
        private readonly CategoryRepository $categories,
    ) {
    }

    public function show(string $slug): string
    {
        $article = $this->articles->findBySlug($slug) ?? throw new NotFoundException();

        $this->articles->incrementViews($article['id']);
        $article['views']++;

        return $this->view->render('article.tpl', [
            'article'    => $article,
            'paragraphs' => preg_split('/\R{2,}/', trim($article['content'])),
            'categories' => $this->categories->findByArticle($article['id']),
            'similar'    => $this->articles->findSimilar($article['id'], self::SIMILAR_LIMIT),
        ]);
    }
}
