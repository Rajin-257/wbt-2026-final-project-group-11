<?php

require __DIR__ . '/controller/home.php';

$page = $_GET['page'] ?? 'home';

if ($page === 'register') {
    register();
} else {
    home_index();
}
