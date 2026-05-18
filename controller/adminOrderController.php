<?php

function admin_orders() {
    $conn = getConnection();

    // fetch all orders with customer name
    $result = mysqli_query($conn, "SELECT o.*, u.name as customer_name 
                                   FROM orders o 
                                   JOIN users u ON o.user_id = u.id 
                                   ORDER BY o.order_date DESC");
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    require_once 'views/admin/orders.php';
}

// AJAX endpoint — update order status
function admin_order_update_status() {
    header('Content-Type: application/json');
    require_once 'config/admin_auth.php';
    require_once 'config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit();
    }

    csrf_require();
    $conn = getConnection();

    $data     = json_decode(file_get_contents('php://input'), true);
    $id       = intval($data['order_id'] ?? 0);
    $status   = $data['status'] ?? '';

    $allowed = ['accepted', 'rejected'];
    if ($id === 0 || !in_array($status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
        exit();
    }

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);

    echo json_encode(['success' => true, 'status' => $status]);
    exit();
}

// purchase history — accepted orders only
function admin_purchase_history() {
    $conn = getConnection();

    $result = mysqli_query($conn, "SELECT o.*, u.name as customer_name 
                                   FROM orders o 
                                   JOIN users u ON o.user_id = u.id 
                                   WHERE o.status = 'accepted' 
                                   ORDER BY o.order_date DESC");
    $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // get order items for each order
    $order_items = [];
    foreach ($orders as $order) {
        $oid  = (int) $order['id'];
        $stmt = mysqli_prepare($conn, "
            SELECT oi.*, m.name AS medicine_name
            FROM order_items oi
            JOIN medicines m ON oi.medicine_id = m.id
            WHERE oi.order_id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'i', $oid);
        mysqli_stmt_execute($stmt);
        $order_items[$oid] = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
    }

    require_once 'views/admin/purchase_history.php';
}