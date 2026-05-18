<?php
// controllers/CartController.php

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/models/CartModel.php';

class CartController {
    private CartModel $cartModel;

    public function __construct() {
        $this->cartModel = new CartModel();
    }

    
    public function index(): void {
        require_customer();
        $userId    = (int) $_SESSION['user_id'];
        $cartItems = $this->cartModel->getCartItems($userId);
        $cartTotal = $this->cartModel->getCartTotal($userId);
        require ROOT_PATH . '/views/cart/index.php';
    }
}
