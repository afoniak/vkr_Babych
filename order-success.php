<?php
session_start();
require_once 'connect-db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$order_id = $_GET['order_id'] ?? '';
$user_id = $_SESSION['user']['id'];

// перевірка що замовлення належить користувачу
if (!empty($order_id)) {
    $stmt = mysqli_prepare($connect, "SELECT id, total_amount FROM orders WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    
    if ($order) {
        // оновлюємо статус замовлення paid
        $stmt = mysqli_prepare($connect, "UPDATE orders SET status = 'paid' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Оплата пройшла успішно! Чек відправлено на вашу електронну пошту. Дякуємо за покупку!'
        ];
    }
}

// перенаправляємо на головну з сповіщенням
header('Location: index.php');
exit();
?>