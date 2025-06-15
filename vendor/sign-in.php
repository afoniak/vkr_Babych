<?php
session_start();
require_once '../connect-db.php';

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';


if (empty($login) || empty($password)) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'заповніть всі поля!'
    ];
    header('Location: ../login.php');
    exit();
}

// пошук користувача за email або логіном
$stmt = mysqli_prepare($connect, "SELECT id, login, email, password, image_account FROM users WHERE email = ? OR login = ?");
mysqli_stmt_bind_param($stmt, "ss", $login, $login);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'користувач не знайдений!'
    ];
    header('Location: ../login.php');
    exit();
}


if (!password_verify($password, $user['password'])) {
    $_SESSION['auth_message'] = [
        'type' => 'error',
        'message' => 'невірний пароль!'
    ];
    header('Location: ../login.php');
    exit();
}

// авторизуємо користувача
$_SESSION['user'] = [
    'id' => $user['id'],
    'login' => $user['login'],
    'email' => $user['email'],
    'image_account' => $user['image_account']
];

$_SESSION['notification'] = [
    'type' => 'success',
    'message' => 'ласкаво просимо, ' . $user['login'] . '!'
];

header('Location: ../index.php');
?> 