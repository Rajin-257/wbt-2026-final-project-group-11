<?php

require_once __DIR__ . '/../../config/app.php';
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/db.php';

$q      = trim($_GET['q']      ?? '');
$vendor = trim($_GET['vendor'] ?? '');
$genre  = trim($_GET['genre']  ?? '');   // category id or empty
$type   = trim($_GET['type']   ?? '');   // liquid | solid | empty

$conn = getConnection();

$sql = "SELECT m.id, m.name, m.vendor_name, m.price, m.availability,
               m.description, m.image_path,
               c.id AS category_id, c.name AS category_name, c.category_type
        FROM medicines m
        LEFT JOIN categories c ON m.category_id = c.id
        WHERE 1=1";

$params = [];
$types  = '';

if ($q !== '') {
    $sql   .= ' AND m.name LIKE ?';
    $params[] = '%' . $q . '%';
    $types  .= 's';
}
if ($vendor !== '') {
    $sql   .= ' AND m.vendor_name LIKE ?';
    $params[] = '%' . $vendor . '%';
    $types  .= 's';
}
if ($genre !== '' && is_numeric($genre)) {
    $sql   .= ' AND m.category_id = ?';
    $params[] = (int)$genre;
    $types  .= 'i';
}
if ($type === 'liquid' || $type === 'solid') {
    $sql   .= ' AND c.category_type = ?';
    $params[] = $type;
    $types  .= 's';
}

$sql .= ' ORDER BY m.name ASC LIMIT 200';

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result    = mysqli_stmt_get_result($stmt);
$medicines = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);
mysqli_close($conn);

// numeric strings → proper types
foreach ($medicines as &$m) {
    $m['id']           = (int)$m['id'];
    $m['category_id']  = (int)$m['category_id'];
    $m['price']        = (float)$m['price'];
    $m['availability'] = (int)$m['availability'];
}
unset($m);

echo json_encode(['success' => true, 'data' => $medicines]);
