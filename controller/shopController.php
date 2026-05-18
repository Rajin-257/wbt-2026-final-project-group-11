<?php

require_once __DIR__ . '/../config/db.php';

function shop_home()
{
    if (session_status() === PHP_SESSION_NONE) 
    {
        session_start();
    }
    if (function_exists('try_remember_me')) 
    {
        try_remember_me();
    }

    $conn = getConnection();

    $catResult  = mysqli_query($conn, "
        SELECT c.id, c.name, c.category_type,
               COUNT(m.id) AS medicine_count
        FROM categories c
        LEFT JOIN medicines m ON m.category_id = c.id
        GROUP BY c.id
        ORDER BY c.category_type, c.name
    ");
    $categories = mysqli_fetch_all($catResult, MYSQLI_ASSOC);

    $activeCategoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
    $activeType = in_array($_GET['type'] ?? '', ['liquid', 'solid'])
                        ? $_GET['type'] : '';

    $sql = "SELECT m.*, c.name AS category_name, c.category_type
               FROM medicines m
               LEFT JOIN categories c ON m.category_id = c.id
               WHERE 1=1";
    $params = [];
    $types = '';

    if ($activeCategoryId > 0) {
        $sql .= " AND m.category_id = ?";
        $params[] = $activeCategoryId;
        $types .= 'i';
    }
    if ($activeType !== '') {
        $sql .= " AND c.category_type = ?";
        $params[] = $activeType;
        $types .= 's';
    }
    $sql .= " ORDER BY m.name ASC";

    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $medicines = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    $vendorResult = mysqli_query($conn, "SELECT DISTINCT vendor_name FROM medicines ORDER BY vendor_name");
    $vendors = array_column(mysqli_fetch_all($vendorResult, MYSQLI_ASSOC), 'vendor_name');

    mysqli_close($conn);

    $title = 'Home - Medicine Shop';
    $view = __DIR__ . '/../views/home.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}
