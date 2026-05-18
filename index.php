<?php
session_start();

// TEMPORARY TEST SESSION - remove before final submission
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['name'] = 'Admin';

$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'home':
        require_once __DIR__ . '/controller/home.php';
        home_index();
        break;

    case 'admin/dashboard':
        require_once __DIR__ . '/controller/AdminController.php';
        admin_dashboard();
        break;

    case 'admin/categories':
        require_once __DIR__ . '/controller/CategoryController.php';
        admin_categories();
        break;

    case 'admin/medicines':
        require_once __DIR__ . '/controller/MedicineController.php';
        admin_medicines();
        break;

    case 'admin/orders':
        require_once __DIR__ . '/controller/OrderController.php';
        admin_orders();
        break;

    case 'admin/orders/update-status':
        require_once __DIR__ . '/controller/OrderController.php';
        admin_order_update_status();
        break;

    case 'admin/purchase-history':
        require_once __DIR__ . '/controller/OrderController.php';
        admin_purchase_history();
        break;

    case 'admin/customers':
        require_once __DIR__ . '/controller/CustomerController.php';
        admin_customers();
        break;

    default:
        http_response_code(404);
        echo "Page not found";
        break;
}