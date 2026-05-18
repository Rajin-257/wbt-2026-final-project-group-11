<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

function admin_dashboard() {
    $conn = getConnection();

    $medicines  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM medicines"))[0];
    $categories = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM categories"))[0];
    $customers  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='customer'"))[0];
    $pending    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE status='pending'"))[0];

    require_once 'views/admin/dashboard.php';
}