<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'для доступу до особистого кабінету потрібно авторизуватися!'
    ];
    header('Location: login.php');
    exit();
}

$user_data = $_SESSION['user'];

// отримуємо повну інформацію про користувача з БД
require_once 'connect-db.php';
$stmt = mysqli_prepare($connect, "SELECT login, email, image_account, created_at FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_data['id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $user_login = $row['login'];
    $user_email = $row['email'];
    $user_avatar = $row['image_account'] ? $row['image_account'] : 'images/account/default.jpg';
    $user_created = $row['created_at'];
} else {
    session_destroy();
    header('Location: login.php');
    exit();
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
    <link rel="stylesheet" href="style-css/style-account-user.css">
    <title>GUSTAVO - <?= htmlspecialchars($user_login); ?></title>
</head>

<body>
    <?php include 'header.php'; ?>

    <?php include 'vendor/notification.php'; ?>

    <div class="account-main-container">
        <h1 class="account-title">Особистий кабінет</h1>
        
        <div class="info-container">
            <div class="profile-header">
                <form method="POST" action="vendor/logout.php" style="display: inline;">
                <button type="submit" class="logout-button" title="Вийти з акаунта">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Вийти</span>
                </button>
                </form>
            </div>
            
            <div class="profile-info">
                <div class="avatar-section">
                    <img src="<?= htmlspecialchars($user_avatar); ?>" alt="User Image" class="user-image">
                    <button id="button-photo-account" class="button-photo-account" onclick="togglePhotoContainer()">Змінити фото</button>
            </div>
                <div class="text-info">
                    <p>Логін: <strong><?= htmlspecialchars($user_login); ?></strong></p>
                    <p>Електронна пошта: <strong><?= htmlspecialchars($user_email); ?></strong></p>
                    <p>Дата реєстрації: <strong><?= date('d.m.Y H:i', strtotime($user_created)); ?></strong></p>
        </div>
        </div>
        <div class="buttons">
            <button id="button-account-password" class="button-account-password"
                        onclick="togglePasswordContainer()">Змінити пароль</button>
        </div>
    </div>

    <div id="password-container" class="update-container" style="display: none;">
            <form action="vendor/update-password.php" method="post">
                <label>Поточний пароль</label>
                <input type="password" name="current_password" placeholder="Введіть поточний пароль" required>
                <label>Новий пароль</label>
                <input type="password" name="new_password" placeholder="Введіть новий пароль (мінімум 6 символів)" required>
            <label>Підтвердження пароля</label>
                <input type="password" name="confirm_password" placeholder="Підтвердіть новий пароль" required>
            <button type="submit">Підтвердити зміну пароля</button>
        </form>
    </div>

    <div id="photo-container" class="update-container" style="display: none;">
            <form action="vendor/update-photo.php" method="post" enctype="multipart/form-data">
                <label>Оберіть фото для аватара</label>
                <div class="file-input-container">
                    <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png,.gif">
                    <label for="photo" class="file-input-label" id="file-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Натисніть для вибору файлу a6o перетягніть сюди</span>
                    </label>
                </div>
                <small style="color: #6c757d; margin-bottom: 15px; display: block;">Підтримуються формати: .jpg, .jpeg, .png, .gif (макс. 5MБ)</small>
            <button type="submit" name="submit">Змінити фото</button>
        </form>
        </div>
    </div>

    <script>
        // покращення роботи input file
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('photo');
            const fileLabel = document.getElementById('file-label');
            
            if (fileInput && fileLabel) {
                fileInput.addEventListener('change', function(e) {
                    const fileName = e.target.files[0]?.name;
                    const fileSize = e.target.files[0]?.size;
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    
                    if (fileName) {
                        if (fileSize > maxSize) {
                            fileLabel.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Файл занадто великий (макс. 5MБ)</span>';
                            fileLabel.classList.remove('file-selected');
                            fileLabel.style.backgroundColor = '#f8d7da';
                            fileLabel.style.borderColor = '#dc3545';
                            fileLabel.style.color = '#721c24';
                            fileInput.value = '';
                        } else {
                            fileLabel.innerHTML = `<i class="fas fa-check-circle"></i><span>Обрано: ${fileName}</span>`;
                            fileLabel.classList.add('file-selected');
                        }
                    } else {
                        fileLabel.innerHTML = '<i class="fas fa-cloud-upload-alt"></i><span>Натисніть для вибору файлу a6o перетягніть сюди</span>';
                        fileLabel.classList.remove('file-selected');
                    }
                });

                // функціональність drag and drop
                fileLabel.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    fileLabel.style.transform = 'translateY(-2px) scale(1.02)';
                    fileLabel.style.borderColor = '#6c757d';
                });

                fileLabel.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    fileLabel.style.transform = '';
                    fileLabel.style.borderColor = '#adb5bd';
                });

                fileLabel.addEventListener('drop', function(e) {
                    e.preventDefault();
                    fileLabel.style.transform = '';
                    fileLabel.style.borderColor = '#adb5bd';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        fileInput.files = files;
                        fileInput.dispatchEvent(new Event('change'));
                    }
                });
            }
        });

        function togglePasswordContainer() {
            const passwordContainer = document.getElementById('password-container');
            const photoContainer = document.getElementById('photo-container');
            
            // ховаємо фото контейнер якщо він відкритий
            if (photoContainer.style.display === 'block') {
                photoContainer.style.opacity = '0';
                photoContainer.style.transform = 'translateY(-20px) scale(0.95)';
                setTimeout(() => {
                    photoContainer.style.display = 'none';
                }, 300);
            }
            
            if (passwordContainer.style.display === 'none' || passwordContainer.style.display === '') {
                passwordContainer.style.display = 'block';
                passwordContainer.style.opacity = '0';
                passwordContainer.style.transform = 'translateY(-20px) scale(0.95)';
                
                setTimeout(() => {
                    passwordContainer.style.opacity = '1';
                    passwordContainer.style.transform = 'translateY(0) scale(1)';
                }, 10);
            } else {
                passwordContainer.style.opacity = '0';
                passwordContainer.style.transform = 'translateY(-20px) scale(0.95)';
                setTimeout(() => {
                    passwordContainer.style.display = 'none';
                }, 300);
            }
        }

        function togglePhotoContainer() {
            const photoContainer = document.getElementById('photo-container');
            const passwordContainer = document.getElementById('password-container');
            
            // ховаємо пароль контейнер якщо він відкритий
            if (passwordContainer.style.display === 'block') {
                passwordContainer.style.opacity = '0';
                passwordContainer.style.transform = 'translateY(-20px) scale(0.95)';
                setTimeout(() => {
                    passwordContainer.style.display = 'none';
                }, 300);
            }
            
            if (photoContainer.style.display === 'none' || photoContainer.style.display === '') {
                photoContainer.style.display = 'block';
                photoContainer.style.opacity = '0';
                photoContainer.style.transform = 'translateY(-20px) scale(0.95)';
                
                setTimeout(() => {
                    photoContainer.style.opacity = '1';
                    photoContainer.style.transform = 'translateY(0) scale(1)';
                }, 10);
            } else {
                photoContainer.style.opacity = '0';
                photoContainer.style.transform = 'translateY(-20px) scale(0.95)';
                setTimeout(() => {
                    photoContainer.style.display = 'none';
                }, 300);
            }
        }
    </script>
    
    <?php include 'footer.php'; ?>
</body>
</html>