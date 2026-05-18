<?php

require_once __DIR__ . '/../config/db.php';

class OrderModel
{
    public function placeOrder(int $userId, string $shippingAddress, string $paymentMethod, array $cartItems, float $totalAmount): int
    {
        $conn = getConnection();
        mysqli_begin_transaction($conn);

        try {
            foreach ($cartItems as $item) {
                $mid = (int) $item['medicine_id'];
                $stmt = mysqli_prepare($conn, 'SELECT availability FROM medicines WHERE id = ? FOR UPDATE');
                mysqli_stmt_bind_param($stmt, 'i', $mid);
                mysqli_stmt_execute($stmt);
                $stockRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);

                if (!$stockRow || (int) $item['quantity'] > (int) $stockRow['availability']) {
                    throw new Exception("Insufficient stock for '{$item['name']}'.");
                }
            }

            $status = 'pending';
            $stmt = mysqli_prepare($conn, "
                INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_method)
                VALUES (?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt, 'idsss', $userId, $totalAmount, $shippingAddress, $status, $paymentMethod);
            mysqli_stmt_execute($stmt);
            $orderId = (int) mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            $itemStmt = mysqli_prepare($conn, '
                INSERT INTO order_items (order_id, medicine_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)
            ');

            foreach ($cartItems as $item) {
                $mid   = (int) $item['medicine_id'];
                $qty   = (int) $item['quantity'];
                $price = (float) $item['price'];
                mysqli_stmt_bind_param($itemStmt, 'iiid', $orderId, $mid, $qty, $price);
                mysqli_stmt_execute($itemStmt);

                $upd = mysqli_prepare($conn, 'UPDATE medicines SET availability = availability - ? WHERE id = ?');
                mysqli_stmt_bind_param($upd, 'ii', $qty, $mid);
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);
            }
            mysqli_stmt_close($itemStmt);

            $txnId = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));
            $payStmt = mysqli_prepare($conn, '
                INSERT INTO payments (order_id, amount, payment_method, transaction_id)
                VALUES (?, ?, ?, ?)
            ');
            mysqli_stmt_bind_param($payStmt, 'idss', $orderId, $totalAmount, $paymentMethod, $txnId);
            mysqli_stmt_execute($payStmt);
            mysqli_stmt_close($payStmt);

            $clear = mysqli_prepare($conn, 'DELETE FROM cart WHERE user_id = ?');
            mysqli_stmt_bind_param($clear, 'i', $userId);
            mysqli_stmt_execute($clear);
            mysqli_stmt_close($clear);

            mysqli_commit($conn);
            mysqli_close($conn);
            return $orderId;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            mysqli_close($conn);
            throw $e;
        }
    }

    public function getCustomerOrders(int $userId): array
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, "
            SELECT o.*, p.transaction_id
            FROM orders o
            LEFT JOIN payments p ON p.order_id = o.id
            WHERE o.user_id = ?
            ORDER BY o.order_date DESC
        ");
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $rows;
    }

    public function getOrderWithItems(int $orderId, int $userId): ?array
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, "
            SELECT o.*, p.transaction_id
            FROM orders o
            LEFT JOIN payments p ON p.order_id = o.id
            WHERE o.id = ? AND o.user_id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $orderId, $userId);
        mysqli_stmt_execute($stmt);
        $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$order) {
            mysqli_close($conn);
            return null;
        }

        $stmt = mysqli_prepare($conn, "
            SELECT oi.*, m.name, m.vendor_name, m.image_path
            FROM order_items oi
            JOIN medicines m ON oi.medicine_id = m.id
            WHERE oi.order_id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'i', $orderId);
        mysqli_stmt_execute($stmt);
        $order['items'] = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $order;
    }
}
