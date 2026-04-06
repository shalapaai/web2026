<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Профиль</title>
        <link href="assets/css/fonts.css" rel="stylesheet">
        <link href="assets/css/icons-bar.css" rel="stylesheet">
        <link href="assets/css/profile.css" rel="stylesheet">
    </head>
    <body>
        <nav class="icons-bar">
            <a href="/home/" title="Домой">
                <img class="icons-bar__icon" src="assets/icons/home.svg" alt="Домой" width="40px" height="40px">
            </a>
            <a href="/profile?id=<?= App\Services\UserService::getCurrentUserId() ?>" title="Профиль">
                <img class="icons-bar__icon" src="assets/icons/profile-active.svg" alt="Профиль" width="40px" height="40px">
            </a>
            <a href="/create/" title="Создать пост">
                <img class="icons-bar__icon" src="assets/icons/new-post.svg" alt="Создать пост" width="40px" height="40px">
            </a>
        </nav>
        <div class="about-user">
            <img class="user-avatar" src="<?= $user->getAvatarUrl() ?>" alt="Аватарка" width="123px" height="123px">
            <h1 class="user-name"><?= htmlspecialchars($user->name) ?></h1>
            <p class="user-status"><?= htmlspecialchars($user->profileStatus ?? '') ?></p>
            <div class="user-post-counter">
                <img class="user-post-counter__image" src="assets/icons/post-counter.svg" alt="Счетчик постов" width="16px" height="16pxs">
                <span class="user-post-counter__text"><?= count($posts) . ' ' . App\Models\Post::pluralizePosts(count($posts)) ?></span>
            </div>
        </div>
        <div class="user-posts">
            <?php foreach ($posts as $post) { 
                foreach ($post->images as $image) {?>
                    <a href="/home?postId=<?= $post->id ?>" class="user-posts__image-link" title="Открыть пост">
                        <img src="uploads/posts<?= $image ?>" class="user-posts__image" alt="Картинка из поста" width="322.35px" height="322.35px">
                    </a>
            <?php 
                }
            } ?>
        </div>
    </body>
</html>