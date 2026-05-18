<?php
// api/cart_add.php
session_start();
header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/CartModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Please login to add items to cart.']);
    exit;
}

if ($_SESSION['role'] !== 'customer') {
    http_response_code(403);
    echo json_encode(['error' => 'Only customers can add to cart.']);
    exit;
}

verify_csrf();

$data        = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$medicineId  = isset($data['medicine_id']) ? (int) $data['medicine_id'] : 0;
$quantity    = isset($data['quantity'])    ? (int) $data['quantity']    : 1;

if ($medicineId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid medicine ID.']);
    exit;
}
if ($quantity < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Quantity must be at least 1.']);
    exit;
}

$cartModel = new CartModel();
$result    = $cartModel->addItem((int) $_SESSION['user_id'], $medicineId, $quantity);

if (!$result['success']) {
    http_response_code(400);
}
echo json_encode($result);
