<?php
$router = new Router();

$router->add('controller-and-action',
    array(
        'method' => 'GET, POST',
        'route' => '/$my_controller/:my_action'
    )
);

$result = $router->match('GET', '/news/add');
?>