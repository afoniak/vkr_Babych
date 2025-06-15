<?php
session_start();
require_once 'connect-db.php';

// отримуємо параметри з url
$category = isset($_GET['category']) ? $_GET['category'] : 'm';
$type = isset($_GET['type']) ? $_GET['type'] : 'tshirts';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// визначаємо категорію товарів для бази даних
$db_category = '';
$sub_category = null;
switch ($type) {
    case 'tshirts':
        $db_category = 't';
        $sub_category = 't-shirt';
        break;
    case 'hoodies':
        $db_category = 't';
        $sub_category = 'shirt';
        break;
    case 'pants':
        $db_category = 'b';
        $sub_category = 'pants';
        break;
    case 'shorts':
        $db_category = 'b';
        $sub_category = 'shorts';
        break;
    case 'accessories':
        $db_category = 'a';
        $sub_category = 'accessories';
        break;
    default:
        $db_category = 't';
        $sub_category = 't-shirt';
}

// визначаємо сортування для sql запиту
$order_by = '';
switch ($sort) {
    case 'name-asc':
        $order_by = 'ORDER BY name ASC';
        break;
    case 'name-desc':
        $order_by = 'ORDER BY name DESC';
        break;
    case 'price-asc':
        $order_by = 'ORDER BY price ASC';
        break;
    case 'price-desc':
        $order_by = 'ORDER BY price DESC';
        break;
    default:
        $order_by = 'ORDER BY id ASC'; 
}


// отримуємо товари з правильною фільтрацією
if ($sub_category !== null) {
    $sql = "SELECT id, name, description, material, price, image_path FROM product WHERE gender = ? AND category = ? AND sub_category = ? " . $order_by;
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $category, $db_category, $sub_category);
} else {
    $sql = "SELECT id, name, description, material, price, image_path FROM product WHERE gender = ? AND category = ? " . $order_by;
    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $category, $db_category);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

$page_titles = [
    'tshirts' => 'Футболки',
    'hoodies' => 'Рубашки',
    'pants' => 'Штани',
    'shorts' => 'Шорти',
    'accessories' => 'Аксесуари'
];
$page_title = isset($page_titles[$type]) ? $page_titles[$type] : 'Товари';

$gender_names = [
    'm' => 'Чоловічі',
    'f' => 'Жіночі'
];
$gender_name = isset($gender_names[$category]) ? $gender_names[$category] : '';

$sort_names = [
    'name-asc' => '3a назвою: A-Я',
    'name-desc' => '3a назвою: Я-A',
    'price-asc' => '3a ціною: дешевші',
    'price-desc' => '3a ціною: дорожчі'
];
$current_sort_name = isset($sort_names[$sort]) ? $sort_names[$sort] : 'Сортування';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo/logo-gustavo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style-css/style-header-footer2.css">
    <link rel="stylesheet" href="style-css/style.css">
    <link rel="stylesheet" href="style-css/style-catalog2.css">
    <title>GUSTAVO - <?= htmlspecialchars($gender_name . ' ' . $page_title); ?></title>
</head>
<body>
    <?php include 'header.php'; ?>

    <?php include 'vendor/notification.php'; ?>

    <main class="catalog-main">
        <div class="catalog-container">
            <div class="catalog-header">
                <h1 class="catalog-title"><?= htmlspecialchars($gender_name . ' ' . $page_title); ?></h1>
                <?php if (!empty($products)): ?>
                    <p class="catalog-subtitle">Знайдено товарів: <strong><?= count($products); ?></strong></p>
                <?php endif; ?>
            </div>

            <div class="catalog-content">
                <div class="catalog-controls">
                    <div class="sort-dropdown">
                        <button class="sort-button">
                            <span><?= htmlspecialchars($current_sort_name); ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="sort-menu">
                            <?php if ($sort): ?>
                                <a href="?category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>" class="sort-item sort-reset">
                                    <i class="fas fa-times"></i> Скинути сортування
                                </a>
                                <div class="sort-divider"></div>
                            <?php endif; ?>
                            <a href="?category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&sort=name-asc" class="sort-item <?= $sort === 'name-asc' ? 'active' : ''; ?>" data-sort="name-asc">3a назвою: A-Я</a>
                            <a href="?category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&sort=name-desc" class="sort-item <?= $sort === 'name-desc' ? 'active' : ''; ?>" data-sort="name-desc">3a назвою: Я-A</a>
                            <a href="?category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&sort=price-asc" class="sort-item <?= $sort === 'price-asc' ? 'active' : ''; ?>" data-sort="price-asc">3a ціною: дешевші</a>
                            <a href="?category=<?= urlencode($category); ?>&type=<?= urlencode($type); ?>&sort=price-desc" class="sort-item <?= $sort === 'price-desc' ? 'active' : ''; ?>" data-sort="price-desc">3a ціною: дорожчі</a>
                        </div>
                    </div>
                </div>


                <div class="products-grid">
                    <?php if (empty($products)): ?>
                        <div class="no-products">
                            <i class="fas fa-box-open"></i>
                            <h3>Товари не знайдені</h3>
                            <p>Ha жаль, товарів y цій категорії поки немає</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($product['image_path']); ?>" 
                                             alt="<?= htmlspecialchars($product['name']); ?>" 
                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;product-placeholder&quot;><i class=&quot;fas fa-tshirt&quot;></i><span>Зображення недоступне</span></div>';">
                                    <?php else: ?>
                                        <div class="product-placeholder">
                                            <i class="fas fa-tshirt"></i>
                                            <span>Зображення недоступне</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?= htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-price">₴<?= number_format($product['price'], 0, ',', ' '); ?></p>
                                    <button class="product-button" onclick="location.href='goods.php?id=<?= $product['id']; ?>';">Оглянути</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        // функціонал сортування
        document.addEventListener('DOMContentLoaded', function() {
            const sortButton = document.querySelector('.sort-button');
            const sortItems = document.querySelectorAll('.sort-item');
            
            // обробка кліків по функції сортування
            sortItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    
                    const urlParams = new URLSearchParams(window.location.search);
                    const category = urlParams.get('category') || 'm';
                    const type = urlParams.get('type') || 'tshirts';
                    const sortType = this.getAttribute('data-sort');
                    
                    // формуємо новий url з параметром сортування
                    const newUrl = `?category=${category}&type=${type}&sort=${sortType}`;
                    

                    window.location.href = newUrl;
                });
            });

            // автоматична оптимізація зображень товарів
            const productImages = document.querySelectorAll('.product-image img');
            productImages.forEach(img => {
                img.addEventListener('load', function() {
                    const aspectRatio = this.naturalWidth / this.naturalHeight;
                    
                    // видаляємо попередні класи
                    this.classList.remove('portrait-image', 'landscape-image', 'square-image');
                    
                    // якщо зображення занадто високе (портретне)
                    if (aspectRatio < 0.8) {
                        this.classList.add('portrait-image');
                    }
                    // якщо зображення занадто широкое (ландшафтне)
                    else if (aspectRatio > 1.4) {
                        this.classList.add('landscape-image');
                    }
                    // для квадратних і нормальних зображень
                    else {
                        this.classList.add('square-image');
                    }
                });
                
                // якщо зображення вже завантажено
                if (img.complete) {
                    img.dispatchEvent(new Event('load'));
                }
            });
        });
    </script>
</body>
</html>
