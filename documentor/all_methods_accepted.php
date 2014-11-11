<?php
try {
    $router = new Router();

    $router->add('all-methods',
        array(
            'method' => '*',
            'route' => '/controller'
        )
    );

    $result = $router->match('PUT', '/news');
} catch (RouterException $e) {
    $result = $e->getMessage();
}
?>