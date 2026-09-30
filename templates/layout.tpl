<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{block name=title}Блог{/block}</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="header">
    <div class="container">
        <a href="/">Блог</a>
    </div>
</header>

<main class="container">
    {block name=content}{/block}
</main>

<footer class="footer">
    <div class="container">&copy; {$smarty.now|date_format:"Y"} Блог</div>
</footer>
</body>
</html>
