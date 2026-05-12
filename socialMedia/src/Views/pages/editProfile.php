<?php 
function renderEditProfilePage(array $data) {
    extract($data);
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Изменить профиль</title>
        <link href="../assets/css/fonts.css" rel="stylesheet">
        <link href="../assets/css/login.css" rel="stylesheet">
        <link href="../assets/css/icons-bar-no-adap.css" rel="stylesheet">
        <script type="module" src="../assets/js/main.js"></script>
    </head>
    <body>
        <nav class="icons-bar">
            <a href="../home/" title="Домой">
                <img class="icons-bar__icon" src="../assets/icons/home.svg" alt="Домой" width="40px" height="40px">
            </a>
            <a href="../profile?id=<?= $_SESSION['user_id'] ?>" title="Профиль">
                <img class="icons-bar__icon" src="../assets/icons/profile.svg" alt="Профиль" width="40px" height="40px">
            </a>
            <a href="../create/" title="Создать пост">
                <img class="icons-bar__icon" src="../assets/icons/new-post.svg" alt="Создать пост" width="40px" height="40px">
            </a>
            <a href="/logout/" title="Выйти">
                <div class="icons-bar__logout-div"><img class="icons-bar__icon" src="../assets/icons/logout.svg" alt="Выйти" width="24px" height="24px"></div>
            </a>
        </nav>
        <div class="main">
            <h1 class="main-header">Профиль</h1>
            <div class="container">
                <img class="main-image" src="../assets/images/main-image.jpg" alt="Заглавная картинка" width="462px" height="501px">
                <form class="login-form" action="/home/">
                    <label class="login-form__label" for="name">Имя</label>
                    <input class="login-form__input-field" id="name" type="text" value="<?= $user->name ?? '' ?>">
                    <label class="login-form__label" for="content">Статус</label>
                    <input class="login-form__input-field" id="content" type="text" value="<?= $user->profileStatus ?? '' ?>">
                    <label class="login-form__label" for="password">Аватар</label>
                    <div class="add-photo-container__field">
                        <button class="login-form__input-field" id="add-new-photo" title="Добавить фото">Добавить фото</button>
                        <input style="display: none;" type="file" accept="image/jpeg, image/png">
                    </div>

                    <button class="login-form__continue-button" title="Продолжить">Изменить</button>
                </form>
            </div>
        </div>
    </body>
</html>
<?php } ?>