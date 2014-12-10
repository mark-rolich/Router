<?php
$router = new Bike\Router();

$router->add('query-string',
    array(
        'method' => 'GET, POST',
        'route' => '/(controller(/action))',
        'defaults' => array(
            'controller' => 'index',
            'action' => 'index'
        )
    )
);

$result = $router->match('GET', '/news/add?slug=some-slug&id=12');
?>