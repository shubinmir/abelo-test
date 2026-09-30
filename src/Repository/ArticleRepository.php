<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ArticleRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM article_category WHERE category_id = ?');
        $stmt->execute([$categoryId]);

        return (int) $stmt->fetchColumn();
    }

    public function findByCategory(int $categoryId, int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.id, a.title, a.slug, a.image, a.description, a.views, a.published_at
             FROM articles a
             JOIN article_category ac ON ac.article_id = a.id
             WHERE ac.category_id = :category_id
             ORDER BY a.published_at DESC, a.id DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, slug, image, description, content, views, published_at
             FROM articles
             WHERE slug = ?'
        );
        $stmt->execute([$slug]);

        return $stmt->fetch() ?: null;
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}
