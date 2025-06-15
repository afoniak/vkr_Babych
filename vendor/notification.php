<?php
// перевіряємо чи є сповіщення в сесії
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    $type = $notification['type'];
    $message = $notification['message'];
    
    // визначаємо іконку в залежності від типу
    switch($type) {
        case 'success':
            $icon = 'fas fa-check-circle';
            break;
        case 'error':
            $icon = 'fas fa-times-circle';
            break;
        case 'warning':
            $icon = 'fas fa-exclamation-triangle';
            break;
        case 'info':
            $icon = 'fas fa-info-circle';
            break;
        default:
            $icon = 'fas fa-bell';
            break;
    }
    ?>
    
 
    <style>

    .notification-overlay {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 9999;
        pointer-events: none;
        display: flex;
        justify-content: center;
        padding: 30px;
    }

    .notification {
        position: relative;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 20px 30px;
        max-width: 500px;
        width: 100%;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            0 4px 16px rgba(0, 0, 0, 0.05),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transform: translateY(100px);
        opacity: 0;
        animation: slideUpGlass 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        pointer-events: all;
        overflow: hidden;
    }

    .notification::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, 
            rgba(255, 255, 255, 0.3) 0%, 
            rgba(255, 255, 255, 0.1) 50%, 
            rgba(255, 255, 255, 0.3) 100%);
        border-radius: 16px 16px 0 0;
    }

    .notification.success {
        background: rgba(40, 167, 69, 0.15);
        border-color: rgba(40, 167, 69, 0.3);
        color: #1e7e34;
    }

    .notification.success::before {
        background: linear-gradient(90deg, 
            rgba(40, 167, 69, 0.8) 0%, 
            rgba(40, 167, 69, 0.4) 50%, 
            rgba(40, 167, 69, 0.8) 100%);
    }

    .notification.error {
        background: rgba(220, 53, 69, 0.15);
        border-color: rgba(220, 53, 69, 0.3);
        color: #721c24;
    }

    .notification.error::before {
        background: linear-gradient(90deg, 
            rgba(220, 53, 69, 0.8) 0%, 
            rgba(220, 53, 69, 0.4) 50%, 
            rgba(220, 53, 69, 0.8) 100%);
    }

    .notification.warning {
        background: rgba(255, 193, 7, 0.15);
        border-color: rgba(255, 193, 7, 0.3);
        color: #856404;
    }

    .notification.warning::before {
        background: linear-gradient(90deg, 
            rgba(255, 193, 7, 0.8) 0%, 
            rgba(255, 193, 7, 0.4) 50%, 
            rgba(255, 193, 7, 0.8) 100%);
    }

    .notification.info {
        background: rgba(23, 162, 184, 0.15);
        border-color: rgba(23, 162, 184, 0.3);
        color: #0c5460;
    }

    .notification.info::before {
        background: linear-gradient(90deg, 
            rgba(23, 162, 184, 0.8) 0%, 
            rgba(23, 162, 184, 0.4) 50%, 
            rgba(23, 162, 184, 0.8) 100%);
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .notification-icon {
        font-size: 24px;
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }

    .notification.success .notification-icon {
        background: rgba(40, 167, 69, 0.2);
        color: #155724;
    }

    .notification.error .notification-icon {
        background: rgba(220, 53, 69, 0.2);
        color: #721c24;
    }

    .notification.warning .notification-icon {
        background: rgba(255, 193, 7, 0.2);
        color: #856404;
    }

    .notification.info .notification-icon {
        background: rgba(23, 162, 184, 0.2);
        color: #0c5460;
    }

    .notification-text {
        flex: 1;
        font-size: 16px;
        font-weight: 500;
        line-height: 1.4;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .notification-close {
        background: none;
        border: none;
        color: inherit;
        font-size: 20px;
        cursor: pointer;
        padding: 5px;
        border-radius: 50%;
        transition: all 0.3s ease;
        opacity: 0.7;
        flex-shrink: 0;
    }

    .notification-close:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.1);
    }

    @keyframes slideUpGlass {
        0% {
            transform: translateY(100px);
            opacity: 0;
        }
        100% {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideDownGlass {
        0% {
            transform: translateY(0);
            opacity: 1;
        }
        100% {
            transform: translateY(100px);
            opacity: 0;
        }
    }

    /* адаптивні стилі */
    @media (max-width: 768px) {
        .notification-overlay {
            padding: 20px;
        }
        
        .notification {
            padding: 15px 20px;
            border-radius: 12px;
        }
        
        .notification-content {
            gap: 12px;
        }
        
        .notification-icon {
            width: 35px;
            height: 35px;
            font-size: 20px;
        }
        
        .notification-text {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .notification-overlay {
            padding: 15px;
        }
        
        .notification {
            padding: 12px 16px;
        }
        
        .notification-text {
            font-size: 13px;
        }
    }
    </style>

    <!-- html для сповіщення -->
    <div class="notification-overlay" id="notificationOverlay">
        <div class="notification <?= $type; ?>" id="notification">
            <div class="notification-content">
                <div class="notification-icon">
                    <i class="<?= $icon; ?>"></i>
                </div>
                <div class="notification-text">
                    <?= htmlspecialchars($message); ?>
                </div>
                <button class="notification-close" onclick="closeNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
    // автоматично закриваємо сповіщення через 5 секунд
    setTimeout(function() {
        closeNotification();
    }, 5000);

    function closeNotification() {
        const notification = document.getElementById('notification');
        const overlay = document.getElementById('notificationOverlay');
        
        if (notification && overlay) {
            notification.style.animation = 'slideDownGlass 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 400);
        }
    }

    // закриваємо сповіщення при кліку поза ним
    document.addEventListener('click', function(e) {
        const notification = document.getElementById('notification');
        const overlay = document.getElementById('notificationOverlay');
        
        if (overlay && e.target === overlay) {
            closeNotification();
        }
    });
    </script>

    <?php
    // очищаємо сповіщення з сесії після показу
    unset($_SESSION['notification']);
}

// перевіряємо сповіщення авторизації
if (isset($_SESSION['auth_message'])) {
    $auth_message = $_SESSION['auth_message'];
    $type = $auth_message['type'];
    $message = $auth_message['message'];
    
    // визначаємо іконку
    switch($type) {
        case 'success':
            $icon = 'fas fa-check-circle';
            break;
        case 'error':
            $icon = 'fas fa-times-circle';
            break;
        default:
            $icon = 'fas fa-bell';
            break;
    }
    ?>
    
    <!-- html для сповіщення авторизації -->
    <div class="notification-overlay" id="authNotificationOverlay">
        <div class="notification <?= $type; ?>" id="authNotification">
            <div class="notification-content">
                <div class="notification-icon">
                    <i class="<?= $icon; ?>"></i>
                </div>
                <div class="notification-text">
                    <?= htmlspecialchars($message); ?>
                </div>
                <button class="notification-close" onclick="closeAuthNotification()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
    // автоматично закриваємо сповіщення авторизації через 5 секунд
    setTimeout(function() {
        closeAuthNotification();
    }, 5000);

    function closeAuthNotification() {
        const notification = document.getElementById('authNotification');
        const overlay = document.getElementById('authNotificationOverlay');
        
        if (notification && overlay) {
            notification.style.animation = 'slideDownGlass 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 400);
        }
    }
    </script>

    <?php
    // очищаємо сповіщення авторизації з сесії після показу
    unset($_SESSION['auth_message']);
}
?>
