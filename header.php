<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// перевіряємо авторизацію користувача
$is_logged_in = isset($_SESSION['user']);
$user_data = null;
$user_avatar = 'images/account/default.jpg';
$cart_count = 0;

if ($is_logged_in) {
    $user_data = $_SESSION['user'];
    
    //повну інформацію про користувача з БД для аватара
    require_once 'connect-db.php';
    $stmt = mysqli_prepare($connect, "SELECT image_account FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_data['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $user_avatar = $row['image_account'] ? $row['image_account'] : 'images/account/default.jpg';
    }
    
    // кількість товарів в кошику
    $stmt = mysqli_prepare($connect, "SELECT SUM(quantity) as total_items FROM basket WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_data['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $cart_count = $row['total_items'] ? (int)$row['total_items'] : 0;
    }
}
?>

<header>
    <div class="header-content">
        <div class="header-left">
            <?php if ($is_logged_in): ?>
                <button class="cart-button" onclick="location.href='basket.php';" title="Кошик">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?= $cart_count; ?></span>
                    <?php endif; ?>
                </button>
            <?php else: ?>
                <button class="cart-button" onclick="redirectToLogin('basket');" title="Увійти для доступу до кошика">
                    <i class="fas fa-shopping-cart"></i>
                </button>
            <?php endif; ?>
        </div>

        <div class="header-center">
            <div class="category-menu">
                <a href="category-list-women.php" class="category-link women-category">Жіночий</a>
                <div class="dropdown-menu women-menu">
                    <div class="dropdown-section">
                        <h4>Bepx</h4>
                        <a href="catalog.php?category=f&type=tshirts" class="dropdown-item">
                            <i class="fas fa-tshirt"></i> Футболки
                        </a>
                        <a href="catalog.php?category=f&type=hoodies" class="dropdown-item">
                            <i class="fas fa-vest"></i> Рубашки
                        </a>
                    </div>
                    <div class="dropdown-section">
                        <h4>Низ</h4>
                        <a href="catalog.php?category=f&type=pants" class="dropdown-item">
                            <i class="fas fa-female"></i> Штани
                        </a>
                        <a href="catalog.php?category=f&type=shorts" class="dropdown-item">
                            <i class="fas fa-running"></i> Шорти
                        </a>
                    </div>
                    <div class="dropdown-section">
                        <h4>Аксесуари</h4>
                        <a href="catalog.php?category=f&type=accessories" class="dropdown-item">
                            <i class="fas fa-glasses"></i> Окуляри
                        </a>
                    </div>
                </div>
            </div>

            <div class="logo-container">
                <a href="index.php">
                    <img src="images/logo/logo-gustavo.png" alt="GUSTAVO logo">
                </a>
            </div>

            <div class="category-menu">
                <a href="category-list-man.php" class="category-link men-category">Чоловічий</a>
                <div class="dropdown-menu men-menu">
                    <div class="dropdown-section">
                        <h4>Bepx</h4>
                        <a href="catalog.php?category=m&type=tshirts" class="dropdown-item">
                            <i class="fas fa-tshirt"></i> Футболки
                        </a>
                        <a href="catalog.php?category=m&type=hoodies" class="dropdown-item">
                            <i class="fas fa-vest"></i> Рубашки
                        </a>
                    </div>
                    <div class="dropdown-section">
                        <h4>Низ</h4>
                        <a href="catalog.php?category=m&type=pants" class="dropdown-item">
                            <i class="fas fa-user-tie"></i> Штани
                        </a>
                        <a href="catalog.php?category=m&type=shorts" class="dropdown-item">
                            <i class="fas fa-running"></i> Шорти
                        </a>
                    </div>
                    <div class="dropdown-section">
                        <h4>Аксесуари</h4>
                        <a href="catalog.php?category=m&type=accessories" class="dropdown-item">
                            <i class="fas fa-glasses"></i> Окуляри
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="header-right">
            <?php if ($is_logged_in): ?>
                <!-- авторизований користувач -->
                <div class="user-info" onclick="location.href='account.php';" title="Перейти в акаунт">
                    <div class="user-avatar-container">
                        <img src="<?php echo htmlspecialchars($user_avatar); ?>" alt="User Avatar" class="user-avatar">
                    </div>
                    <span class="user-login"><?php echo htmlspecialchars($user_data['login']); ?></span>
                </div>
            <?php else: ?>
                <!-- неавторизований користувач -->
                <button class="account-button" onclick="location.href='login.php';" title="Увійти в акаунт">
                    <i class="fas fa-user"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>


document.addEventListener('DOMContentLoaded', function() {
    const categoryMenus = document.querySelectorAll('.category-menu');
    
    categoryMenus.forEach(menu => {
        const dropdown = menu.querySelector('.dropdown-menu');
        let timeout;
        
        menu.addEventListener('mouseenter', function() {
            clearTimeout(timeout);
            dropdown.style.opacity = '1';
            dropdown.style.visibility = 'visible';
        });
        
        menu.addEventListener('mouseleave', function() {
            timeout = setTimeout(() => {
                dropdown.style.opacity = '0';
                dropdown.style.visibility = 'hidden';
            }, 200);
        });
    });
});
</script>

<?php
include 'vendor/notification.php';
?>
