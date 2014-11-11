<?php
$router = new Router();

$router->add('article-with-slug',
    array(
        'method' => 'GET, POST',
        'route' => '/controller-action(/^slug)',
        'defaults' => array(
            'controller' => 'index',
            'action' => 'index',
            'format' => 'html'
        )
    )
);

$result = $router->match('GET', '/news-add/some-article-title');
?>