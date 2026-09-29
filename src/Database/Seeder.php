<?php

declare(strict_types=1);

namespace App\Database;

use App\Core\Str;
use PDO;

/**
 * Наполняет базу тестовыми категориями и статьями.
 * Тексты собираются из заготовленных фраз — без внешних библиотек.
 */
final class Seeder
{
    private const CATEGORIES = [
        'PHP' => [
            'Современный PHP: язык, стандарты, инструменты и лучшие практики.',
            ['PHP 8.3', 'Composer', 'PSR-стандарты', 'строгая типизация', 'атрибуты в PHP', 'enum в PHP'],
        ],
        'Базы данных' => [
            'Проектирование схем, оптимизация запросов и работа с MySQL.',
            ['MySQL', 'индексы', 'оконные функции', 'транзакции', 'нормализация', 'EXPLAIN'],
        ],
        'Frontend' => [
            'Вёрстка, стили и всё, что происходит в браузере.',
            ['SCSS', 'CSS Grid', 'Flexbox', 'адаптивная вёрстка', 'доступность интерфейсов'],
        ],
        'DevOps' => [
            'Инфраструктура, контейнеры, CI/CD и деплой.',
            ['Docker', 'Docker Compose', 'nginx', 'CI/CD', 'мониторинг'],
        ],
        'Архитектура' => [
            'Как проектировать код, который легко читать и менять.',
            ['SOLID', 'чистая архитектура', 'паттерн Repository', 'рефакторинг', 'код-ревью'],
        ],
        'Карьера' => [
            'Собеседования, рост, soft skills и жизнь разработчика.',
            ['собеседование', 'тестовое задание', 'менторство', 'выгорание', 'удалённая работа'],
        ],
        'Анонсы' => [
            'Новости блога. Будет пустой',
            [],
        ],
    ];

    private const TITLE_TEMPLATES = [
        '%s: полное руководство',
        '%s на практике',
        '%s: частые ошибки новичков',
        '%s — что нужно знать',
        '%s: 10 практических советов',
        '%s за 15 минут',
        '%s в реальном проекте',
        'Разбор темы: %s',
        '%s: мифы и реальность',
    ];

    private const SENTENCES = [
        'Эта тема кажется простой, пока не столкнёшься с ней в реальном проекте.',
        'Разберём основные принципы и посмотрим на типичные сценарии использования.',
        'Главное — понимать, какую проблему решает инструмент, а не просто копировать примеры.',
        'На небольших проектах разница почти незаметна, но с ростом нагрузки всё меняется.',
        'Хорошая новость в том, что большинство ошибок легко заметить заранее.',
        'Документация отвечает на многие вопросы, но далеко не на все.',
        'Попробуйте повторить пример у себя и посмотреть, как поведёт себя код.',
        'Не стоит усложнять решение раньше времени — простота почти всегда выигрывает.',
        'Опытные разработчики советуют начинать с измерений, а не с догадок.',
        'Со временем такие вещи становятся привычкой и перестают требовать усилий.',
        'Важно договориться о подходе всей командой, иначе код быстро станет разнородным.',
        'Иногда лучший способ разобраться — прочитать исходники.',
        'Ниже приведены рекомендации, которые хорошо зарекомендовали себя на практике.',
        'Каждый из подходов имеет свои плюсы и минусы, выбор зависит от задачи.',
        'Автоматизация рутинных шагов экономит больше времени, чем кажется.',
        'Не забывайте про тесты: они делают изменения безопасными.',
        'Если что-то работает медленно, сначала найдите узкое место.',
        'В следующих статьях мы вернёмся к этой теме и рассмотрим продвинутые случаи.',
    ];

    private const IMAGES_DIR = '/uploads/seed';
    private const IMAGES_COUNT = 8;

    public function __construct(private readonly PDO $pdo)
    {
    }

    public function run(int $articlesCount): void
    {
        $this->truncate();

        $this->pdo->beginTransaction();

        try {
            $categories = $this->seedCategories();
            $this->seedArticles($articlesCount, $categories);
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function truncate(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec('TRUNCATE TABLE article_category');
        $this->pdo->exec('TRUNCATE TABLE articles');
        $this->pdo->exec('TRUNCATE TABLE categories');
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function seedCategories(): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)'
        );

        $result = [];
        foreach (self::CATEGORIES as $name => [$description, $topics]) {
            $stmt->execute([
                'name'        => $name,
                'slug'        => Str::slug($name),
                'description' => $description,
            ]);
            $result[(int) $this->pdo->lastInsertId()] = $topics;
        }

        return $result;
    }

    private function seedArticles(int $count, array $categories): void
    {
        $articleStmt = $this->pdo->prepare(
            'INSERT INTO articles (title, slug, image, description, content, views, published_at)
             VALUES (:title, :slug, :image, :description, :content, :views, :published_at)'
        );
        $linkStmt = $this->pdo->prepare(
            'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)'
        );

        // Статьи распределяем только по категориям, у которых есть темы
        $categoryIds = array_keys(array_filter($categories));
        $titles = $this->uniqueTitles($categories);

        for ($i = 1; $i <= $count; $i++) {
            // Когда уникальные заголовки кончились, начинаем новый круг с пометкой «часть N»
            $round = intdiv($i - 1, count($titles));
            [$mainCategoryId, $title] = $titles[($i - 1) % count($titles)];
            if ($round > 0) {
                $title .= sprintf(' (часть %d)', $round + 1);
            }

            $articleStmt->execute([
                'title'        => $title,
                'slug'         => Str::slug($title) . '-' . $i,
                'image'        => sprintf('%s/%d.svg', self::IMAGES_DIR, random_int(1, self::IMAGES_COUNT)),
                'description'  => $this->sentences(2),
                'content'      => $this->content(),
                'views'        => random_int(0, 5000),
                'published_at' => date('Y-m-d H:i:s', time() - random_int(0, 365 * 24 * 3600)),
            ]);
            $articleId = (int) $this->pdo->lastInsertId();

            foreach ($this->pickCategories($mainCategoryId, $categoryIds) as $categoryId) {
                $linkStmt->execute(['article_id' => $articleId, 'category_id' => $categoryId]);
            }
        }
    }

    private function uniqueTitles(array $categories): array
    {
        $titles = [];
        foreach ($categories as $categoryId => $topics) {
            foreach ($topics as $topic) {
                foreach (self::TITLE_TEMPLATES as $template) {
                    $title = sprintf($template, $topic);
                    $titles[] = [$categoryId, mb_strtoupper(mb_substr($title, 0, 1)) . mb_substr($title, 1)];
                }
            }
        }
        shuffle($titles);

        return $titles;
    }

    private function pickCategories(int $mainCategoryId, array $categoryIds): array
    {
        $others = array_values(array_diff($categoryIds, [$mainCategoryId]));
        shuffle($others);

        return [$mainCategoryId, ...array_slice($others, 0, random_int(0, 2))];
    }

    private function content(): string
    {
        $paragraphs = [];
        for ($i = 0, $n = random_int(4, 7); $i < $n; $i++) {
            $paragraphs[] = $this->sentences(random_int(3, 5));
        }

        return implode("\n\n", $paragraphs);
    }

    private function sentences(int $count): string
    {
        $keys = (array) array_rand(self::SENTENCES, $count);
        shuffle($keys);

        return implode(' ', array_map(fn (int $k) => self::SENTENCES[$k], $keys));
    }
}
