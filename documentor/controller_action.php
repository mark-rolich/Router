<?php
$router = new Core\Router();

$router->add('controller-and-action',
    array(
        'method' => 'GET, POST',
        'route' => '/$my_controller/:my_action'
    )
);

$result = $router->match('GET', '/news/add');
?>