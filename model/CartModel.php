<?php

require_once __DIR__ . '/../config/db.php';

class CartModel
{
    public function getCartItems(int $userId): array
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, "
            SELECT c.id AS cart_id, c.quantity,
                   m.id AS medicine_id, m.name, m.vendor_name, m.price,
                   m.availability AS stock, m.image_path
            FROM cart c
            JOIN medicines m ON c.medicine_id = m.id
            WHERE c.user_id = ?
            ORDER BY c.added_at DESC
        ");
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $rows;
    }

    public function getCartCount(int $userId): int
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, 'SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return (int) $count;
    }

    public function addItem(int $userId, int $medicineId, int $quantity): array
    {
        $conn = getConnection();

        $stmt = mysqli_prepare($conn, 'SELECT id, availability FROM medicines WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $medicineId);
        mysqli_stmt_execute($stmt);
        $medicine = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$medicine) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Medicine not found.'];
        }

        $stmt = mysqli_prepare($conn, 'SELECT id, quantity FROM cart WHERE user_id = ? AND medicine_id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $userId, $medicineId);
        mysqli_stmt_execute($stmt);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        $newQty = $existing ? (int) $existing['quantity'] + $quantity : $quantity;
        $stock  = (int) $medicine['availability'];

        if ($newQty > $stock) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Requested quantity exceeds available stock (' . $stock . ').'];
        }
        if ($newQty < 1) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Quantity must be at least 1.'];
        }

        if ($existing) {
            $stmt = mysqli_prepare($conn, 'UPDATE cart SET quantity = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ii', $newQty, $existing['id']);
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO cart (user_id, medicine_id, quantity) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'iii', $userId, $medicineId, $quantity);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return ['success' => true, 'cart_count' => $this->getCartCount($userId)];
    }

    public function updateItem(int $userId, int $cartId, int $quantity): array
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, "
            SELECT c.id, m.availability AS stock
            FROM cart c
            JOIN medicines m ON c.medicine_id = m.id
            WHERE c.id = ? AND c.user_id = ?
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $cartId, $userId);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$row) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Cart item not found.'];
        }
        if ($quantity < 1) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Quantity must be at least 1.'];
        }
        if ($quantity > (int) $row['stock']) {
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Quantity exceeds stock (' . $row['stock'] . ').'];
        }

        $stmt = mysqli_prepare($conn, 'UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($stmt, 'iii', $quantity, $cartId, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        return [
            'success'    => true,
            'cart_count' => $this->getCartCount($userId),
            'cart_total' => $this->getCartTotal($userId),
        ];
    }

    public function removeItem(int $userId, int $cartId): array
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, 'DELETE FROM cart WHERE id = ? AND user_id = ?');
        mysqli_stmt_bind_param($stmt, 'ii', $cartId, $userId);
        mysqli_stmt_execute($stmt);
        $deleted = mysqli_stmt_affected_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        if (!$deleted) {
            return ['success' => false, 'message' => 'Cart item not found.'];
        }

        return [
            'success'    => true,
            'cart_count' => $this->getCartCount($userId),
            'cart_total' => $this->getCartTotal($userId),
        ];
    }

    public function getCartTotal(int $userId): float
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, '
            SELECT COALESCE(SUM(c.quantity * m.price), 0)
            FROM cart c
            JOIN medicines m ON c.medicine_id = m.id
            WHERE c.user_id = ?
        ');
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return (float) $total;
    }

    public function clearCart(int $userId): void
    {
        $conn = getConnection();
        $stmt = mysqli_prepare($conn, 'DELETE FROM cart WHERE user_id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
