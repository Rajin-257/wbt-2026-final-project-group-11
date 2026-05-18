<?php
// index.php  —  Front controller / router

session_start();

if (!defined('ROOT_PATH')) define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/app.php';

$page = $_GET['page'] ?? 'home';


switch ($page) {

    case 'cart':
        require_once ROOT_PATH . '/controllers/CartController.php';
        (new CartController())->index();
        break;

    case 'checkout':
        require_once ROOT_PATH . '/controllers/CheckoutController.php';
        (new CheckoutController())->index();
        break;

    case 'checkout_payment':
        require_once ROOT_PATH . '/controllers/CheckoutController.php';
        (new CheckoutController())->payment();
        break;

    case 'place_order':
        require_once ROOT_PATH . '/controllers/CheckoutController.php';
        (new CheckoutController())->placeOrder();
        break;

    case 'order_confirmation':
        require_once ROOT_PATH . '/controllers/OrderController.php';
        (new OrderController())->confirmation();
        break;

    case 'my_orders':
        require_once ROOT_PATH . '/controllers/OrderController.php';
        (new OrderController())->myOrders();
        break;

    default:
        echo '<p>Page "' . e($page) . '" is handled by another task.</p>';
        break;
}
