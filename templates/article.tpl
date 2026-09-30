{extends file="layout.tpl"}

{block name=title}{$article.title} — Блог{/block}

{block name=content}
    <article class="article">
        <header class="article__header">
            <div class="article__categories">
                {foreach $categories as $category}
                    <a class="tag" href="/category/{$category.slug}">{$category.name}</a>
                {/foreach}
            </div>
            <h1 class="article__title">{$article.title}</h1>
            <div class="meta">
                <time datetime="{$article.published_at}">{$article.published_at|date_format:"d.m.Y H:i"}</time>
                <span class="meta__views">{$article.views} просмотров</span>
            </div>
            <p class="article__lead">{$article.description}</p>
        </header>

        <img class="article__image" src="{$article.image}" alt="{$article.title}">

        <div class="article__content">
            {foreach $paragraphs as $paragraph}
                <p>{$paragraph}</p>
            {/foreach}
        </div>
    </article>
{/block}
