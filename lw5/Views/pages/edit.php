<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="assets/css/fonts.css" rel="stylesheet">
        <link href="assets/css/icons-bar-no-adap.css" rel="stylesheet">
        <link href="assets/css/create.css" rel="stylesheet">
        <title>Редактировать пост</title>
    </head>
    <body>
        <nav class="icons-bar">
            <a href="../home/" title="Домой">
                <img class="icons-bar__icon" src="assets/icons/home.svg" alt="Домой" width="40px" height="40px">
            </a>
            <a href="../profile/" title="Профиль">
                <img class="icons-bar__icon" src="assets/icons/profile.svg" alt="Профиль" width="40px" height="40px">
            </a>
            <a href="../create/" title="Создать пост">
                <img class="icons-bar__icon" src="assets/icons/new-post.svg" alt="Создать пост" width="40px" height="40px">
            </a>
        </nav>
        <div class="header">
            <a href="/home/"><img class="header__arrow" src="assets/icons/arrow-left.svg" alt="Обратно" width="24px" height="24px"></a>
            <span class="header__name">Редактировать пост</span>
        </div>
        <form class="input-field" action="/upload-images" method="post" enctype="multipart/form-data">
            <div class="add-photo-container">
                <div class="add-photo-container__field">
                    <img class="add-photo-container__picture" src="assets/icons/picture.png" alt="Ввод картинки" width="81px" height="81px">
                    <button class="add-photo-container__post-image-main-button">Добавить фото</button>
                    <input style="display: none;" type="file" accept="image/jpeg, image/png" multiple>
                </div>
            </div>
            <div class="inputs-conteiner">
                <button class="post-image-button">
                    <img src="assets/icons/plus.svg" alt="Плюс">
                    <span class="post-image-button__text">Добавить фото</span>
                    <input style="display: none;" type="file" accept="image/jpeg, image/png" multiple>
                </button>
                <textarea class="add-info" name="comment" rows="10" maxlength="500" placeholder="Добавьте подпись"></textarea>
                <button class="save" title="Сохранить">Сохранить изменения</button>
            </div>
        </form>
    </body>
</html>