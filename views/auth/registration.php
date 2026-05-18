
<div class="auth-wrap">
    <div class="auth-card">
        <h1>Create account</h1>
        <p class="lead">Online Medicine Shop — register with your details.</p>

        <?php if (!empty($errors)): ?>
            <div class="auth-alert auth-alert--error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/../partials/csrf_flash.php'; ?>

        <form method="post" action="" autocomplete="on" id="reg-form" novalidate>
            <?php echo csrf_field(); ?>
            <div class="auth-field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required
                       placeholder="Your full name"
                       value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
                <span class="field-error" id="err-name"></span>
            </div>
            <div class="auth-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       placeholder="you@example.com"
                       value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
                <span class="field-error" id="err-email"></span>
            </div>
            <div class="auth-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="Min. 8 characters">
                <span class="field-error" id="err-password"></span>
            </div>
            <div class="auth-field">
                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                       placeholder="Street, city, postal code"
                       value="<?php echo htmlspecialchars($old['address'] ?? ''); ?>">
            </div>
            <div class="auth-field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone"
                       placeholder="+880 1xxx xxxxxx"
                       value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
                <span class="field-error" id="err-phone"></span>
            </div>
            <div class="auth-field">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled <?php echo empty($old['role']) ? 'selected' : ''; ?>>Select role</option>
                    <option value="customer" <?php echo (($old['role'] ?? '') === 'customer') ? 'selected' : ''; ?>>Customer</option>
                    <option value="admin"    <?php echo (($old['role'] ?? '') === 'admin')    ? 'selected' : ''; ?>>Admin</option>
                </select>
                <span class="field-error" id="err-role"></span>
            </div>
            <div class="auth-row">
                <span></span>
                <a href="index.php?page=login" class="auth-link">Already have an account?</a>
            </div>
            <button type="submit" class="auth-submit">Register</button>
        </form>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('reg-form');
    if (!form) return;

    function setError(id, msg) {
        var el = document.getElementById(id);
        if (el) el.textContent = msg;
    }
    function clearErrors() {
        ['err-name','err-email','err-password','err-phone','err-role'].forEach(function(id){
            setError(id, '');
        });
    }

    form.addEventListener('submit', function (e) {
        clearErrors();
        var valid = true;

        var name = document.getElementById('name').value.trim();
        if (name === '') {
            setError('err-name', 'Name is required.');
            valid = false;
        }

        var email = document.getElementById('email').value.trim();
        var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRe.test(email)) {
            setError('err-email', 'Enter a valid email address.');
            valid = false;
        }

        var pw = document.getElementById('password').value;
        if (pw.length < 8) {
            setError('err-password', 'Password must be at least 8 characters.');
            valid = false;
        }

        var phone = document.getElementById('phone').value.trim();
        if (phone !== '' && !/^[+]?[\d\s\-()]{7,20}$/.test(phone)) {
            setError('err-phone', 'Enter a valid phone number.');
            valid = false;
        }

        var role = document.getElementById('role').value;
        if (!role) {
            setError('err-role', 'Please select a role.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
})();
</script>
