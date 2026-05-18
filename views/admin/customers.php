<!DOCTYPE html>
<html>
<head>
    <title>Customers — Admin</title>
    <link rel="stylesheet" href="public/admin.css">
    <?php include __DIR__ . '/../partials/csrf_head.php'; ?>
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>Customer Management</h1>

        <?php if ($error):   ?><div class="error"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= e($success) ?></div><?php endif; ?>

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
                        <td><?= e($customer['name']) ?></td>
                        <td><?= e($customer['email']) ?></td>
                        <td><?= e($customer['phone']) ?></td>
                        <td><?= e($customer['address']) ?></td>
                        <td><?= $customer['created_at'] ?></td>
                        <td>
                            <form method="POST" action="index.php?page=admin/customers" class="inline-form"
                                  onsubmit="return confirm('Delete this customer and all their data?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $customer['id'] ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
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