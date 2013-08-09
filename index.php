<?php
include 'Router.php';

function output($data)
{
    return '<pre>' . print_r($data, true) . '</pre>';
}

$router = new Router();

$router->add(
    'controller-only',
    '/$controller'
);

$result = $router->match('/news');

echo output($result);

$router = new Router();

$router->add(
    'controller-and-action',
    '/$my_controller/$action'
);

$result = $router->match('/news/add');

echo output($result);

$router = new Router();

$router->add(
    'optional-controller-and-action',
    '/($controller)(/$action)',
    array(
        'controller' => 'index',
        'action' => 'index'
    )
);

$result = $router->match('/news/add');
echo output($result);

$result = $router->match('/news');
echo output($result);

$result = $router->match('/');
echo output($result);

$router = new Router();

$router->add(
    'optional-controller-and-action',
    '/($controller)(/$action)',
    array(
        'controller' => 'index',
        'action' => 'index'
    )
);

$result = $router->match('/news/add?slug=some-slug&id=12');
echo output($result);

$router = new Router();

$router->add(
    'in-place-regex',
    '/($controller<[A-Z]{2}>)(/$action)',
    array(
        'controller' => 'index',
        'action' => 'index'
    )
);

$result = $router->match('/news/add');
echo output($result);

$result = $router->match('/AB/add');
echo output($result);

$router = new Router();

$router->add(
    'article-with-slug',
    '/$controller-$action(/^slug)',
    array(
        'controller' => 'index',
        'action' => 'index',
        'format' => 'html'
    )
);

$result = $router->match('/news-add/some-article-title');
echo output($result);

$router = new Router();

$router->add(
    'article-with-date-and-slug',
    '(/$controller)(/$action(.~format))(/#year-#month-#day)(/^slug)',
    array(
        'controller' => 'index',
        'action' => 'index',
        'format' => 'html'
    )
);

$result = $router->match('/articles/2009-01-01/some-slug-for-article');
echo output($result);

$router = new Router();

$router->add(
    'controller-action-id',
    '/$controller/$action/#id'
);

$router->add(
    'controller-action',
    '/$controller/$action'
);

$router->add(
    'controller',
    '/$controller'
);

$result = $router->match('/news/add/12');
echo output($result);

$result = $router->match('/news/add');
echo output($result);

$result = $router->match('/news');
echo output($result);

$routes = include 'routes.php';

$router = new Router($routes);

$result = $router->match('/news/add.xml/12/some-slug');
echo output($result);

$result = $router->match('/news/add/12');
echo output($result);

$result = $router->match('/news/add');
echo output($result);

$router->compile();
?>