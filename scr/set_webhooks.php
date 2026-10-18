<?php
// Đặt token của bot Telegram
$token = "7904727562:AAEUYkLeLYl_qWJM7Fwe0VLC-TVB6H253pA";

// Địa chỉ URL webhook của bạn
$webhookUrl = "https://dichvudev.hicam.net/webhooks.php"; // Thay bằng URL chính thức của bạn

// Telegram API URL để thiết lập webhook
$apiUrl = "https://api.telegram.org/bot" . $token . "/setWebhook?url=" . $webhookUrl;

// Gửi yêu cầu cài đặt webhook
$response = file_get_contents($apiUrl);

// Kiểm tra phản hồi từ Telegram
if ($response) {
    echo "Webhook đã được thiết lập thành công!";
} else {
    echo "Lỗi khi thiết lập webhook!";
}
?>
