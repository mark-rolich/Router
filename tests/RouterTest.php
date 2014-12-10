<?php
use Bike\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testController()
    {
        $router = new Router();

        $router->add('controller-only',
            array(
                'method' => 'GET, POST',
                'route' => '/controller'
            )
        );

        $result = $router->match('GET', '/news');

        $expected = array(
            'url' => array(
                'controller' => 'news'
            ),
            'id' => 'controller-only',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'news',
                'action' => 'view'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testCustomControllerAction()
    {
        $router = new Router();

        $router->add('controller-and-action',
            array(
                'method' => 'GET, POST',
                'route' => '/$my_controller/:my_action'
            )
        );

        $result = $router->match('GET', '/news/add');

        $expected = array(
            'url' => array(
                'my_controller' => 'news',
                'my_action' => 'add'
            ),
            'id' => 'controller-and-action',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'my_controller' => 'news',
                'my_action' => 'add',
                'action' => 'view'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testOptionalParameters()
    {
        $router = new Router();

        $router->add('optional-controller-and-action',
            array(
                'method' => 'GET, POST',
                'route' => '/(controller(/action))'
            )
        );

        $result = $router->match('GET', '/news/add');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add'
            ),
            'id' => 'optional-controller-and-action',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'news',
                'action' => 'add'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testOPtionalParametersWithDefaults()
    {
        $router = new Router();

        $router->add('optional-controller-and-action-with-defaults',
            array(
                'method' => 'GET, POST',
                'route' => '/(controller(/action))',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index'
                )
            )
        );

        $result = $router->match('GET', '/news');

        $expected = array(
            'url' => array(
                'controller' => 'news'
            ),
            'id' => 'optional-controller-and-action-with-defaults',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'news',
                'action' => 'index'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testQueryString()
    {
        $router = new Router();

        $router->add('query-string',
            array(
                'method' => 'GET, POST',
                'route' => '/(controller(/action))',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index'
                )
            )
        );

        $result = $router->match('GET', '/news/add?slug=some-slug&id=12');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add'
            ),
            'id' => 'query-string',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'slug' => 'some-slug',
                'id' => '12',
                'controller' => 'news',
                'action' => 'add'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testInPlaceRegex()
    {
        $router = new Router();

        $router->add('in-place-regex',
            array(
                'method' => 'GET, POST',
                'route' => '(/controller<[A-Z]{2}>(/action))'
            )
        );

        $result = $router->match('GET', '/AB/add');

        $expected = array(
            'url' => array(
                'controller' => 'AB',
                'action' => 'add'
            ),
            'id' => 'in-place-regex',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'AB',
                'action' => 'add'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testUserDefinedToken()
    {
        $router = new Router();

        $router->addToken('page', '[0-9]+');

        $router->add('user-defined-token',
            array(
                'method' => 'GET, POST',
                'route' => '(/controller(/action(/page)))'
            )
        );

        $result = $router->match('GET', '/news/view/12');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'view',
                'page' => '12'
            ),
            'id' => 'user-defined-token',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'news',
                'action' => 'view',
                'page' => '12'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testSlug()
    {
        $router = new Router();

        $router->add('article-with-slug',
            array(
                'method' => 'GET, POST',
                'route' => '/controller-action(/^slug)',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'format' => 'html'
                )
            )
        );

        $result = $router->match('GET', '/news-add/some-article-title');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add',
                'slug' => 'some-article-title'
            ),
            'id' => 'article-with-slug',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'news',
                'action' => 'add',
                'format' => 'html',
                'slug' => 'some-article-title'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testAdvanced()
    {
        $router = new Router();

        $router->addToken('y', '[0-9]{4}');
        $router->addToken('m', '[0-9]{2}');
        $router->addToken('d', '[0-9]{2}');

        $router->add('article-with-date-and-slug',
            array(
                'method' => 'GET, POST',
                'route' => '(/controller)(/action(.~format))(/y-m-d(/^slug))',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'format' => 'html'
                )
            )
        );

        $result = $router->match('GET', '/articles/2009-01-01/some-slug-for-article');

        $expected = array(
            'url' => array(
                'controller' => 'articles',
                'y' => '2009',
                'm' => '01',
                'd' => '01',
                'slug' => 'some-slug-for-article'
            ),
            'id' => 'article-with-date-and-slug',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'articles',
                'action' => 'index',
                'format' => 'html',
                'y' => '2009',
                'm' => '01',
                'd' => '01',
                'slug' => 'some-slug-for-article'
            )
        );

        $this->assertSame($expected, $result);
    }

}
?>