<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'connect-db.php';

// перевіряємо авторизацію
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Потрібна авторизація']);
    exit();
}

// встановлюємо stripe api ключ
\Stripe\Stripe::setApiKey('sk_test_#');

header('Content-Type: application/json');

$user_id = $_SESSION['user']['id'];
$user_email = $_SESSION['user']['email'];
$YOUR_DOMAIN = 'http://clothmarket';

try {
    // отримуємо дані з запиту
    $input = json_decode(file_get_contents('php://input'), true);
    $amount = isset($input['amount']) ? floatval($input['amount']) : 0;
    $items_count = isset($input['items']) ? intval($input['items']) : 0;

    if ($amount <= 0 || $items_count <= 0) {
        throw new Exception('Невірна сума a6o кількість товарів');
    }

    $first_name = isset($input['first_name']) ? trim($input['first_name']) : '';
    $last_name = isset($input['last_name']) ? trim($input['last_name']) : '';
    $full_name = $first_name . ' ' . $last_name;
    $phone = isset($input['phone']) ? trim($input['phone']) : '';
    $address = isset($input['address']) ? trim($input['address']) : '';
    $delivery_method = isset($input['delivery_method']) ? trim($input['delivery_method']) : '';

    // отримуємо товари з кошика
    $stmt = mysqli_prepare($connect, "
        SELECT product_name, price, quantity, size 
        FROM basket 
        WHERE user_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $cart_items = [];
    $line_items = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
        $line_items[] = [
            'price_data' => [
                'currency' => 'uah',
                'product_data' => [
                    'name' => $row['product_name'] . ' (Розмір: ' . $row['size'] . ')',
                ],
                'unit_amount' => intval($row['price'] * 100), 
            ],
            'quantity' => $row['quantity'],
        ];
    }

    if (empty($cart_items)) {
        throw new Exception('Кошик порожній');
    }

    // починаємо транзакцію
    mysqli_autocommit($connect, false);

    // створюємо замовлення в БД
    $stmt = mysqli_prepare($connect, "
        INSERT INTO orders (user_id, total_amount, user_email, status, full_name, phone_number, delivery_address, delivery_method) 
        VALUES (?, ?, ?, 'pending', ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param($stmt, "idsssss", $user_id, $amount, $user_email, $full_name, $phone, $address, $delivery_method);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($connect);

    // додаємо товари замовлення
    $stmt = mysqli_prepare($connect, "
        INSERT INTO order_items (order_id, product_name, price, quantity, size) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($cart_items as $item) {
        mysqli_stmt_bind_param($stmt, "isdis", 
            $order_id, 
            $item['product_name'], 
            $item['price'], 
            $item['quantity'], 
            $item['size']
        );
        mysqli_stmt_execute($stmt);
    }

    // створюємо checkout сесію
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/order-success.php?order_id=' . $order_id,
        'cancel_url' => $YOUR_DOMAIN . '/basket.php',
        'metadata' => [
            'user_id' => $user_id,
            'order_id' => $order_id,
            'total_amount' => $amount,
            'items_count' => $items_count
        ],
        'customer_email' => $user_email,
    ]);

    // оновлюємо замовлення з stripe session id
    $stmt = mysqli_prepare($connect, "UPDATE orders SET stripe_session_id = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $checkout_session->id, $order_id);
    mysqli_stmt_execute($stmt);

    // відправляємо чек на email
    require_once 'email-functions.php';
    sendOrderEmail($user_email, $order_id, $cart_items, $amount);

    // очищуємо кошик
    $stmt = mysqli_prepare($connect, "DELETE FROM basket WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // підтверджуємо транзакцію
    mysqli_commit($connect);

    echo json_encode(['id' => $checkout_session->id]);

} catch (Exception $e) {
    // відкатуємо транзакцію при помилці
    mysqli_rollback($connect);
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// відновлюємо автокоміт
mysqli_autocommit($connect, true);

?> 
