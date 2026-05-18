<?php
session_start();

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/controller/home.php';

$page = $_GET['page'] ?? 'home';
$GLOBALS['page'] = $page;

switch ($page) {

    /* ── public auth ─────────────────────────────────────────────────────── */
    case 'register':
        register();
        break;

    case 'login':
        login();
        break;

    case 'logout':
        logout();
        break;

    /* ── profile (session-gated, any role) ───────────────────────────────── */
    case 'profile':
        profile();
        break;

    /* ── admin pages ─────────────────────────────────────────────────────── */
    case 'admin/dashboard':
        require_once __DIR__ . '/controller/adminController.php';
        admin_dashboard();
        break;

    case 'admin/categories':
        require_once __DIR__ . '/controller/categoryController.php';
        admin_categories();
        break;

    case 'admin/medicines':
        require_once __DIR__ . '/controller/medicineController.php';
        admin_medicines();
        break;

    case 'admin/orders':
        require_once __DIR__ . '/controller/adminOrderController.php';
        require_once __DIR__ . '/config/db.php';
        admin_orders();
        break;

    case 'admin/purchase-history':
        require_once __DIR__ . '/controller/adminOrderController.php';
        require_once __DIR__ . '/config/db.php';
        admin_purchase_history();
        break;

    case 'admin/customers':
        require_once __DIR__ . '/controller/customerController.php';
        admin_customers();
        break;

    case 'admin/order-status':
        require_once __DIR__ . '/controller/adminOrderController.php';
        admin_order_update_status();
        break;

    /* ── customer cart & checkout (Task 3) ───────────────────────────────── */
    case 'cart':
        require_once __DIR__ . '/controller/cartCheckout.php';
        cart_page();
        break;

    case 'checkout':
        require_once __DIR__ . '/controller/cartCheckout.php';
        checkout_page();
        break;

    case 'checkout_payment':
        require_once __DIR__ . '/controller/cartCheckout.php';
        checkout_payment();
        break;

    case 'place_order':
        require_once __DIR__ . '/controller/cartCheckout.php';
        place_order();
        break;

    case 'order_confirmation':
        require_once __DIR__ . '/controller/cartCheckout.php';
        order_confirmation();
        break;

    case 'my_orders':
        require_once __DIR__ . '/controller/cartCheckout.php';
        my_orders_page();
        break;

    /* ── home / shop ─────────────────────────────────────────────────────── */
    default:
        require_once __DIR__ . '/controller/shopController.php';
        shop_home();
        break;
}
