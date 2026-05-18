<?php
// controllers/CheckoutController.php

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/CartModel.php';
require_once ROOT_PATH . '/models/OrderModel.php';

class CheckoutController {
    private CartModel  $cartModel;
    private OrderModel $orderModel;

    public function __construct() {
        $this->cartModel  = new CartModel();
        $this->orderModel = new OrderModel();
    }

    
    public function index(): void {
        require_customer();
        $userId    = (int) $_SESSION['user_id'];
        $cartItems = $this->cartModel->getCartItems($userId);

        if (empty($cartItems)) {
            redirect(BASE_URL . '/index.php?page=cart');
        }

        $cartTotal = $this->cartModel->getCartTotal($userId);

        
        $db   = Database::getConnection();
        $stmt = $db->prepare("SELECT address, phone FROM users WHERE id = :uid");
        $stmt->execute([':uid' => $userId]);
        $userInfo = $stmt->fetch();

        require ROOT_PATH . '/views/checkout/index.php';
    }

    
    public function payment(): void {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=checkout');
        }


        $userId          = (int) $_SESSION['user_id'];
        $shippingAddress = trim($_POST['shipping_address'] ?? '');

        
        $errors = [];
        if (empty($shippingAddress)) {
            $errors[] = 'Shipping address is required.';
        }

        $cartItems = $this->cartModel->getCartItems($userId);
        if (empty($cartItems)) {
            $errors[] = 'Your cart is empty.';
        }

        
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock']) {
                $errors[] = "'{$item['name']}' only has {$item['stock']} units in stock.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            redirect(BASE_URL . '/index.php?page=checkout');
        }

        
        $_SESSION['pending_address']   = $shippingAddress;
        $cartTotal = $this->cartModel->getCartTotal($userId);
        $cartItems  = $this->cartModel->getCartItems($userId);
        require ROOT_PATH . '/views/checkout/payment.php';
    }

    
    public function placeOrder(): void {
        require_customer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . '/index.php?page=checkout');
        }


        $userId          = (int) $_SESSION['user_id'];
        $shippingAddress = $_SESSION['pending_address'] ?? '';
        $paymentMethod   = trim($_POST['payment_method'] ?? '');

        $allowedMethods = ['Credit Card', 'bKash', 'Nagad', 'Bank Transfer', 'Cash on Delivery'];

        $errors = [];
        if (empty($shippingAddress)) {
            $errors[] = 'Shipping address missing. Please restart checkout.';
        }
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $errors[] = 'Please select a valid payment method.';
        }

        $cartItems = $this->cartModel->getCartItems($userId);
        if (empty($cartItems)) {
            $errors[] = 'Your cart is empty.';
        }

        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            redirect(BASE_URL . '/index.php?page=checkout');
        }

        $cartTotal = $this->cartModel->getCartTotal($userId);

        try {
            $orderId = $this->orderModel->placeOrder($userId, $shippingAddress, $paymentMethod, $cartItems, $cartTotal);
            unset($_SESSION['pending_address']);
            redirect(BASE_URL . '/index.php?page=order_confirmation&order_id=' . $orderId);
        } catch (Exception $e) {
            $_SESSION['checkout_errors'] = [$e->getMessage()];
            redirect(BASE_URL . '/index.php?page=checkout');
        }
    }
}
