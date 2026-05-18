<?php
// models/CartModel.php

require_once ROOT_PATH . '/config/database.php';

class CartModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    
    public function getCartItems(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT c.id AS cart_id, c.quantity,
                   m.id AS medicine_id, m.name, m.vendor_name, m.price,
                   m.availability AS stock, m.image_path
            FROM cart c
            JOIN medicines m ON c.medicine_id = m.id
            WHERE c.user_id = :uid
            ORDER BY c.added_at DESC
        ");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }

    
    public function getCartCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    
    public function addItem(int $userId, int $medicineId, int $quantity): array {
        // Check medicine exists and has stock
        $stmt = $this->db->prepare("SELECT id, availability FROM medicines WHERE id = :mid");
        $stmt->execute([':mid' => $medicineId]);
        $medicine = $stmt->fetch();

        if (!$medicine) {
            return ['success' => false, 'message' => 'Medicine not found.'];
        }

        
        $stmt = $this->db->prepare("SELECT id, quantity FROM cart WHERE user_id = :uid AND medicine_id = :mid");
        $stmt->execute([':uid' => $userId, ':mid' => $medicineId]);
        $existing = $stmt->fetch();

        $newQty = $existing ? $existing['quantity'] + $quantity : $quantity;

        if ($newQty > $medicine['availability']) {
            return ['success' => false, 'message' => 'Requested quantity exceeds available stock (' . $medicine['availability'] . ').'];
        }
        if ($newQty < 1) {
            return ['success' => false, 'message' => 'Quantity must be at least 1.'];
        }

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE cart SET quantity = :qty WHERE id = :id");
            $stmt->execute([':qty' => $newQty, ':id' => $existing['id']]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO cart (user_id, medicine_id, quantity, added_at) VALUES (:uid, :mid, :qty, NOW())");
            $stmt->execute([':uid' => $userId, ':mid' => $medicineId, ':qty' => $quantity]);
        }

        return ['success' => true, 'cart_count' => $this->getCartCount($userId)];
    }

    
    public function updateItem(int $userId, int $cartId, int $quantity): array {
        // Get cart row with stock info
        $stmt = $this->db->prepare("
            SELECT c.id, m.availability AS stock
            FROM cart c JOIN medicines m ON c.medicine_id = m.id
            WHERE c.id = :cid AND c.user_id = :uid
        ");
        $stmt->execute([':cid' => $cartId, ':uid' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Quantity must be at least 1.'];
        }
        if ($quantity > $row['stock']) {
            return ['success' => false, 'message' => 'Quantity exceeds stock (' . $row['stock'] . ').'];
        }

        $stmt = $this->db->prepare("UPDATE cart SET quantity = :qty WHERE id = :cid AND user_id = :uid");
        $stmt->execute([':qty' => $quantity, ':cid' => $cartId, ':uid' => $userId]);

        return [
            'success'    => true,
            'cart_count' => $this->getCartCount($userId),
            'cart_total' => $this->getCartTotal($userId),
        ];
    }

    
    public function removeItem(int $userId, int $cartId): array {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE id = :cid AND user_id = :uid");
        $stmt->execute([':cid' => $cartId, ':uid' => $userId]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }

        return [
            'success'    => true,
            'cart_count' => $this->getCartCount($userId),
            'cart_total' => $this->getCartTotal($userId),
        ];
    }

    
    public function getCartTotal(int $userId): float {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(c.quantity * m.price), 0)
            FROM cart c JOIN medicines m ON c.medicine_id = m.id
            WHERE c.user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        return (float) $stmt->fetchColumn();
    }

    
    public function clearCart(int $userId): void {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
    }
}
