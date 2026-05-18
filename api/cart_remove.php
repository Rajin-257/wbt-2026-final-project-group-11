<?php
// api/cart_remove.php
session_start();
header('Content-Type: application/json');

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/CartModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

verify_csrf();

$data   = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$cartId = isset($data['cart_id']) ? (int) $data['cart_id'] : 0;

if ($cartId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid cart item.']);
    exit;
}

$cartModel = new CartModel();
$result    = $cartModel->removeItem((int) $_SESSION['user_id'], $cartId);

if (!$result['success']) {
    http_response_code(404);
}
echo json_encode($result);
