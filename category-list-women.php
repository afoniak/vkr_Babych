<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo/logo-gustavo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style-css/style-header-footer2.css">
    <link rel="stylesheet" href="style-css/style.css">
    <link rel="stylesheet" href="style-css/style-category-list.css">
    <title>GUSTAVO - Жіночий одяг</title>
</head>
<body data-category="women">
    <?php include 'header.php'; ?>

    <?php include 'vendor/notification.php'; ?>

    <main class="category-main">
        <div class="category-container">
            <div class="category-header">
                <h1 class="category-title">Жіночий одяг</h1>
                <p class="category-subtitle">Обери свій стиль з нашої колекції для жінок</p>
            </div>

            <section class="category-section">
                <h2 class="section-title">Bepx</h2>
                
                <div class="subcategory-grid">
                    <div class="subcategory-card subcategory-tshirts" onclick="location.href='catalog.php?category=f&type=tshirts';">
                        <div class="subcategory-content">
                            <h3 class="subcategory-name">ФУТБОЛКИ</h3>
                        </div>
                    </div>

                    <div class="subcategory-card subcategory-hoodies" onclick="location.href='catalog.php?category=f&type=hoodies';">
                        <div class="subcategory-content">
                            <h3 class="subcategory-name">РУБАШКИ</h3>
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="category-section">
                <h2 class="section-title">Низ</h2>
                
                <div class="subcategory-grid">
                    <div class="subcategory-card subcategory-pants" onclick="location.href='catalog.php?category=f&type=pants';">
                        <div class="subcategory-content">
                            <h3 class="subcategory-name">ШТАНИ</h3>
                        </div>
                    </div>

                    <div class="subcategory-card subcategory-shorts" onclick="location.href='catalog.php?category=f&type=shorts';">
                        <div class="subcategory-content">
                            <h3 class="subcategory-name">ШОРТИ</h3>
                        </div>
                    </div>
                </div>
            </section>

            
            <section class="category-section">
                <h2 class="section-title">Аксесуари</h2>
                
                <div class="subcategory-grid">
                    <div class="subcategory-card subcategory-accessories" onclick="location.href='catalog.php?category=f&type=accessories';">
                        <div class="subcategory-content">
                            <h3 class="subcategory-name">ОКУЛЯРИ</h3>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include 'footer.php'; ?>


</body>
</html>
