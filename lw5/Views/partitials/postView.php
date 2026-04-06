<?php
function renderPost($post, $user) {
?>

<div class="post">
    <div class="header">
        <a class="header__user" href="/profile?id=<?= $user->id ?>" title="Профиль">
            <img class="header__avatar" src="<?= $user->getAvatarUrl() ?>" alt="Аватарка" width="32px" height="32px">
            <span class="header__user-name"><?= htmlspecialchars($user->name) ?></span>
        </a>
        <?php if ($user->id === App\Services\UserService::getCurrentUserId()) { ?>
        <a href="/edit/" title="Редактировать пост">
            <img class="header__edit-post" src="../assets/icons/edit.svg" alt="Редактировать пост" width="24px" height="24px">
        </a>
        <?php 
        } ?>
    </div>
    <div class="post-content">
        <?php if ($post->hasImage()) { ?>
            <img class="post-content__image" src="/uploads/posts<?= $post->getFirstImage() ?>" alt="Картинка из поста" width="474px" height="474px">
            <?php 
            $imgCnt = $post->getImagesCount();
            if ($imgCnt > 1) { ?>
                <span class="post-content__counter">1/<?= $imgCnt ?></span>
                <div class="post-content__arrow arrow_left">
                    <img src="../assets/icons/arrow-left.svg" width="10px" height="10px">
                </div>
                <div class="post-content__arrow arrow_right">
                    <img src="../assets/icons/arrow-right.svg" width="10px" height="10px">
                </div>
            <?php 
            } 
        }
        ?>
    </div>
    <div class="about-post">
        <button class="likes" title="Лайкнуть">
            <img class="likes__image" src="../assets/icons/like.png" alt="Лайк" width="16px" height="16px">
            <span class="likes__counter"><?= $post->likes ?></span>
        </button>
        <p class="post-text"><?= htmlspecialchars($post->content ?? '') ?></p>
        <button class="read-more" title="Показать ещё">ещё</button>
        <p class="posted-at"><?= $post->getRelativeTime() ?></p>
    </div>
</div>

<?php
}
?>