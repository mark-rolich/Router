<?php
$routes = include 'predefined_routes.php';

$router = new Core\Router($routes);

$result = $router->compile(false);
?>