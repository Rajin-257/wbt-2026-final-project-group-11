<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

function admin_categories() {
    $conn = getConnection();
    $error = '';
    $success = '';
    $edit_category = null;

    // DELETE (POST + CSRF)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
        csrf_require();
        $id = intval($_POST['id'] ?? 0);

        $check = mysqli_prepare($conn, 'SELECT COUNT(*) FROM medicines WHERE category_id = ?');
        mysqli_stmt_bind_param($check, 'i', $id);
        mysqli_stmt_execute($check);
        mysqli_stmt_bind_result($check, $count);
        mysqli_stmt_fetch($check);
        mysqli_stmt_close($check);

        if ($count > 0) {
            $error = 'Cannot delete — medicines exist under this category.';
        } else {
            $stmt = mysqli_prepare($conn, 'DELETE FROM categories WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $success = 'Category deleted successfully.';
        }
    }

    // CREATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
        csrf_require();
        $name = trim($_POST['name']);
        $type = trim($_POST['category_type']);

        if (empty($name) || empty($type)) {
            $error = 'All fields are required.';
        } else {
            $stmt = mysqli_prepare($conn, 'INSERT INTO categories (name, category_type) VALUES (?, ?)');
            mysqli_stmt_bind_param($stmt, 'ss', $name, $type);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $success = 'Category created successfully.';
        }
    }

    // UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
        csrf_require();
        $id   = intval($_POST['id']);
        $name = trim($_POST['name']);
        $type = trim($_POST['category_type']);

        if (empty($name) || empty($type)) {
            $error = 'All fields are required.';
        } else {
            $stmt = mysqli_prepare($conn, 'UPDATE categories SET name = ?, category_type = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'ssi', $name, $type, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $success = 'Category updated successfully.';
        }
    }

    // load edit form if edit requested
    if (isset($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $stmt = mysqli_prepare($conn, 'SELECT * FROM categories WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $edit_category = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
    }

    $result = mysqli_query($conn, 'SELECT * FROM categories ORDER BY created_at DESC');
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

    require_once 'views/admin/categories.php';
}
