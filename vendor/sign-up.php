<?php
session_start();
require_once '../connect-db.php';

$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';


if (empty($login) || empty($email) || empty($password)) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'заповніть всі поля!'
    ];
    header('Location: ../register.php');
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'паролі не співпадають!'
    ];
    header('Location: ../register.php');
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'пароль повинен містити мінімум 6 символів!'
    ];
    header('Location: ../register.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'невірний формат email!'
    ];
    header('Location: ../register.php');
    exit();
}

// перевіряємо існування користувача з таким email або логіном
$stmt = mysqli_prepare($connect, "SELECT id FROM users WHERE email = ? OR login = ?");
mysqli_stmt_bind_param($stmt, "ss", $email, $login);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'користувач з таким email a6o логіном вже існує!'
    ];
    header('Location: ../register.php');
    exit();
}

// створюємо користувача
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$default_image = 'images/account/default.jpg';

$stmt = mysqli_prepare($connect, "INSERT INTO users (login, email, password, image_account, created_at) VALUES (?, ?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, "ssss", $login, $email, $hashed_password, $default_image);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['auth_message'] = [
        'type' => 'success',
        'message' => 'реєстрація успішна! тепер ви можете увійти.'
    ];
    header('Location: ../login.php');
} else {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'помилка реєстрації: ' . mysqli_error($connect)
    ];
    header('Location: ../register.php');
}
?> 