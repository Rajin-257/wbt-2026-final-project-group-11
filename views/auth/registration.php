
<div class="auth-wrap">
    <div class="auth-card">
        <h1>Create account</h1>
        <p class="lead">Online Medicine Shop — register with your details.</p>

        <form method="post" action="" autocomplete="on">
            <div class="auth-field">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required placeholder="Your full name">
            </div>
            <div class="auth-field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com">
            </div>
            <div class="auth-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>
            <div class="auth-field">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Street, city, postal code">
            </div>
            <div class="auth-field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" placeholder="+1 555 000 0000">
            </div>
            <div class="auth-field">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Select role</option>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="auth-row">
                <span></span>
                <a href="index.php" class="auth-link">Sign in</a>
            </div>
            <button type="submit" class="auth-submit">Register</button>
        </form>
    </div>
</div>
