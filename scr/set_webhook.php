<?php
// Token của bot Telegram
$token = "7893895483:AAFx8gx4KqG0wnwjvL1bpV-qkQDCdDIMXD8";

// URL của webhook (đã thay thành tên miền của bạn)
$webhook_url = "https://dichvudev.hicam.net/webhook.php"; // URL của file webhook.php trên tên miền của bạn

// Thiết lập webhook cho bot
$response = file_get_contents("https://api.telegram.org/bot$token/setWebhook?url=$webhook_url");

// Hiển thị kết quả để biết webhook có thiết lập thành công không
echo $response;
?>
