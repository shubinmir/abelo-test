## Запуск

Нужны Docker и Docker Compose.

```bash
make install
```

Команда поднимет контейнеры, установит зависимости, соберёт CSS и заполнит базу тестовыми данными.
Сайт будет доступен на http://localhost:8080 (порт меняется через `APP_PORT` в `.env`).

То же без `make`:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec php composer install
docker compose exec php composer build-css
docker compose exec php composer seed
```

Схема БД (`database/schema.sql`) применяется автоматически при первом старте MySQL.

| Команда | Что делает |
|---|---|
| `make seed` / `make seed ARTICLES=100` | пересоздать тестовые категории и статьи |
| `make css` | пересобрать CSS из SCSS |
| `make up` / `make down` | запустить / остановить контейнеры |

## Сидинг

`composer seed -- --articles=N` (по умолчанию 60) очищает таблицы и создаёт 7 категорий и N статей.
Заголовки строятся из комбинаций «шаблон × тема категории» и не повторяются, тексты собираются из набора фраз.
Дата публикации (за последний год), просмотры и 1–3 категории статьи выбираются случайно.
Картинки — SVG-заглушки из `public/uploads/seed/`