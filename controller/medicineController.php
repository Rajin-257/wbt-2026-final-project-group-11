<?php
require_once 'config/admin_auth.php';
require_once 'config/db.php';

function admin_medicines() {
    $conn = getConnection();
    $error = '';
    $success = '';
    $edit_medicine = null;

    // DELETE
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);

        // block delete if medicine is in a pending order
        $check = mysqli_query($conn, "SELECT COUNT(*) FROM order_items oi 
                                      JOIN orders o ON oi.order_id = o.id 
                                      WHERE oi.medicine_id = $id AND o.status = 'pending'");
        $count = mysqli_fetch_row($check)[0];

        if ($count > 0) {
            $error = "Cannot delete — medicine is in a pending order.";
        } else {
            // delete image file
            $res = mysqli_query($conn, "SELECT image_path FROM medicines WHERE id = $id");
            $row = mysqli_fetch_assoc($res);
            if ($row && $row['image_path'] && file_exists($row['image_path'])) {
                unlink($row['image_path']);
            }

            $stmt = mysqli_prepare($conn, "DELETE FROM medicines WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $success = "Medicine deleted successfully.";
        }
    }

    // CREATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
        $name        = trim($_POST['name']);
        $category_id = intval($_POST['category_id']);
        $vendor      = trim($_POST['vendor_name']);
        $price       = trim($_POST['price']);
        $stock       = intval($_POST['availability']);
        $description = trim($_POST['description']);
        $image_path  = '';

        // PHP validation
        if (empty($name) || empty($vendor) || empty($price) || $category_id === 0) {
            $error = "All fields are required.";
        } elseif (!is_numeric($price) || floatval($price) <= 0) {
            $error = "Price must be a positive number.";
        } else {
            // image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed_mime = ['image/jpeg', 'image/png'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowed_mime)) {
                    $error = "Only JPEG and PNG images are allowed.";
                } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                    $error = "Image must be under 2MB.";
                } else {
                    $upload_dir = 'public/uploads/medicines/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $filename   = time() . '_' . basename($_FILES['image']['name']);
                    $image_path = $upload_dir . $filename;
                    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
                }
            }

            if (empty($error)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO medicines 
                    (name, category_id, vendor_name, price, availability, description, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sisdiis", $name, $category_id, $vendor, $price, $stock, $description, $image_path);
                mysqli_stmt_execute($stmt);
                $success = "Medicine added successfully.";
            }
        }
    }

    // UPDATE
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        $id          = intval($_POST['id']);
        $name        = trim($_POST['name']);
        $category_id = intval($_POST['category_id']);
        $vendor      = trim($_POST['vendor_name']);
        $price       = trim($_POST['price']);
        $stock       = intval($_POST['availability']);
        $description = trim($_POST['description']);

        if (empty($name) || empty($vendor) || empty($price) || $category_id === 0) {
            $error = "All fields are required.";
        } elseif (!is_numeric($price) || floatval($price) <= 0) {
            $error = "Price must be a positive number.";
        } else {
            // get existing image
            $res = mysqli_query($conn, "SELECT image_path FROM medicines WHERE id = $id");
            $row = mysqli_fetch_assoc($res);
            $image_path = $row['image_path'];

            // new image uploaded?
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed_mime = ['image/jpeg', 'image/png'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowed_mime)) {
                    $error = "Only JPEG and PNG images are allowed.";
                } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                    $error = "Image must be under 2MB.";
                } else {
                    // delete old image
                    if ($image_path && file_exists($image_path)) unlink($image_path);

                    $upload_dir = 'public/uploads/medicines/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    $filename   = time() . '_' . basename($_FILES['image']['name']);
                    $image_path = $upload_dir . $filename;
                    move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
                }
            }

            if (empty($error)) {
                $stmt = mysqli_prepare($conn, "UPDATE medicines 
                    SET name=?, category_id=?, vendor_name=?, price=?, availability=?, description=?, image_path=? 
                    WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sisdissi", $name, $category_id, $vendor, $price, $stock, $description, $image_path, $id);
                mysqli_stmt_execute($stmt);
                $success = "Medicine updated successfully.";
            }
        }
    }

    // load edit form
    if (isset($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $result = mysqli_query($conn, "SELECT * FROM medicines WHERE id = $id");
        $edit_medicine = mysqli_fetch_assoc($result);
    }

    // fetch all medicines with category name
    $result    = mysqli_query($conn, "SELECT m.*, c.name as category_name 
                                      FROM medicines m 
                                      LEFT JOIN categories c ON m.category_id = c.id 
                                      ORDER BY m.created_at DESC");
    $medicines = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // fetch categories for dropdown
    $cat_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
    $cat_list   = mysqli_fetch_all($cat_result, MYSQLI_ASSOC);

    require_once 'views/admin/medicines.php';
}