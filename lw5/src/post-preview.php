<?php

function initJSONFile(string $jsonPath): array  {
    $json = file_get_contents($jsonPath);
    if (!$json) throw new Exception("Файл не найден: $jsonPath");
    $arr = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) throw new Exception("Ошибка декодирования JSON" . json_last_error_msg());
    return $arr;
}

function getPosts(): array {
    return initJSONFile(__DIR__ . '/json/posts.json');
}

function getUsers(): array {
    return initJSONFile(__DIR__ . '/json/users.json');
}

function renderPost(array $user, $post) {
?>

<div class="post">
    <div class="header">
        <a class="header__user" href="/profile/" title="Профиль">
            <img class="header__avatar" src="../uploads/avatars/<?php echo $user['avatar'] ?>" alt="Аватарка" width="32px" height="32px">
            <span class="header__user-name"><?php echo$user['name'] ?></span>
        </a>
        <a href="/edit/" title="Редактировать пост">
            <img class="header__edit-post" src="../assets/icons/edit.svg" alt="Редактировать пост" width="24px" height="24px">
        </a>
    </div>
    <div class="post-content">
        <img class="post-content__image" src="../uploads/posts<?php echo $post['images'][0] ?>" alt="Картинка из поста" width="474px" height="474px">
        <?php 
        $imgCnt = count($post['images']);
        if ($imgCnt !== 1) { ?>
            <span class="post-content__counter">1/<?php echo $imgCnt ?></span>
            <div class="post-content__arrow arrow_left">
                <img src="../assets/icons/arrow-left.svg" width="10px" height="10px">
            </div>
            <div class="post-content__arrow arrow_right">
                <img src="../assets/icons/arrow-right.svg" width="10px" height="10px">
            </div>
        <?php 
        } 
        ?>
    </div>
    <div class="about-post">
        <button class="likes">
            <img class="likes__image" src="../assets/icons/like.png" alt="Лайк" width="16px" height="16px">
            <span class="likes__counter"><?php echo $post['likes'] ?></span>
        </button>
        <p class="post-text"><?php echo $post['content'] ?></p>
        <button class="read-more">ещё</button>
        <p class="posted-at"><?php echo date($user['createdAt']) ?></p>
    </div>
</div>

<?php
}
?>