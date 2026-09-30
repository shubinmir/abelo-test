{if $paginator->hasPages()}
    <nav class="pagination" aria-label="Страницы">
        {if $paginator->page > 1}
            <a class="pagination__link" href="?page={$paginator->page - 1}">&larr;</a>
        {/if}
        {for $p = 1 to $paginator->pages}
            {if $p == $paginator->page}
                <span class="pagination__link pagination__link--active">{$p}</span>
            {else}
                <a class="pagination__link" href="?page={$p}">{$p}</a>
            {/if}
        {/for}
        {if $paginator->page < $paginator->pages}
            <a class="pagination__link" href="?page={$paginator->page + 1}">&rarr;</a>
        {/if}
    </nav>
{/if}
