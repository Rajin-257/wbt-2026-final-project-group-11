<?php
require_once __DIR__ . '/../model/OrderModel.php';

class OrderController {
    private OrderModel $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
    }

    
    public function confirmation(): void {
        require_customer();
        $userId  = (int) $_SESSION['user_id'];
        $orderId = (int) ($_GET['order_id'] ?? 0);

        $order = $this->orderModel->getOrderWithItems($orderId, $userId);
        if (!$order) {
            redirect(BASE_URL . '/index.php?page=my_orders');
        }

        require ROOT_PATH . '/views/orders/confirmation.php';
    }

    
    public function myOrders(): void {
        require_customer();
        $userId = (int) $_SESSION['user_id'];
        $orders = $this->orderModel->getCustomerOrders($userId);
        require ROOT_PATH . '/views/orders/my_orders.php';
    }
}


