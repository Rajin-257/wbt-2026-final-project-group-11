<?php
if (!function_exists('csrf_meta')) {
    require_once dirname(__DIR__, 2) . '/config/app.php';
}
$assetBase = defined('BASE_URL') ? BASE_URL : '';
echo csrf_meta();
?>
<script src="<?= e($assetBase) ?>/public/csrf.js"></script>
