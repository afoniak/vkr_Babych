<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendOrderEmail($email, $order_id, $items, $total) {
    $mail = new PHPMailer(true);

    try {
        // smtp налаштування для gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = '$$$';
        $mail->Password   = '$$$';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        // налаштування листа
        $mail->setFrom('catpeach3454@gmail.com', 'GUSTAVO Store');
        $mail->addAddress($email);
        $mail->Subject = "Замовлення #$order_id - GUSTAVO";

        // html вміст
        $message = buildEmailMessage($order_id, $items, $total);
        $mail->isHTML(true);
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}

function buildEmailMessage($order_id, $items, $total) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #fff; }
            .header { background: #f8f9fa; color: #333; padding: 30px; text-align: center; border-bottom: 1px solid #eee; }
            .content { padding: 30px; max-width: 600px; margin: 0 auto; }
            .order-details { background: #f8f9fa; padding: 20px; border: 1px solid #eee; margin: 20px 0; }
            .item { border-bottom: 1px solid #eee; padding: 12px 0; }
            .item:last-child { border-bottom: none; }
            .total { font-weight: bold; font-size: 18px; color: #333; text-align: right; margin-top: 15px; padding-top: 15px; border-top: 2px solid #333; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #666; border-top: 1px solid #eee; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>GUSTAVO</h1>
            <p>Дякуємо за ваше замовлення!</p>
        </div>
        
        <div class="content">
            <h2>Замовлення #' . $order_id . '</h2>
            <p>Вітаємо! Ваше замовлення прийнято в обробку.</p>
            
            <div class="order-details">
                <h3>Деталі замовлення:</h3>';
    
    foreach ($items as $item) {
        $item_total = $item['price'] * $item['quantity'];
        $html .= '
                <div class="item">
                    <strong>' . htmlspecialchars($item['product_name']) . '</strong><br>
                    Розмір: ' . htmlspecialchars($item['size']) . ' | Кількість: ' . $item['quantity'] . ' | 
                    Ціна: ₴' . number_format($item['price'], 0, ',', ' ') . ' | 
                    Сума: ₴' . number_format($item_total, 0, ',', ' ') . '
                </div>';
    }
    
    $html .= '
                <div class="total">
                    Загальна сума: ₴' . number_format($total, 0, ',', ' ') . '
                </div>
            </div>
            
            <p><strong>Що далі?</strong></p>
            <ul>
                <li>Товар буде відправлений протягом 1-2 робочих днів</li>
                <li>Наш менеджер зв\'яжеться з вами для уточнення деталей доставки</li>
                <li>Ви отримаєте SMS з номером для відстеження</li>
            </ul>
        </div>
        
        <div class="footer">
            <p>3 повагою, команда GUSTAVO<br>
            Email: catpeach3454@gmail.com</p>
        </div>
    </body>
    </html>';
    
    return $html;
}
?> 
