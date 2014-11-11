<?php
$router = new Router();

$router->addToken('page', '[0-9]+');

$router->add('user-defined-token',
    array(
        'method' => 'GET, POST',
        'route' => '(/controller(/action(/page)))'
    )
);

$result = $router->match('GET', '/news/view/12');
?>