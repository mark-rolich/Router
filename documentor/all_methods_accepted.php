<?php
try {
    $router = new Core\Router();

    $router->add('all-methods',
        array(
            'method' => '*',
            'route' => '/controller'
        )
    );

    $result = $router->match('PUT', '/news');
} catch (Core\RouterException $e) {
    $result = $e->getMessage();
}
?>