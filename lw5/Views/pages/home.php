<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Домашняя страница</title>
        <link href="assets/css/fonts.css" rel="stylesheet">
        <link href="assets/css/icons-bar.css" rel="stylesheet">
        <link href="assets/css/home.css" rel="stylesheet">
    </head>
    <body>
        <nav class="icons-bar">
            <a href="/home/" title="Домой">
                <img class="icons-bar__icon" src="assets/icons/home-active.svg" alt="Домой" width="40px" height="40px">
            </a>
            <a href="/profile?id=1" title="Профиль">
                <img class="icons-bar__icon" src="assets/icons/profile.svg" alt="Профиль" width="40px" height="40px">
            </a>
            <a href="/create/" title="Создать пост">
                <img class="icons-bar__icon" src="assets/icons/new-post.svg" alt="Создать пост" width="40px" height="40px">
            </a>
        </nav>
        <div class="top-title"></div>
        <div class="content">
            <?php
                require_once __DIR__ . '/../partitials/postView.php';
                foreach ($posts as $post) {
                    $user = array_find($users, fn($u) => $u->id === $post->authorId);
                    renderPost($post, $user);
                }
            ?>
        </div>
    </body>
</html>