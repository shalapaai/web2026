<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Логинация</title>
        <link href="assets/css/fonts.css" rel="stylesheet">
        <link href="assets/css/login.css" rel="stylesheet">
        <script type="module" src="assets/js/main.js"></script>
    </head>
    <body>
        <div class="main">
            <h1 class="main-header">Войти</h1>
            <div class="container">
                <img class="main-image" src="assets/images/main-image.jpg" alt="Заглавная картинка" width="462px" height="501px">
                <form class="login-form" action="/home/">
                    <label class="login-form__label" for="email">Электропочта</label>
                    <input class="login-form__input-field" id="email" type="email">
                    <p class="login-form__extra-info">Введите электропочту в формате *****@***.**</p>
                    <label class="login-form__label" for="password">Пароль</label>

                    <div class="login-form__password-wrapper">
                        <input class="login-form__input-field" id="password" type="password">
                        <img class="login-form__show-password-image" src="assets/icons/eye-off.svg" alt="Скрывать пароль" width="24px" height="24px">
                    </div>

                    <button class="login-form__continue-button" title="Продолжить">Продолжить</button>
                </form>
            </div>
        </div>
    </body>
</html>