<?php
$router = new Router();

$router->add('in-place-regex',
    array(
        'method' => 'GET, POST',
        'route' => '(/controller<[A-Z]{2}>(/action))'
    )
);

$result1 = $router->match('GET', '/news/add');
$result2 = $router->match('GET', '/AB/add');
?>