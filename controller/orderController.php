<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

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
        $res = mysqli_query($conn, "SELECT oi.*, m.name as medicine_name 
                                    FROM order_items oi 
                                    JOIN medicines m ON oi.medicine_id = m.id 
                                    WHERE oi.order_id = " . $order['id']);
        $order_items[$order['id']] = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    require_once 'views/admin/purchase_history.php';
}