<?php
$router = new Core\Router();

$router->add('controller-only',
    array(
        'method' => 'GET, POST',
        'route' => '/controller'
    )
);

$result = $router->match('GET', '/news');
?>