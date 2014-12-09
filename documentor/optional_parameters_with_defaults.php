<?php
$router = new Core\Router();

$router->add('optional-controller-and-action-with-defaults',
    array(
        'method' => 'GET, POST',
        'route' => '/(controller(/action))',
        'defaults' => array(
            'controller' => 'index',
            'action' => 'index'
        )
    )
);

$result1 = $router->match('GET', '/news');
$result2 = $router->match('GET', '/');
?>