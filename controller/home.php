<?php

function home_index()
{
    $title = 'Sign in — Medicine';
    $view = __DIR__ . '/../views/auth/login.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}

function register()
{
    $title = 'Register — Medicine';
    $view = __DIR__ . '/../views/auth/registration.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}
