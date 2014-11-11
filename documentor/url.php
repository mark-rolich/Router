<?php
$router = new Router();

$router->addToken('page', '[0-9]+');

$router->add('url',
    array(
        'method' => 'GET',
        'route' => '(/controller(/action(/page)))',
        'defaults' => array(
            'controller' => 'index',
            'action' => 'index',
            'page' => 1
        )
    )
);

$result1 = $router->url(array(
    'controller' => 'news',
    'page' => 2
), 'url');

// output: '/news/index/2'

$result2 = $router->url(array(
    'controller' => 'news',
    'page' => 2
), 'url', true);

// output: '/news/2'
?>