<?php
function renderPost(array $post, $user) {
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
        <p class="posted-at"><?php echo date('d.m.Y H:i', $post['createdAt']) ?></p>
    </div>
</div>

<?php
}
?>