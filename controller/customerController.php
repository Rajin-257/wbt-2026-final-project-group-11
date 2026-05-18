<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

function admin_customers() {
    $conn = getConnection();
    $error   = '';
    $success = '';

    // DELETE customer
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);

        // cascade delete cart and orders first
        $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'customer'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $success = "Customer deleted successfully.";
    }

    // fetch all customers
    $result    = mysqli_query($conn, "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC");
    $customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

    require_once 'views/admin/customers.php';
}