<?php
session_start();


$_SESSION['user_id'] = 1;
$_SESSION['name']    = 'Test Customer';
$_SESSION['role']    = 'customer';

echo "✅ You are now logged in as a test customer!<br>";
echo '<a href="index.php?page=cart">Go to Cart →</a>';