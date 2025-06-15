<?php
session_start();
require_once '../connect-db.php';

// перевіряємо авторизацію
if (!isset($_SESSION['user'])) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'для доступу потрібно авторизуватися!'
    ];
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';


if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'заповніть всі поля!'
    ];
    header('Location: ../account.php');
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'новий пароль та підтвердження не співпадають!'
    ];
    header('Location: ../account.php');
    exit();
}

if (strlen($new_password) < 6) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'новий пароль повинен містити мінімум 6 символів!'
    ];
    header('Location: ../account.php');
    exit();
}

// перевіряємо поточний пароль
$stmt = mysqli_prepare($connect, "SELECT password FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user || !password_verify($current_password, $user['password'])) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'поточний пароль невірний!'
    ];
    header('Location: ../account.php');
    exit();
}

// оновлюємо пароль
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($connect, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['notification'] = [
        'type' => 'success',
        'message' => 'пароль успішно змінено!'
    ];
} else {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'помилка при зміні пароля: ' . mysqli_error($connect)
    ];
}

header('Location: ../account.php');
?> 