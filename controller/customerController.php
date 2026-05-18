<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

function admin_customers() {
    $conn = getConnection();
    $error   = '';
    $success = '';

    // DELETE customer (POST + CSRF)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
        csrf_require();
        $id = intval($_POST['id'] ?? 0);

        $stmt = mysqli_prepare($conn, 'DELETE FROM cart WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $orderIds = [];
        $res = mysqli_prepare($conn, 'SELECT id FROM orders WHERE user_id = ?');
        mysqli_stmt_bind_param($res, 'i', $id);
        mysqli_stmt_execute($res);
        $result = mysqli_stmt_get_result($res);
        while ($row = mysqli_fetch_assoc($result)) {
            $orderIds[] = (int) $row['id'];
        }
        mysqli_stmt_close($res);

        foreach ($orderIds as $oid) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM order_items WHERE order_id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $oid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare($conn, 'DELETE FROM payments WHERE order_id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $oid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        $stmt = mysqli_prepare($conn, 'DELETE FROM orders WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'customer'");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = 'Customer deleted successfully.';
    }

    $result    = mysqli_query($conn, "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC");
    $customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

    require_once 'views/admin/customers.php';
}
