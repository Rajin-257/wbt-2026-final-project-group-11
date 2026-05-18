<!DOCTYPE html>
<html>
<head>
    <title>Customers — Admin</title>
    <link rel="stylesheet" href="public/css/admin.css">
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>Customer Management</h1>

        <?php if ($error):   ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr><td colspan="7">No customers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= $customer['id'] ?></td>
                        <td><?= htmlspecialchars($customer['name']) ?></td>
                        <td><?= htmlspecialchars($customer['email']) ?></td>
                        <td><?= htmlspecialchars($customer['phone']) ?></td>
                        <td><?= htmlspecialchars($customer['address']) ?></td>
                        <td><?= $customer['created_at'] ?></td>
                        <td>
                            <a href="index.php?page=admin/customers&delete=<?= $customer['id'] ?>"
                               class="btn btn-danger"
                               onclick="return confirm('Delete this customer and all their data?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>