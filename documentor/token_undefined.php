<?php
$router = new Bike\Router();

$router->addToken('non_static_1', '[a-z]+');
$router->addToken('non_static_2', '[a-z]+');

$router->add('user-defined-token',
    array(
        'method' => 'GET, POST',
        'route' => '/non_static_1/undefined/non_static_2'
    )
);

$result = $router->match('GET', '/some/undefined/tokens');
?>