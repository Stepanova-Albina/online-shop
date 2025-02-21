<?php

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

require_once './classes/User.php';
$user = new User();
if ($requestUri === '/registration') {
    if ($requestMethod === 'GET') {
        $user->getRegistrate();
    } elseif ($requestMethod === 'POST') {
        $user->registrate();
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
} elseif ($requestUri === '/login') {
    if ($requestMethod === 'GET') {
        $user->getLogin();
    } elseif ($requestMethod === 'POST') {
        $user->login();
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
} elseif ($requestUri === '/catalog') {
    require_once './classes/Product.php';
    $product = new Product();
    if ($requestMethod === 'GET') {
        $product->getCatalog();
    } elseif ($requestMethod === 'POST') {
        $product->addProduct();
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
} elseif ($requestUri === '/profile') {
    $user->getProfile();
} elseif ($requestUri === '/profile-edit') {
    if ($requestMethod === 'GET') {
        $user->getProfileEdit();
    } elseif ($requestMethod === 'POST') {
        $user->profileEdit();
    } else {
        echo "$requestMethod для адреса $requestUri не поддерживается";
    }
}
elseif ($requestUri === '/cart') {
    require_once './classes/Cart.php';
    $cart = new Cart();
    $cart->getCart();
}
else {
    http_response_code(404);
    require_once './404.php';
}