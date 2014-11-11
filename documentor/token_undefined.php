<?php
$router = new Router();

$router->add('user-defined-token',
    array(
        'method' => 'GET, POST',
        'route' => '(/controller(/action(/page)))'
    )
);
try {
    $result = $router->match('GET', '/news/view/12');
} catch (RouterException $e) {
    if ($e->getCode() === 0) {
        $result = $e->getMessage();
    }
}

// output: Token "page" is undefined
?>