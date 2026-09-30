{extends file="layout.tpl"}

{block name=title}{$category.name} — Блог{/block}

{block name=content}
    <header class="page-header">
        <h1 class="page-header__title">{$category.name}</h1>
        <p class="page-header__description">{$category.description}</p>
    </header>

    {if $articles}
        <div class="grid">
            {foreach $articles as $article}
                {include file="partials/article_card.tpl" article=$article}
            {/foreach}
        </div>
        {include file="partials/pagination.tpl"}
    {else}
        <p class="empty">В этой категории пока нет статей.</p>
    {/if}
{/block}
