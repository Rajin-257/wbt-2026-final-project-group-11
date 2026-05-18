<!DOCTYPE html>
<html>
<head>
    <title>Categories — Admin</title>
    <link rel="stylesheet" href="public/admin.css">
    <?php include __DIR__ . '/../partials/csrf_head.php'; ?>
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>Category Management</h1>

        <?php if ($error):   ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <div class="form-box">
            <h3><?= $edit_category ? 'Edit Category' : 'Add New Category' ?></h3>
            <form method="POST" action="index.php?page=admin/categories"
                  onsubmit="return validateCategoryForm()">
                <?= csrf_field() ?>
                <input type="hidden" name="action"
                       value="<?= $edit_category ? 'update' : 'create' ?>">
                <?php if ($edit_category): ?>
                    <input type="hidden" name="id" value="<?= $edit_category['id'] ?>">
                <?php endif; ?>

                <label>Category Name</label>
                <input type="text" name="name" id="cat_name"
                       value="<?= $edit_category ? htmlspecialchars($edit_category['name']) : '' ?>"
                       placeholder="e.g. Paracetamol genre">

                <label>Type</label>
                <select name="category_type" id="cat_type">
                    <option value="">-- Select Type --</option>
                    <option value="liquid" <?= ($edit_category && $edit_category['category_type'] === 'liquid') ? 'selected' : '' ?>>Liquid</option>
                    <option value="solid"  <?= ($edit_category && $edit_category['category_type'] === 'solid')  ? 'selected' : '' ?>>Solid</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <?= $edit_category ? 'Update Category' : 'Add Category' ?>
                </button>
                <?php if ($edit_category): ?>
                    <a href="index.php?page=admin/categories"
                       class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr><td colspan="5">No categories found.</td></tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td><?= htmlspecialchars($cat['category_type']) ?></td>
                        <td><?= $cat['created_at'] ?></td>
                        <td>
                            <a href="index.php?page=admin/categories&edit=<?= $cat['id'] ?>"
                               class="btn btn-warning">Edit</a>
                            <form method="POST" action="index.php?page=admin/categories" class="inline-form"
                                  onsubmit="return confirm('Delete this category?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
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

<script>
function validateCategoryForm() {
    var name = document.getElementById('cat_name').value.trim();
    var type = document.getElementById('cat_type').value;
    if (name === '') {
        alert('Category name is required.');
        return false;
    }
    if (type === '') {
        alert('Please select a category type.');
        return false;
    }
    return true;
}
</script>

</body>
</html>