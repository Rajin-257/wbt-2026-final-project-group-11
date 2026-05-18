
<div class="auth-wrap">
    <div class="auth-card">
        <h1>Online Medicine Shop</h1>
        <p class="lead">Sign in to your account to continue.</p>

        <?php if (!empty($success)): ?>
            <div class="auth-alert auth-alert--success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="auth-alert auth-alert--error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/../partials/csrf_flash.php'; ?>

        <form method="post" action="" autocomplete="on" id="login-form" novalidate>
            <?php echo csrf_field(); ?>
            <div class="auth-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       placeholder="you@example.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                <span class="field-error" id="err-email"></span>
            </div>
            <div class="auth-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
                <span class="field-error" id="err-password"></span>
            </div>
            <div class="auth-row">
                <label class="auth-remember" for="remember">
                    <input type="checkbox" id="remember" name="remember">
                    Remember me
                </label>
                <a href="index.php?page=register" class="auth-link">Create account</a>
            </div>
            <button type="submit" class="auth-submit">Sign in</button>
        </form>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('login-form');
    if (!form) return;

    function setError(id, msg) {
        var el = document.getElementById(id);
        if (el) el.textContent = msg;
    }

    form.addEventListener('submit', function (e) {
        setError('err-email', '');
        setError('err-password', '');
        var valid = true;

        var email = document.getElementById('email').value.trim();
        var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRe.test(email)) {
            setError('err-email', 'Enter a valid email address.');
            valid = false;
        }

        var pw = document.getElementById('password').value;
        if (pw === '') {
            setError('err-password', 'Password is required.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
})();
</script>
