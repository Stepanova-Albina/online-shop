<?php
namespace Core;

use Controller\UserController;
use Controller\ProductController;
use Controller\CartController;
class App
{
    private array $routes = [
        '/registration' => [
            'GET' => [
                'class' => UserController::class,
                'method' => 'getRegistrate'
            ],
            'POST' => [
                'class' => UserController::class,
                'method' => 'registrate'
            ]
        ],
        '/login' => [
            'GET' => [
                'class' => UserController::class,
                'method' => 'getLogin'
            ],
            'POST' => [
                'class' => UserController::class,
                'method' => 'login'
            ]
        ],
        '/catalog' => [
            'GET' => [
                'class' => ProductController::class,
                'method' => 'getCatalog'
            ],
            'POST' => [
                'class' => ProductController::class,
                'method' => 'addProduct'
            ]
        ],
        '/profile' => [
            'GET' => [
                'class' => UserController::class,
                'method' => 'getProfile'
            ]
        ],
        '/profile-edit' => [
            'GET' => [
                'class' => UserController::class,
                'method' => 'getProfileEdit'
            ],
            'POST' => [
                'class' => UserController::class,
                'method' => 'profileEdit'
            ]
        ],
        '/cart' => [
            'GET' => [
                'class' => CartController::class,
                'method' => 'getCart'
            ]
        ],
        '/logout' => [
            'GET' => [
                'class' =>UserController::class,
                'method' => 'logout'
                ]
            ]
    ];
    public function run()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$requestUri])) {
            $routeMethods = $this->routes[$requestUri];
            if (isset($routeMethods[$requestMethod])) {
                $handler = $routeMethods[$requestMethod];

                $class = $handler['class'];
                $method = $handler['method'];

                $controller = new $class();
                $controller->$method();
            } else {
                echo "$requestMethod для адреса $requestUri не поддерживается";
            }
        } else {
            http_response_code(404);
            require_once '../Views/404.php';
        }
    }
}