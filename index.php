<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logo/logo-gustavo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="style-css/style-header-footer2.css">
    <link rel="stylesheet" href="style-css/style-index2.css">
    <title>GUSTAVO - стильний одяг для тебе</title>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Система сповіщень -->
    <?php include 'vendor/notification.php'; ?>

    <!-- Hero секція з великим фоном -->
    <section class="hero-section">
        <div class="hero-background">
            <!-- Тут буде фоновое изображение через CSS -->
        </div>
        <div class="hero-content">
            <div class="hero-text">
                <h1>Обери свій стиль прямо зараз!</h1>
                <p>Відкрий для ce6e унікальну колекцію одягу GUSTAVO</p>
            </div>
            
            <!-- Область для змінних картинок товарів -->
            <div class="product-showcase">
                <img id="rotating-product" src="images/products/product-slider1.png" alt="GUSTAVO Product" class="showcase-image">
            </div>
            
            <!-- Дві головні кнопки -->
            <div class="hero-buttons">
                <button class="hero-btn women-btn" onclick="location.href='category-list-women.php';">
                    <i class="fas fa-female"></i>
                    <span>для неї</span>
                </button>
                <button class="hero-btn men-btn" onclick="location.href='category-list-man.php';">
                    <i class="fas fa-male"></i>
                    <span>для нього</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Основний контент -->
    <div class="main-content">
        
        <!-- Історія бренду -->
        <section class="brand-story">
            <div class="story-container">
                <div class="story-image">
                    <img src="images/logo/logo-gustavo.png" alt="GUSTAVO Brand Story" class="brand-image">
                </div>
                <div class="story-text">
                    <h2>GUSTAVO SHTANINNI</h2>
                    <div class="story-content">
                        <p>Gustavo Shtaninni — це бренд, що народився зі сміливої ідеї створити одяг, який не просто зручний, a говорить за тебе.</p>
                        
                        <p>Бренд з італійським вайбом, що поєднує грайливий стиль, вуличну моду та мінімалізм. Ми не про костюми з подіумів Мілана.</p>
                        
                        <p>Ми про одяг, який вдягають ті, хто живе швидко, рухається вулицею з музикою в навушниках i не боїться виглядати абсурдно стильно.</p>
                        
                        <p>Наші речі створені для тих, кому 15-25, хто хоче виглядати легко, трохи нахабно, але по-своєму — без зайвого пафосу.</p>
                        
                        <p><strong>Shtaninni — це коли одяг, але на максимумі стилю.</strong></p>
                    </div>
                </div>
            </div>
        </section>
        
        <hr class="section-divider">
        
        <!-- Галерея товарів -->
        <section class="product-gallery">
            <div class="gallery-header">
                <h2>Модельний ряд</h2>
                <p class="gallery-subtitle">Наші найкращі моделі одягу</p>
            </div>
            
            <div class="models-grid">
                <div class="model-item">
                    <img src="images/models/model-section1.jpg" alt="GUSTAVO Model 1" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;model-placeholder&quot;><i class=&quot;fas fa-tshirt&quot;></i><span>Модель 1</span></div>';">
                </div>
                <div class="model-item">
                    <img src="images/models/model-section2.jpg" alt="GUSTAVO Model 2" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;model-placeholder&quot;><i class=&quot;fas fa-hoodie&quot;></i><span>Модель 2</span></div>';">
                </div>
                <div class="model-item">
                    <img src="images/models/model-section3.jpg" alt="GUSTAVO Model 3" onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;model-placeholder&quot;><i class=&quot;fas fa-vest&quot;></i><span>Модель 3</span></div>';">
                </div>
            </div>
        </section>
        
        <hr class="section-divider">
        
        <!-- Покращені відгуки -->
        <section class="reviews-section">
            <h2>Що кажуть наші клієнти</h2>
            <div class="reviews-grid">
                <div class="review-card">
                    <div class="review-header">
                        <img src="images/reviews/reviews1.jpg" alt="Оксана K." class="reviewer-avatar">
                        <div class="reviewer-info">
                            <h4>Оксана K.</h4>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"Замовляла футболку для сина-підлітка. Якість просто супер! Тканина м'яка, шви акуратні. Син в захваті, каже що це найкраща футболка в його гардеробі."</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <img src="images/reviews/reviews2.jpg" alt="Дмитро B." class="reviewer-avatar">
                        <div class="reviewer-info">
                            <h4>Дмитро B.</h4>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"Купив худі місяць тому - ношу постійно. Після 10 прань виглядає як нове. Однозначно буду замовляти ще!"</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <img src="images/reviews/reviews3.jpg" alt="Марія C." class="reviewer-avatar">
                        <div class="reviewer-info">
                            <h4>Марія C.</h4>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"Штани сіли ідеально, матеріал приємний до тіла. Дизайн стильний i простий, рекомендую!."</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <img src="images/reviews/reviews4.jpg" alt="Андрій Л." class="reviewer-avatar">
                        <div class="reviewer-info">
                            <h4>Андрій Л.</h4>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"Замовляв футболку через сайт - все просто i зрозуміло. Розмір підійшов точно, якість на висоті. Ціна супер за таку якість. Рекомендую!"</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <img src="images/reviews/reviews5.jpg" alt="Софія M." class="reviewer-avatar">
                        <div class="reviewer-info">
                            <h4>Софія M.</h4>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"Купувала подарунок хлопцю. Він в захваті від худі! Каже, що дуже зручно і виглядає стильно. Буду обов'язково замовляти щось для себе."</p>
                </div>

                <div class="review-card">
                    <div class="review-header">
                        <img src="images/reviews/reviews6.jpg" alt="Олег P." class="reviewer-avatar">
                        <div class="reviewer-info">
                            <h4>Олег P.</h4>
                            <div class="rating">
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                                <span class="star">★</span>
                            </div>
                        </div>
                    </div>
                    <p class="review-text">"Брав шорти на літо. 3a таку ціну це просто ідеальна якість!"</p>
                </div>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Ротація товарних картинок
        document.addEventListener('DOMContentLoaded', function() {
            const productImages = [
                'images/products/product-slider1.png',
                'images/products/product-slider2.png',
                'images/products/product-slider3.png',
                'images/products/product-slider4.png',
                'images/products/product-slider5.png',
                'images/products/product-slider6.png',
                'images/products/product-slider7.png',
                'images/products/product-slider8.png',
                'images/products/product-slider9.png'
            ];
            
            let currentImageIndex = 0;
            const rotatingProduct = document.getElementById('rotating-product');
            
            // Функція ротації
            function rotateImage() {
                rotatingProduct.style.opacity = '0';
                
                setTimeout(() => {
                    rotatingProduct.src = productImages[currentImageIndex];
                    rotatingProduct.style.opacity = '1';
                }, 300);
            }
            
            setInterval(() => {
                currentImageIndex = (currentImageIndex + 1) % productImages.length;
                rotateImage();
            }, 1000);

            // Анімація появи елементів при скролі
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);

            // Спостерігаємо за елементами для анімації
            document.querySelectorAll('.review-card, .product-card, .story-container').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>