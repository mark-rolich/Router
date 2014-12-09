<?php
$router = new Core\Router();

$router->addToken('y', '[0-9]{4}');
$router->addToken('m', '[0-9]{2}');
$router->addToken('d', '[0-9]{2}');

$router->add('article-with-date-and-slug',
    array(
        'method' => 'GET, POST',
        'route' => '(/controller)(/action(.~format))(/y-m-d(/^slug))',
        'defaults' => array(
            'controller' => 'index',
            'action' => 'index',
            'format' => 'html'
        )
    )
);

$result = $router->match('GET', '/articles/2009-01-01/some-slug-for-article');
?>