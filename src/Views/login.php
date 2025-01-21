<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/inventario/public/css/login.css">
    <style>
        .sede {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>

<body>
    <section class="container__form">
        <form class="form__login" action="<?= '/login' ?>" method="post">
            <h1>Login</h1>
            <div class="container__inputs">
                <label for="username">Usuario: </label>
                <input id="username" type="text" name="username" placeholder="laguaca" required>
            </div>
            <div class="container__inputs">
                <label for="password">Contraseña: </label>
                <input id="password" type="password" name="password">
            </div>
            <div class="sede">
                <div>
                    <label for="sahagun">Sahagún: </label>
                    <input type="radio" name="sede" value="sahagun" id="sahagun" />
                </div>
                <div>
                    <label for="sincelejo">Sincelejo: </label>
                    <input type="radio" name="sede" value="sincelejo" id="sincelejo" />
                </div>
            </div>
            <?php if (isset($_SESSION['error_login'])): ?>
                <div class="error-login">
                    <?php echo $_SESSION['error_login']; ?>
                    <?php unset($_SESSION['error_login']); ?>
                </div>
            <?php endif; ?>
            <div class="container__button">
                <button type="submit">Ingresar</button>
            </div>
        </form>
    </section>
</body>

</html>