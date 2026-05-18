<div class="sidebar">
    <div class="sidebar-brand">Admin Panel</div>
    <a href="index.php?page=admin/dashboard"
       class="<?= ($page === 'admin/dashboard') ? 'active' : '' ?>">
        Dashboard
    </a>
    <a href="index.php?page=admin/categories"
       class="<?= ($page === 'admin/categories') ? 'active' : '' ?>">
        Categories
    </a>
    <a href="index.php?page=admin/medicines"
       class="<?= ($page === 'admin/medicines') ? 'active' : '' ?>">
        Medicines
    </a>
    <a href="index.php?page=admin/orders"
       class="<?= ($page === 'admin/orders') ? 'active' : '' ?>">
        Orders
    </a>
    <a href="index.php?page=admin/purchase-history"
       class="<?= ($page === 'admin/purchase-history') ? 'active' : '' ?>">
        Purchase History
    </a>
    <a href="index.php?page=admin/customers"
       class="<?= ($page === 'admin/customers') ? 'active' : '' ?>">
        Customers
    </a>
    <div class="sidebar-footer">
        <a href="index.php?page=logout">Logout</a>
    </div>
</div>