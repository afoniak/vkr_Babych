<?php
session_start();
require_once 'connect-db.php';

// отримуємо id товару з url
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// перевіряємо авторизацію користувача
$is_logged_in = isset($_SESSION['user']);

// якщо id не вказано, перенаправляємо на головну
if ($product_id <= 0) {
    header('Location: index.php');
    exit();
}

// отримуємо дані товару з бази даних
$stmt = mysqli_prepare($connect, "SELECT id, name, description, material, price, image_path, gender, category, sub_category FROM product WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($product = mysqli_fetch_assoc($result)) {
    // товар знайдено
    $product_name = $product['name'];
    $product_description = $product['description'];
    $product_material = $product['material'];
    $product_price = $product['price'];
    $product_image = $product['image_path'];
    $product_gender = $product['gender'];
    $product_category = $product['category'];
    $product_sub_category = $product['sub_category'];
} else {
    // товар не знайдено - перенаправляємо на головну
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'Товар не знайдено!'
    ];
    header('Location: index.php');
    exit();
}

// переклади категорій для breadcrumbs
$gender_names = [
    'm' => 'Чоловічий',
    'f' => 'Жіночий'
];

$sub_category_names = [
    't-shirt' => 'Футболки',
    'shirt' => 'Рубашки',
    'pants' => 'Штани', 
    'shorts' => 'Шорти',
    'accessories' => 'Аксесуари'
];

$gender_name = isset($gender_names[$product_gender]) ? $gender_names[$product_gender] : '';
$category_name = isset($sub_category_names[$product_sub_category]) ? $sub_category_names[$product_sub_category] : '';

// визначаємо тип для url на основі sub_category
$type_mapping = [
    't-shirt' => 'tshirts',
    'shirt' => 'hoodies', 
    'pants' => 'pants',
    'shorts' => 'shorts',
    'accessories' => 'accessories'
];
$url_type = isset($type_mapping[$product_sub_category]) ? $type_mapping[$product_sub_category] : 'tshirts';
?>

<!DOCTYPE html>
<html lang="uk">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="images/logo/logo-gustavo.png" type="image/png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style-css/style-header-footer2.css">
    <link rel="stylesheet" href="style-css/style-goods.css">
    <title>GUSTAVO - <?= htmlspecialchars($product_name); ?></title>
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Система сповіщень -->
    <?php include 'vendor/notification.php'; ?>
    
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="breadcrumbs-container">
            <a href="index.php">Головна</a>
            <span>></span>
            <a href="catalog.php?category=<?= $product_gender; ?>&type=<?= $url_type; ?>"><?= htmlspecialchars($gender_name . ' ' . $category_name); ?></a>
            <span>></span>
            <span><?= htmlspecialchars($product_name); ?></span>
        </div>
    </div>
    
    <div class="product-container">
        <div class="product-image-section">
            <img src="<?= htmlspecialchars($product_image); ?>" alt="<?= htmlspecialchars($product_name); ?>" class="product-image" onerror="this.src='images/products/placeholder.jpg';">
        </div>
        
        <div class="product-info-section">
            <div class="product-header">
                <h1 class="product-title"><?= htmlspecialchars($product_name); ?></h1>
            </div>
            
            <p class="product-description">
                <?= htmlspecialchars($product_description); ?>
            </p>
            
            <?php if ($product_material): ?>
            <div class="product-material">
                <strong>Матеріал:</strong> <?= htmlspecialchars($product_material); ?>
            </div>
            <?php endif; ?>
            
            <?php if (in_array($product_sub_category, ['t-shirt', 'shirt', 'pants', 'shorts'])): ?>
            <div class="size-selection">
                <h3>Оберіть розмір:</h3>
                <div class="size-options">
                    <label class="size-option">
                        <input type="radio" name="size" value="S">
                        <span class="size-label">S</span>
                    </label>
                    <label class="size-option">
                        <input type="radio" name="size" value="M">
                        <span class="size-label">M</span>
                    </label>
                    <label class="size-option">
                        <input type="radio" name="size" value="L" checked>
                        <span class="size-label">L</span>
                    </label>
                    <label class="size-option">
                        <input type="radio" name="size" value="XL">
                        <span class="size-label">XL</span>
                    </label>
        </div>
            </div>
            <?php endif; ?>
            
            <div class="price-section">
                <span class="price"><?= number_format($product_price, 0, ',', ' '); ?> ₴</span>
        </div>
            
            <?php if ($is_logged_in): ?>
                <button class="buy-button" onclick="addToCart(<?= $product_id; ?>)">
                    <i class="fas fa-shopping-cart"></i> Додати в кошик
                </button>
            <?php else: ?>
                <button class="buy-button" onclick="redirectToLogin('add-to-cart')">
                    <i class="fas fa-shopping-cart"></i> Додати в кошик
                </button>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // функція для додавання товару в кошик
        function addToCart(productId) {
            const selectedSize = document.querySelector('input[name="size"]:checked');
            const haseSizeSelection = document.querySelector('.size-selection') !== null;
            
            // перевіряємо розмір тільки якщо є вибір розмірів
            if (haseSizeSelection && !selectedSize) {
                alert('Будь ласка, оберіть розмір!');
                return;
            }
            
            const size = selectedSize ? selectedSize.value : 'ONE_SIZE';
            
            // відправляємо запит для додавання в кошик
            fetch('vendor/basket-process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=add&product_id=${productId}&size=${size}`
            })
            .then(response => response.text())
            .then(data => {
                console.log('Server response:', data); // для відладки
                if (data.trim() === 'success') {
                    // перезавантажуємо сторінку для показу сповіщення
                    location.reload();
                } else {
                    console.error('Unexpected response:', data);
                    alert('Помилка при додаванні товару!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Помилка при додаванні товару!');
            });
        }
        
        // функція перенаправлення на авторизацію
        function redirectToLogin(source) {
            fetch('vendor/set-auth-message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'source=' + source
            }).then(() => {
                location.href = 'login.php';
            });
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>