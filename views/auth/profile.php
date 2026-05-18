
<div class="profile-wrap">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>"
                         alt="Profile picture" class="profile-avatar__img">
                <?php else: ?>
                    <div class="profile-avatar__placeholder">
                        <?php echo strtoupper(mb_substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                <span class="profile-role-badge profile-role-badge--<?php echo $user['role']; ?>">
                    <?php echo ucfirst($user['role']); ?>
                </span>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="auth-alert auth-alert--success"><p><?php echo htmlspecialchars($success); ?></p></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="auth-alert auth-alert--error">
                <?php foreach ($errors as $e): ?>
                    <p><?php echo htmlspecialchars($e); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php include __DIR__ . '/../partials/csrf_flash.php'; ?>

        <!-- ── Update Profile ── -->
        <h2 class="profile-section-title">Update Profile</h2>
        <form method="post" action="index.php?page=profile" enctype="multipart/form-data"
              id="profile-form" novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="update_profile">

            <div class="auth-field">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required
                       value="<?php echo htmlspecialchars($user['name']); ?>">
                <span class="field-error" id="err-name"></span>
            </div>
            <div class="auth-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required
                       value="<?php echo htmlspecialchars($user['email']); ?>">
                <span class="field-error" id="err-email"></span>
            </div>
            <div class="auth-field">
                <label for="address">Address</label>
                <input type="text" id="address" name="address"
                       placeholder="Street, city, postal code"
                       value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
            </div>
            <div class="auth-field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone"
                       placeholder="+880 1xxx xxxxxx"
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                <span class="field-error" id="err-phone"></span>
            </div>
            <div class="auth-field">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture"
                       accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="auth-hint">JPEG, PNG, GIF or WEBP — max 2 MB</small>
            </div>
            <button type="submit" class="auth-submit">Save Changes</button>
        </form>

        <hr class="profile-divider">

        <!-- ── Change Password ── -->
        <h2 class="profile-section-title">Change Password</h2>
        <form method="post" action="index.php?page=profile"
              id="password-form" novalidate>
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="change_password">

            <div class="auth-field">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required placeholder="••••••••">
                <span class="field-error" id="err-current"></span>
            </div>
            <div class="auth-field">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required placeholder="Min. 8 characters">
                <span class="field-error" id="err-newpw"></span>
            </div>
            <div class="auth-field">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat new password">
                <span class="field-error" id="err-confirm"></span>
            </div>
            <button type="submit" class="auth-submit">Change Password</button>
        </form>
    </div>
</div>

<script>
(function () {
    /* ── profile info form ── */
    var profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            var valid = true;

            function setError(id, msg) {
                var el = document.getElementById(id);
                if (el) el.textContent = msg;
            }
            ['err-name','err-email','err-phone'].forEach(function(id){ setError(id,''); });

            var name = document.getElementById('name').value.trim();
            if (name === '') { setError('err-name', 'Name is required.'); valid = false; }

            var email = document.getElementById('email').value.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                setError('err-email', 'Enter a valid email address.');
                valid = false;
            }

            var phone = document.getElementById('phone').value.trim();
            if (phone !== '' && !/^[+]?[\d\s\-()]{7,20}$/.test(phone)) {
                setError('err-phone', 'Enter a valid phone number.');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    /* ── change password form ── */
    var passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function (e) {
            var valid = true;

            function setError(id, msg) {
                var el = document.getElementById(id);
                if (el) el.textContent = msg;
            }
            ['err-current','err-newpw','err-confirm'].forEach(function(id){ setError(id,''); });

            var cur = document.getElementById('current_password').value;
            if (cur === '') { setError('err-current', 'Current password is required.'); valid = false; }

            var nw = document.getElementById('new_password').value;
            if (nw.length < 8) { setError('err-newpw', 'New password must be at least 8 characters.'); valid = false; }

            var cf = document.getElementById('confirm_password').value;
            if (nw !== cf) { setError('err-confirm', 'Passwords do not match.'); valid = false; }

            if (!valid) e.preventDefault();
        });
    }
})();
</script>
