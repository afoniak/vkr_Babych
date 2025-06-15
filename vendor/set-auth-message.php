<?php
session_start();

$source = $_POST['source'] ?? '';

switch ($source) {
    case 'basket':
        $_SESSION['auth_message'] = [
            'type' => 'error',
            'message' => 'для доступу до кошика потрібно авторизуватися!'
        ];
        break;
    case 'account':
        $_SESSION['auth_message'] = [
            'type' => 'error',
            'message' => 'для доступу до акаунта потрібно авторизуватися!'
        ];
        break;
    default:
        $_SESSION['auth_message'] = [
            'type' => 'error',
            'message' => 'для виконання цієї дії потрібно авторизуватися!'
        ];
        break;
}

echo 'success';
?> 