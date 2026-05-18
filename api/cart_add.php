<?php

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/model/CartModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.']);
    exit;
}

if (($_SESSION['role'] ?? '') !== 'customer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only customers can add to cart.']);
    exit;
}

csrf_require();

$data       = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$medicineId = isset($data['medicine_id']) ? (int) $data['medicine_id'] : 0;
$quantity   = isset($data['quantity']) ? (int) $data['quantity'] : 1;

if ($medicineId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid medicine ID.']);
    exit;
}
if ($quantity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1.']);
    exit;
}

$cartModel = new CartModel();
$result    = $cartModel->addItem((int) $_SESSION['user_id'], $medicineId, $quantity);

if (!$result['success']) {
    http_response_code(400);
}
echo json_encode($result);
