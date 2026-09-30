<article class="card">
    <a class="card__image" href="/article/{$article.slug}">
        <img src="{$article.image}" alt="{$article.title}" loading="lazy">
    </a>
    <div class="card__body">
        <h3 class="card__title">
            <a href="/article/{$article.slug}">{$article.title}</a>
        </h3>
        <div class="meta">
            <time datetime="{$article.published_at}">{$article.published_at|date_format:"d.m.Y"}</time>
            <span class="meta__views">{$article.views} просмотров</span>
        </div>
        <p class="card__description">{$article.description}</p>
        <a class="card__more" href="/article/{$article.slug}">Читать далее</a>
    </div>
</article>
