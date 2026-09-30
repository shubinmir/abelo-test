<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, name, slug, description FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);

        return $stmt->fetch() ?: null;
    }

    public function findByArticle(int $articleId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.name, c.slug
             FROM categories c
             JOIN article_category ac ON ac.category_id = c.id
             WHERE ac.article_id = ?
             ORDER BY c.name'
        );
        $stmt->execute([$articleId]);

        return $stmt->fetchAll();
    }

    public function findWithLatestArticles(int $limit): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM (
                 SELECT c.id   AS category_id,
                        c.name AS category_name,
                        c.slug AS category_slug,
                        a.id, a.title, a.slug, a.image, a.description, a.views, a.published_at,
                        ROW_NUMBER() OVER (PARTITION BY c.id ORDER BY a.published_at DESC, a.id DESC) AS rn
                 FROM categories c
                 JOIN article_category ac ON ac.category_id = c.id
                 JOIN articles a          ON a.id = ac.article_id
             ) ranked
             WHERE rn <= :limit
             ORDER BY category_name, rn'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $categories = [];
        foreach ($stmt as $row) {
            $id = $row['category_id'];
            $categories[$id] ??= [
                'id'       => $id,
                'name'     => $row['category_name'],
                'slug'     => $row['category_slug'],
                'articles' => [],
            ];
            $categories[$id]['articles'][] = array_diff_key(
                $row,
                array_flip(['category_id', 'category_name', 'category_slug', 'rn'])
            );
        }

        return array_values($categories);
    }
}
