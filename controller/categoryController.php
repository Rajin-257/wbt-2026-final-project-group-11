<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

function admin_categories() {
    $conn = getConnection();
    $error = '';
    $success = '';
    $edit_category = null;

    // DELETE
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        
        // block delete if medicines exist under this category
        $check = mysqli_query($conn, "SELECT COUNT(*) FROM medicines WHERE category_id = $id");
        $count = mysqli_fetch_row($check)[0];
        
        if ($count > 0) {
            $error = "Cannot delete — medicines exist under this category.";
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $success = "Category deleted successfully.";
        }
    }

    // CREATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
        $name = trim($_POST['name']);
        $type = trim($_POST['category_type']);

        if (empty($name) || empty($type)) {
            $error = "All fields are required.";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO categories (name, category_type) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $name, $type);
            mysqli_stmt_execute($stmt);
            $success = "Category created successfully.";
        }
    }

    // UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $id   = intval($_POST['id']);
        $name = trim($_POST['name']);
        $type = trim($_POST['category_type']);

        if (empty($name) || empty($type)) {
            $error = "All fields are required.";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE categories SET name = ?, category_type = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $name, $type, $id);
            mysqli_stmt_execute($stmt);
            $success = "Category updated successfully.";
        }
    }

    // load edit form if edit requested
    if (isset($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $result = mysqli_query($conn, "SELECT * FROM categories WHERE id = $id");
        $edit_category = mysqli_fetch_assoc($result);
    }

    // fetch all categories
    $result = mysqli_query($conn, "SELECT * FROM categories ORDER BY created_at DESC");
    $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

    require_once 'views/admin/categories.php';
}