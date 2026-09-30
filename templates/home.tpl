{extends file="layout.tpl"}

{block name=content}
    {foreach $categories as $category}
        <section class="category-section">
            <header class="category-section__header">
                <h2 class="category-section__title">{$category.name}</h2>
                <a class="button" href="/category/{$category.slug}">Все статьи</a>
            </header>
            <div class="grid">
                {foreach $category.articles as $article}
                    {include file="partials/article_card.tpl" article=$article}
                {/foreach}
            </div>
        </section>
    {foreachelse}
        <p class="empty">Статей пока нет. Запустите сидер: <code>php bin/seed.php</code></p>
    {/foreach}
{/block}
