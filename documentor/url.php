<?php
$router = new Bike\Router();

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

$router->add('static-and-dynamic',
    array(
        'method' => 'GET, POST',
        'route' => '/r/$subreddit/comments/$thread_id/$thread_slug/'
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

$result3 = $router->url(array(
    'subreddit' => 'javascript',
    'thread_id' => '10',
    'thread_slug' => 'router',
), 'static-and-dynamic');

// output: '/r/javascript/comments/10/router'
?>