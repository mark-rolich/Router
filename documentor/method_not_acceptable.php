<?php
use Bike\Router;
use Bike\RouterException;

try {
    $router = new Router();

    $router->add('controller-only',
        array(
            'method' => 'GET, POST',
            'route' => '/controller'
        )
    );

    $result = $router->match('PUT', '/news');
} catch (RouterException $e) {
    if ($e->getCode() === 2) {
        $result = $e->getMessage();
    }
}

// output: 'Method PUT is not allowed'
?>