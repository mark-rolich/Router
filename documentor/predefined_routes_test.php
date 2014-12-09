<?php
$routes = include 'predefined_routes.php';

$router = new Core\Router($routes);

$result1 = $router->match('GET', '/news/add.xml/12/some-slug');
$result2 = $router->match('GET', '/news/add/12');
$result3 = $router->match('GET', '/news/add');
?>