<?php 

require_once "src/post-preview.php";

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Домашняя страница</title>
        <link href="css/fonts.css" rel="stylesheet">
        <link href="css/icons-bar.css" rel="stylesheet">
        <link href="css/home.css" rel="stylesheet">
    </head>
    <body>
        <?php
        try {
            $posts = getPosts();
            $users = getUsers();
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Ошибка загрузки данных: ' . $e->getMessage();
            exit;
        }
        ?>
        <nav class="icons-bar">
            <a href="../home/" title="Домой">
                <img class="icons-bar__icon" src="assets/icons/home-active.svg" alt="Домой" width="40px" height="40px">
            </a>
            <a href="../profile?id=<?php echo $users[0]['id'] ?>" title="Профиль">
                <img class="icons-bar__icon" src="assets/icons/profile.svg" alt="Профиль" width="40px" height="40px">
            </a>
            <a href="../create/" title="Создать пост">
                <img class="icons-bar__icon" src="assets/icons/new-post.svg" alt="Создать пост" width="40px" height="40px">
            </a>
        </nav>
        <div class="top-title"></div>
        <div class="content">
            <?php
            foreach ($posts as $post) {
                $userId = $post['authtorId'];
                $user = $users[$userId - 1];
                renderPost($user, $post);
            }
            ?>
        </div>
    </body>
</html>