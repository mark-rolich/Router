<?php
$router = new Bike\Router();

$router->add('static-and-dynamic',
    array(
        'method' => 'GET, POST',
        'route' => '/r/$subreddit/comments/#thread_id/$thread_slug/'
    )
);

$result = $router->match('GET', '/r/php/comments/12/router/');
?>