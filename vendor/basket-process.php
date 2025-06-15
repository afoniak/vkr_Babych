<?php
session_start();
require_once '../connect-db.php';

// перевіряємо авторизацію
if (!isset($_SESSION['user'])) {
    echo 'unauthorized';
    exit();
}

$user_id = $_SESSION['user']['id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addToCart();
        break;
    case 'remove':
        removeFromCart();
        break;
    case 'update':
        updateQuantity();
        break;
    case 'get_count':
        getCartCount();
        break;
    default:
        echo 'error';
        break;
}

function addToCart() {
    global $connect, $user_id;
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $size = isset($_POST['size']) ? $_POST['size'] : 'ONE_SIZE';

    // перевіряємо коректність id товару
    if ($product_id <= 0) {
        echo 'error';
        exit();
    }

    try {
        // отримуємо інформацію про товар
        $stmt = mysqli_prepare($connect, "SELECT name, price, category FROM product WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);

        if (!$product) {
            echo 'product_not_found';
            exit();
        }

        // перевіряємо чи є товар в кошику
        $stmt = mysqli_prepare($connect, "
            SELECT id, quantity FROM basket 
            WHERE user_id = ? AND product_id = ? AND size = ?
        ");
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $size);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing_item = mysqli_fetch_assoc($result);

        if ($existing_item) {
            // збільшуємо кількість
            $new_quantity = $existing_item['quantity'] + 1;
            $stmt = mysqli_prepare($connect, "UPDATE basket SET quantity = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $new_quantity, $existing_item['id']);
            mysqli_stmt_execute($stmt);
        } else {
            // додаємо новий товар
            $stmt = mysqli_prepare($connect, "
                INSERT INTO basket (user_id, product_id, product_category, product_name, price, quantity, added_at, size) 
                VALUES (?, ?, ?, ?, ?, 1, NOW(), ?)
            ");
            mysqli_stmt_bind_param($stmt, "iissis", 
                $user_id, $product_id, $product['category'], 
                $product['name'], intval($product['price']), $size
            );
            mysqli_stmt_execute($stmt);
        }

        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'товар додано до кошика!'
        ];

        echo 'success';
    } catch (Exception $e) {
        echo 'error';
    }
}

function removeFromCart() {
    global $connect, $user_id;
    
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

    // перевіряємо коректність id елементу
    if ($item_id <= 0) {
        echo 'error';
        exit();
    }

    try {
        // видаляємо товар з кошика
        $stmt = mysqli_prepare($connect, "DELETE FROM basket WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
        
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'товар видалено з кошика!'
            ];
            echo 'success';
        } else {
            echo 'error';
        }
    } catch (Exception $e) {
        echo 'error';
    }
}

function updateQuantity() {
    global $connect, $user_id;
    
    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    // перевіряємо коректність параметрів
    if ($item_id <= 0 || $quantity <= 0) {
        echo 'error';
        exit();
    }

    try {
        // оновлюємо кількість товару
        $stmt = mysqli_prepare($connect, "
            UPDATE basket SET quantity = ? 
            WHERE id = ? AND user_id = ?
        ");
        mysqli_stmt_bind_param($stmt, "iii", $quantity, $item_id, $user_id);
        
        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'кількість оновлено!'
            ];
            echo 'success';
        } else {
            echo 'error';
        }
    } catch (Exception $e) {
        echo 'error';
    }
}

function getCartCount() {
    global $connect, $user_id;
    
    try {
        // отримуємо загальну кількість товарів в кошику
        $stmt = mysqli_prepare($connect, "SELECT SUM(quantity) as total FROM basket WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        echo intval($row['total']);
    } catch (Exception $e) {
        echo '0';
    }
}
?> 