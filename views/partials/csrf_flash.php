<?php
$csrfErr = csrf_take_flash_error();
if ($csrfErr !== ''):
?>
<div class="auth-alert auth-alert--error" role="alert">
    <p><?= e($csrfErr) ?></p>
</div>
<?php endif; ?>
