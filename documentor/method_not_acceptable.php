<?php
try {
    $router = new Core\Router();

    $router->add('controller-only',
        array(
            'method' => 'GET, POST',
            'route' => '/controller'
        )
    );

    $result = $router->match('PUT', '/news');
} catch (Core\RouterException $e) {
    if ($e->getCode() === 2) {
        $result = $e->getMessage();
    }
}

// output: 'Method PUT is not allowed'
?>