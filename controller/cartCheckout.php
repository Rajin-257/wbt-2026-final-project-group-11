<?php

require_once __DIR__ . '/home.php';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../model/CartModel.php';
require_once __DIR__ . '/../model/OrderModel.php';

function cart_page(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    try_remember_me();
    require_customer();

    $cartModel = new CartModel();
    $userId    = (int) $_SESSION['user_id'];
    $cartItems = $cartModel->getCartItems($userId);
    $cartTotal = $cartModel->getCartTotal($userId);
    $pageTitle = 'My Cart';

    require ROOT_PATH . '/views/ashiqur/cart/index.php';
}

function checkout_page(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    try_remember_me();
    require_customer();

    $cartModel = new CartModel();
    $userId    = (int) $_SESSION['user_id'];
    $cartItems = $cartModel->getCartItems($userId);

    if (empty($cartItems)) {
        redirect(BASE_URL . '/index.php?page=cart');
    }

    $cartTotal = $cartModel->getCartTotal($userId);
    $userInfo  = user_find_by_id($userId) ?: [];
    $pageTitle = 'Checkout';

    require ROOT_PATH . '/views/ashiqur/checkout/index.php';
}

function checkout_payment(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    try_remember_me();
    require_customer();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(BASE_URL . '/index.php?page=checkout');
    }
    csrf_require();

    $cartModel       = new CartModel();
    $userId          = (int) $_SESSION['user_id'];
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $errors          = [];

    if ($shippingAddress === '') {
        $errors[] = 'Shipping address is required.';
    }

    $cartItems = $cartModel->getCartItems($userId);
    if (empty($cartItems)) {
        $errors[] = 'Your cart is empty.';
    }

    foreach ($cartItems as $item) {
        if ((int) $item['quantity'] > (int) $item['stock']) {
            $errors[] = "'{$item['name']}' only has {$item['stock']} units in stock.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['checkout_errors'] = $errors;
        redirect(BASE_URL . '/index.php?page=checkout');
    }

    $_SESSION['pending_address'] = $shippingAddress;
    $cartTotal = $cartModel->getCartTotal($userId);
    $pageTitle = 'Select Payment';

    require ROOT_PATH . '/views/ashiqur/checkout/payment.php';
}

function place_order(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    try_remember_me();
    require_customer();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(BASE_URL . '/index.php?page=checkout');
    }
    csrf_require();

    $cartModel       = new CartModel();
    $orderModel      = new OrderModel();
    $userId          = (int) $_SESSION['user_id'];
    $shippingAddress = trim($_SESSION['pending_address'] ?? '');
    $paymentMethod   = trim($_POST['payment_method'] ?? '');

    $allowedMethods = ['Credit Card', 'bKash', 'Nagad', 'Bank Transfer', 'Cash on Delivery'];
    $errors         = [];

    if ($shippingAddress === '') {
        $errors[] = 'Shipping address missing. Please restart checkout.';
    }
    if (!in_array($paymentMethod, $allowedMethods, true)) {
        $errors[] = 'Please select a valid payment method.';
    }

    $cartItems = $cartModel->getCartItems($userId);
    if (empty($cartItems)) {
        $errors[] = 'Your cart is empty.';
    }

    if (!empty($errors)) {
        $_SESSION['checkout_errors'] = $errors;
        redirect(BASE_URL . '/index.php?page=checkout');
    }

    $cartTotal = $cartModel->getCartTotal($userId);

    try {
        $orderId = $orderModel->placeOrder($userId, $shippingAddress, $paymentMethod, $cartItems, $cartTotal);
        unset($_SESSION['pending_address']);
        redirect(BASE_URL . '/index.php?page=order_confirmation&order_id=' . $orderId);
    } catch (Exception $e) {
        $_SESSION['checkout_errors'] = [$e->getMessage()];
        redirect(BASE_URL . '/index.php?page=checkout');
    }
}

function order_confirmation(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    try_remember_me();
    require_customer();

    $orderModel = new OrderModel();
    $userId     = (int) $_SESSION['user_id'];
    $orderId    = (int) ($_GET['order_id'] ?? 0);
    $order      = $orderModel->getOrderWithItems($orderId, $userId);

    if (!$order) {
        redirect(BASE_URL . '/index.php?page=my_orders');
    }

    $pageTitle = 'Order Confirmed';
    require ROOT_PATH . '/views/ashiqur/orders/confirmation.php';
}

function my_orders_page(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    try_remember_me();
    require_customer();

    $orderModel = new OrderModel();
    $userId     = (int) $_SESSION['user_id'];
    $orders     = $orderModel->getCustomerOrders($userId);
    $pageTitle  = 'My Orders';

    require ROOT_PATH . '/views/ashiqur/orders/my_orders.php';
}
