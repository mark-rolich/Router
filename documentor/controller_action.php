<?php
$router = new Bike\Router();

$router->add('controller-and-action',
    array(
        'method' => 'GET, POST',
        'route' => '/$my_controller/:my_action'
    )
);

$result = $router->match('GET', '/news/add');
?>