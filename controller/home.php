<?php

function home_index(): void
{
    $title = 'Sign in — Library';
    $view = __DIR__ . '/../views/auth/auth.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}
