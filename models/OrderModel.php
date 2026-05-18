<?php
// models/OrderModel.php

require_once ROOT_PATH . '/config/database.php';

class OrderModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function placeOrder(int $userId, string $shippingAddress, string $paymentMethod, array $cartItems, float $totalAmount): int {
        $this->db->beginTransaction();
        try {
            
            foreach ($cartItems as $item) {
                $stmt = $this->db->prepare("SELECT availability FROM medicines WHERE id = :mid FOR UPDATE");
                $stmt->execute([':mid' => $item['medicine_id']]);
                $stock = (int) $stmt->fetchColumn();
                if ($item['quantity'] > $stock) {
                    throw new Exception("Insufficient stock for '{$item['name']}'.");
                }
            }

            
            $stmt = $this->db->prepare("
                INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_method, order_date)
                VALUES (:uid, :total, :addr, 'pending', :pm, NOW())
            ");
            $stmt->execute([
                ':uid'   => $userId,
                ':total' => $totalAmount,
                ':addr'  => $shippingAddress,
                ':pm'    => $paymentMethod,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            
            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, medicine_id, quantity, unit_price)
                VALUES (:oid, :mid, :qty, :price)
            ");

            foreach ($cartItems as $item) {
                $itemStmt->execute([
                    ':oid'   => $orderId,
                    ':mid'   => $item['medicine_id'],
                    ':qty'   => $item['quantity'],
                    ':price' => $item['price'],
                ]);
                
                $this->db->prepare("UPDATE medicines SET availability = availability - :qty WHERE id = :mid")
                         ->execute([':qty' => $item['quantity'], ':mid' => $item['medicine_id']]);
            }

            
            $txnId = 'TXN-' . strtoupper(bin2hex(random_bytes(6)));
            $this->db->prepare("
                INSERT INTO payments (order_id, amount, payment_method, transaction_id, payment_date)
                VALUES (:oid, :amt, :pm, :txn, NOW())
            ")->execute([
                ':oid' => $orderId,
                ':amt' => $totalAmount,
                ':pm'  => $paymentMethod,
                ':txn' => $txnId,
            ]);

            
            $this->db->prepare("DELETE FROM cart WHERE user_id = :uid")->execute([':uid' => $userId]);

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    
    public function getCustomerOrders(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT o.*, p.transaction_id
            FROM orders o
            LEFT JOIN payments p ON p.order_id = o.id
            WHERE o.user_id = :uid
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    
    public function getOrderWithItems(int $orderId, int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*, p.transaction_id
            FROM orders o
            LEFT JOIN payments p ON p.order_id = o.id
            WHERE o.id = :oid AND o.user_id = :uid
        ");
        $stmt->execute([':oid' => $orderId, ':uid' => $userId]);
        $order = $stmt->fetch();
        if (!$order) return null;

        $stmt = $this->db->prepare("
            SELECT oi.*, m.name, m.vendor_name, m.image_path
            FROM order_items oi
            JOIN medicines m ON oi.medicine_id = m.id
            WHERE oi.order_id = :oid
        ");
        $stmt->execute([':oid' => $orderId]);
        $order['items'] = $stmt->fetchAll();
        return $order;
    }
}
