<?php
$router = new Core\Router();

$router->add('optional-controller-and-action',
    array(
        'method' => 'GET, POST',
        'route' => '/(controller(/action))'
    )
);

$result1 = $router->match('GET', '/news/add');
$result2 = $router->match('GET', '/news');
?>