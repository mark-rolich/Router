<?php
$routes = include 'predefined_routes.php';

$router = new Router($routes);

$result = $router->compile();
?>