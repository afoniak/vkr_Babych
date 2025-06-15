<?php
session_start();

// перевіряємо авторизацію
if (!isset($_SESSION['user'])) {
    $_SESSION['notification'] = [
        'type' => 'warning',
        'message' => 'Для доступу до кошика потрібно спочатку авторизуватися!'
    ];
    header('Location: login.php');
    exit();
}

require_once 'connect-db.php';
$user_id = $_SESSION['user']['id'];

// отримуємо товари з кошика користувача
$stmt = mysqli_prepare($connect, "
    SELECT b.id, b.product_id, b.product_name, b.price, b.quantity, b.size, b.added_at,
           p.image_path, p.description, p.material
    FROM basket b 
    LEFT JOIN product p ON b.product_id = p.id 
    WHERE b.user_id = ? 
    ORDER BY b.added_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$basket_items = [];
$total_amount = 0;
$total_quantity = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $basket_items[] = $row;
    $total_amount += $row['price'] * $row['quantity'];
    $total_quantity += $row['quantity'];
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo/logo-gustavo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style-css/style-header-footer2.css">
    <link rel="stylesheet" href="style-css/style-basket-order.css">
    <script src="https://js.stripe.com/v3/"></script>
    <title>GUSTAVO - Кошик</title>
</head>
<body>
    <?php include 'header.php'; ?>


    <?php include 'vendor/notification.php'; ?>
    
    <div class="basket-main-container">
        <h1 class="basket-title">Ваш кошик</h1>
        
        <?php if (empty($basket_items)): ?>
            
            <div class="empty-basket">
                <i class="fas fa-shopping-cart"></i>
                <h3>Ваш кошик порожній</h3>
                <p>Додайте товари до кошика, щоб почати покупки</p>
                <a href="index.php" class="continue-shopping-btn">
                    <i class="fas fa-arrow-left"></i> Продовжити покупки
                </a>
            </div>
        <?php else: ?>
            <!-- Контейнер с товарами -->
            <div class="basket-items-container">
                <?php foreach ($basket_items as $item): ?>
                    <div class="basket-item" data-item-id="<?= $item['id']; ?>" data-price="<?= $item['price']; ?>">
                        <div class="item-image">
                            <img src="<?= htmlspecialchars($item['image_path'] ?: 'images/products/placeholder.jpg'); ?>" 
                                 alt="<?= htmlspecialchars($item['product_name']); ?>"
                                 onerror="this.src='images/products/placeholder.jpg';">
                        </div>
                        <div class="item-info">
                            <h3 class="item-title"><?= htmlspecialchars($item['product_name']); ?></h3>
                            <p class="item-description"><?= htmlspecialchars($item['description'] ?: 'Опис товару'); ?></p>
                            <?php if ($item['size'] !== 'ONE_SIZE'): ?>
                                <span class="item-size">Розмір: <?= htmlspecialchars($item['size']); ?></span>
                            <?php endif; ?>
                            <?php if ($item['material']): ?>
                                <span class="item-material">Матеріал: <?= htmlspecialchars($item['material']); ?></span>
                            <?php endif; ?>
                            <span class="item-price">₴<?= number_format($item['price'], 0, ',', ' '); ?></span>
                        </div>
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button class="quantity-btn minus" onclick="changeQuantity(<?= $item['id']; ?>, -1)">-</button>
                                <span class="quantity"><?= $item['quantity']; ?></span>
                                <button class="quantity-btn plus" onclick="changeQuantity(<?= $item['id']; ?>, 1)">+</button>
                            </div>
                            <button class="remove-btn" onclick="removeItem(<?= $item['id']; ?>)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <div class="total-section">
                <div class="total-info">
                    <p>Товарів y кошику: <span id="total-items"><?= $total_quantity; ?></span></p>
                    <h3>Загальна сума: <span class="total-price" id="total-price">₴<?= number_format($total_amount, 0, ',', ' '); ?></span></h3>
                </div>
            </div>


            <div class="delivery-form-container">
                <h2>Інформація про доставку</h2>
                <form class="delivery-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">Ім'я</label>
                            <input type="text" id="first-name" name="first-name" required>
                        </div>
                        <div class="form-group">
                            <label for="last-name">Прізвище</label>
                            <input type="text" id="last-name" name="last-name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Адреса доставки</label>
                        <textarea id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="delivery-method">Спосіб доставки</label>
                        <select id="delivery-method" name="delivery-method" required>
                            <option value="">Оберіть спосіб доставки</option>
                            <option value="nova-poshta">Нова Пошта</option>
                            <option value="ukr-poshta">Укрпошта</option>
                            <option value="courier">Kyp'єрська доставка</option>
                        </select>
                    </div>
                </form>
            </div>


            <button class="checkout-btn" onclick="checkout()">
                <i class="fas fa-credit-card"></i> Оплатити
            </button>
        <?php endif; ?>
    </div>

    <script>
        // функція для зміни кількості товару
        function changeQuantity(itemId, change) {
            const quantityElement = document.querySelector(`[data-item-id="${itemId}"] .quantity`);
            const currentQuantity = parseInt(quantityElement.textContent);
            const newQuantity = currentQuantity + change;
            
            if (newQuantity < 1) {
                if (confirm('Видалити товар з кошика?')) {
                    removeItem(itemId);
                }
                return;
            }
            
            
            fetch('vendor/basket-process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&item_id=${itemId}&quantity=${newQuantity}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    location.reload(); 
                } else {
                    alert('Помилка при оновленні кількості!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка при оновленні кількості!');
            });
        }
        
        // функція для видалення товару
        function removeItem(itemId) {
            if (!confirm('Ви впевнені, що хочете видалити цей товар?')) {
                return;
            }
            
            fetch('vendor/basket-process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=remove&item_id=${itemId}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    location.reload(); 
                } else {
                    alert('Помилка при видаленні товару!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка при видаленні товару!');
            });
        }
        
        // функція оформлення замовлення
        function checkout() {
            // перевіряємо заповнення форми
            const form = document.querySelector('.delivery-form');
            
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                alert('Будь ласка, заповніть всі обов\'язкові поля!');
                return;
            }
            
            // блокування кнопки
            const checkoutBtn = document.querySelector('.checkout-btn');
            checkoutBtn.disabled = true;
            checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Обробка...';
            
            // створюємо сесію оплати
            fetch('create-checkout-session.php', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: <?= $total_amount; ?>,
                    items: <?= $total_quantity; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Помилка: ' + data.error);
                    resetCheckoutButton();
                } else {
                    // перенаправляємо на stripe checkout
                    const stripe = Stripe('pk_test_#');
                    stripe.redirectToCheckout({ sessionId: data.id })
                        .then(function (result) {
                            // якщо є помилка після спроби редіректу
                            if (result.error) {
                                alert('Помилка: ' + result.error.message);
                                resetCheckoutButton();
                            }
                        });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка з\'єднання. Спробуйте ще раз.');
                resetCheckoutButton();
            });
        }
        
        function resetCheckoutButton() {
            const checkoutBtn = document.querySelector('.checkout-btn');
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = '<i class="fas fa-credit-card"></i> Оплатити';
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
    