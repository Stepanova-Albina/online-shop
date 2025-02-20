<?php

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestUri === '/registration') {
    if ($requestMethod === 'GET') {
        require_once './registration/registration_form.php';
    } elseif ($requestMethod === 'POST') {
        require_once './registration/handle_registration_form.php';
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
} elseif ($requestUri === '/login') {
    if ($requestMethod === 'GET') {
        require_once './login/login_form.php';
    } elseif ($requestMethod === 'POST') {
        require_once './login/handle_login.php';
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
} elseif ($requestUri === '/catalog') {
    if ($requestMethod === 'GET') {
        require_once './catalog/catalog.php';
    } elseif ($requestMethod === 'POST') {
        require_once './catalog/handle_catalog.php';
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
} elseif ($requestUri === '/profile') {
    require_once './profile/handle_profile_page.php';
} elseif ($requestUri === '/profile-edit') {
    if ($requestMethod === 'GET') {
        require_once './profileEdit/profile_edit.php';
    } elseif ($requestMethod === 'POST') {
        require_once './profileEdit/handle_profile_edit.php';
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
}
elseif ($requestUri === '/cart') {
    require_once './cart/cart.php';
}
else {
    http_response_code(404);
    require_once './404.php';
}