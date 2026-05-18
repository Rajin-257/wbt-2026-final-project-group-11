<!DOCTYPE html>
<html>
<head>
    <title>Dashboard — Admin</title>
    <link rel="stylesheet" href="public/admin.css">
    <?php include __DIR__ . '/../partials/csrf_head.php'; ?>
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>Admin Dashboard</h1>

        <div class="stat-cards">
            <div class="card">
                <h3><?= $medicines ?></h3>
                <p>Medicines</p>
            </div>
            <div class="card">
                <h3><?= $categories ?></h3>
                <p>Categories</p>
            </div>
            <div class="card">
                <h3><?= $customers ?></h3>
                <p>Customers</p>
            </div>
            <div class="card">
                <h3><?= $pending ?></h3>
                <p>Pending Orders</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>