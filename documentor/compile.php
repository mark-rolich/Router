<?php
$routes = include 'predefined_routes.php';

$router = new Bike\Router($routes);

$result = $router->compile(false);
?>