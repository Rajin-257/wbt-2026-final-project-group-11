<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// auto-login from remember-me cookie if the function is available
if (function_exists('try_remember_me')) try_remember_me();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Medicine Shop') ?></title>
    <link rel="stylesheet" href="public/style.css">
    <?php include __DIR__ . '/csrf_head.php'; ?>
    <script src="public/xss.js"></script>
</head>
<body>
