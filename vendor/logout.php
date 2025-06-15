<?php
session_start();

// очищаємо сесію
unset($_SESSION['user']);
session_destroy();

// повідомлення про вихід
session_start();
$_SESSION['notification'] = [
    'type' => 'success',
    'message' => 'ви успішно вийшли з акаунту!'
];

header('Location: ../index.php');
?> 