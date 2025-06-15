<?php
session_start();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo/logo-gustavo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style-css/style-header-footer2.css">
    <link rel="stylesheet" href="style-css/style.css">
    <link rel="stylesheet" href="style-css/style-login-register.css">
    <title>GUSTAVO - Реєстрація</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <?php include 'vendor/notification.php'; ?>

    <main class="auth-main">
        <div class="auth-container">
            <h1 class="auth-title">Реєстрація</h1>
            
            <?php if (isset($_SESSION['auth_message'])): ?>
                <div class="alert alert-<?= $_SESSION['auth_message']['type'] ?>">
                    <?= $_SESSION['auth_message']['message'] ?>
                </div>
                <?php unset($_SESSION['auth_message']); ?>
            <?php endif; ?>
            
            <form class="auth-form" action="vendor/sign-up.php" method="POST">
                <div class="form-group">
                    <label for="login">Логін</label>
                    <input type="text" id="login" name="login" placeholder="Введіть ваш логін" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Електронна пошта</label>
                    <input type="email" id="email" name="email" placeholder="Введіть вашу електронну пошту" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" placeholder="Введіть пароль (мінімум 6 символів)" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Підтвердження пароля</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Підтвердіть пароль" required>
                </div>
                
                <button type="submit" class="auth-button">Зареєструватися</button>
            </form>
            
            <p class="auth-link-text">
                Ви вже зареєстровані? Тоді <a href="login.php" class="auth-link">авторизуйтесь</a>!
            </p>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
