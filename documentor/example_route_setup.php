<?php
$router = new Core\Router();

$router->add('controller-action-id',
    array(
        'method' => 'GET, POST',
        'route' => '/controller/action/#id'
    )
);

$router->add('controller-action',
    array(
        'method' => 'GET, POST',
        'route' => '/controller/action'
    )
);

$router->add('controller',
    array(
        'method' => 'GET, POST',
        'route' => '/controller'
    )
);
?>