<!DOCTYPE html>
<html>
<head>
    <title>Medicines — Admin</title>
    <link rel="stylesheet" href="public/admin.css">
    <?php include __DIR__ . '/../partials/csrf_head.php'; ?>
</head>
<body>

<?php require_once __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h1>Medicine Management</h1>

        <?php if ($error):   ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

        <div class="form-box">
            <h3><?= $edit_medicine ? 'Edit Medicine' : 'Add New Medicine' ?></h3>
            <form method="POST" action="index.php?page=admin/medicines"
                  enctype="multipart/form-data" onsubmit="return validateMedicineForm()">
                <?= csrf_field() ?>
                <input type="hidden" name="action"
                       value="<?= $edit_medicine ? 'update' : 'create' ?>">
                <?php if ($edit_medicine): ?>
                    <input type="hidden" name="id" value="<?= $edit_medicine['id'] ?>">
                <?php endif; ?>

                <label>Medicine Name</label>
                <input type="text" name="name" id="med_name"
                       value="<?= $edit_medicine ? htmlspecialchars($edit_medicine['name']) : '' ?>"
                       placeholder="e.g. Napa 500mg">

                <label>Category</label>
                <select name="category_id" id="med_category">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($cat_list as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= ($edit_medicine && $edit_medicine['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?> (<?= $cat['category_type'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Vendor Name</label>
                <input type="text" name="vendor_name" id="med_vendor"
                       value="<?= $edit_medicine ? htmlspecialchars($edit_medicine['vendor_name']) : '' ?>"
                       placeholder="e.g. Square Pharma">

                <label>Price (BDT)</label>
                <input type="number" name="price" id="med_price"
                       step="0.01" min="0.01"
                       value="<?= $edit_medicine ? $edit_medicine['price'] : '' ?>">

                <label>Stock (Availability)</label>
                <input type="number" name="availability" id="med_stock"
                       min="0"
                       value="<?= $edit_medicine ? $edit_medicine['availability'] : '' ?>">

                <label>Description</label>
                <textarea name="description" id="med_desc"
                          rows="3"><?= $edit_medicine ? htmlspecialchars($edit_medicine['description']) : '' ?></textarea>

                <label>Image (JPEG/PNG, max 2MB)</label>
                <?php if ($edit_medicine && $edit_medicine['image_path']): ?>
                    <img src="<?= htmlspecialchars($edit_medicine['image_path']) ?>" class="thumb">
                    <small>Upload new image to replace existing</small>
                <?php endif; ?>
                <input type="file" name="image" id="med_image"
                       accept="image/jpeg,image/png">

                <button type="submit" class="btn btn-primary">
                    <?= $edit_medicine ? 'Update Medicine' : 'Add Medicine' ?>
                </button>
                <?php if ($edit_medicine): ?>
                    <a href="index.php?page=admin/medicines"
                       class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Vendor</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($medicines)): ?>
                    <tr><td colspan="8">No medicines found.</td></tr>
                <?php else: ?>
                    <?php foreach ($medicines as $med): ?>
                    <tr>
                        <td><?= $med['id'] ?></td>
                        <td>
                            <?php if ($med['image_path']): ?>
                                <img src="<?= htmlspecialchars($med['image_path']) ?>"
                                     class="thumb">
                            <?php else: ?>
                                No image
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($med['name']) ?></td>
                        <td><?= htmlspecialchars($med['category_name']) ?></td>
                        <td><?= htmlspecialchars($med['vendor_name']) ?></td>
                        <td><?= $med['price'] ?> bdt</td>
                        <td><?= $med['availability'] ?></td>
                        <td>
                            <a href="index.php?page=admin/medicines&edit=<?= $med['id'] ?>"
                               class="btn btn-warning">Edit</a>
                            <form method="POST" action="index.php?page=admin/medicines" class="inline-form"
                                  onsubmit="return confirm('Delete this medicine?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int) $med['id'] ?>">
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
function validateMedicineForm() {
    var name     = document.getElementById('med_name').value.trim();
    var category = document.getElementById('med_category').value;
    var vendor   = document.getElementById('med_vendor').value.trim();
    var price    = parseFloat(document.getElementById('med_price').value);
    var stock    = parseInt(document.getElementById('med_stock').value);
    var image    = document.getElementById('med_image').files[0];

    if (name === '') {
        alert('Medicine name is required.'); return false;
    }
    if (category === '') {
        alert('Please select a category.'); return false;
    }
    if (vendor === '') {
        alert('Vendor name is required.'); return false;
    }
    if (isNaN(price) || price <= 0) {
        alert('Price must be a positive number.'); return false;
    }
    if (isNaN(stock) || stock < 0) {
        alert('Stock cannot be negative.'); return false;
    }
    if (image) {
        var allowed = ['image/jpeg', 'image/png'];
        if (!allowed.includes(image.type)) {
            alert('Only JPEG and PNG images allowed.'); return false;
        }
        if (image.size > 2 * 1024 * 1024) {
            alert('Image must be under 2MB.'); return false;
        }
    }
    return true;
}
</script>

</body>
</html>