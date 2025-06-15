<?php
session_start();
require_once '../connect-db.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'для доступу потрібно авторизуватися!'
    ];
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

// перевіряємо чи завантажено файл
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'оберіть фото для завантаження!'
    ];
    header('Location: ../account.php');
    exit();
}

$file = $_FILES['photo'];
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// перевіряємо тип файлу
if (!in_array($file['type'], $allowed_types)) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'дозволені тільки файли JPEG, PNG та GIF!'
    ];
    header('Location: ../account.php');
    exit();
}

// перевіряємо розмір файлу
if ($file['size'] > $max_size) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'розмір файлу не повинен перевищувати 5MB!'
    ];
    header('Location: ../account.php');
    exit();
}

// створюємо директорію якщо не існує
$upload_dir = '../images/account/users/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// генеруємо унікальне ім'я файлу
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$new_filename = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
$upload_path = $upload_dir . $new_filename;

// завантажуємо файл
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    $photo_path = 'images/account/users/' . $new_filename;
    
    // оновлюємо шлях до фото в базі даних
    $stmt = mysqli_prepare($connect, "UPDATE users SET image_account = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $photo_path, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // оновлюємо інформацію в сесії
        $_SESSION['user']['image_account'] = $photo_path;
        
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'фото профілю успішно оновлено!'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'помилка при збереженні в базі даних: ' . mysqli_error($connect)
        ];
    }
} else {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'помилка при завантаженні файлу!'
    ];
}

header('Location: ../account.php');
?> 