<?php

function home()
{
    $title = 'Home — Medicine';
    $view = __DIR__ . '/../views/home.php';
    $layout = __DIR__ . '/../views/layout.php';
    include $layout;
}

function login()
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
