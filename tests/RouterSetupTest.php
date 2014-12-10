<?php
use Bike\Router;

class RouterSetupTest extends PHPUnit_Framework_TestCase
{
    private $router;

    public function setUp()
    {
        $this->router = new Router(array(
            'controller-action-id' => array(
                'route'     => '/controller/action/#id'
            ),
            'controller-action' => array(
                'route'     => '/controller/action'
            ),
            'general' => array(
                'route'     => '(/controller)(/action(.format<[a-z]{2,4}>))(/#id)(/slug<[A-Za-z0-9\-]+>)',
                'defaults'  => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'format' => 'html',
                    'id' => 1,
                    'slug' => 'default-slug'
                )
            )
        ));
    }

    public function testGeneralRoute()
    {
        $result = $this->router->match('GET', '/news/add.xml/12/some-slug');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add',
                'format' => 'xml',
                'id' => '12',
                'slug' => 'some-slug'
            ),
            'id' => 'general',
            'method' => 'GET',
            'data' => array(
                'controller' => 'news',
                'action' => 'add',
                'format' => 'xml',
                'id' => '12',
                'slug' => 'some-slug'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testControllerActionIdRoute()
    {
        $result = $this->router->match('GET', '/news/add/12');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add',
                'id' => '12'
            ),
            'id' => 'controller-action-id',
            'method' => 'GET',
            'data' => array(
                'controller' => 'news',
                'action' => 'add',
                'id' => '12'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testControllerActionRoute()
    {
        $result = $this->router->match('GET', '/news/add');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add'
            ),
            'id' => 'controller-action',
            'method' => 'GET',
            'data' => array(
                'controller' => 'news',
                'action' => 'add'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testCompileArray()
    {
        $result = $this->router->compile();

        $expected = array(
            'controller-action-id' => array(
                'route' => '/controller/action/#id',
                'regex' => '/^\/(?P<controller>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\/(?P<action>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\/(?P<id>[0-9]+)$/D'
            ),
            'controller-action' => array(
                'route' => '/controller/action',
                'regex' => '/^\/(?P<controller>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\/(?P<action>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/D'
            ),
            'general' => array(
                'route' => '(/controller)(/action(.format<[a-z]{2,4}>))(/#id)(/slug<[A-Za-z0-9\-]+>)',
                'defaults' =>   array(
                    'controller' => 'index',
                    'action' => 'index',
                    'format' => 'html',
                    'id' => 1,
                    'slug' => 'default-slug',
                ),
                'regex' => '/^(?:\/(?P<controller>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*))?(?:\/(?P<action>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(?:\.(?P<format>[a-z]{2,4}))?)?(?:\/(?P<id>[0-9]+))?(?:\/(?P<slug>[A-Za-z0-9\-]+))?$/D'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testCompileString()
    {
        $result = $this->router->compile(false);
        $this->assertStringEqualsFile('tests/compiled.php', $result);
    }

    public function testCompiledRegex()
    {
        $router = new Router($this->router->compile());

        $result = $router->match('GET', '/news/add.xml/12/some-slug');

        $expected = array(
            'url' => array(
                'controller' => 'news',
                'action' => 'add',
                'format' => 'xml',
                'id' => '12',
                'slug' => 'some-slug'
            ),
            'id' => 'general',
            'method' => 'GET',
            'data' => array(
                'controller' => 'news',
                'action' => 'add',
                'format' => 'xml',
                'id' => '12',
                'slug' => 'some-slug'
            )
        );

        // should use regex instead of compiling

        $router->compile();

        $this->assertSame($expected, $result);
    }

    public function tearDown()
    {
        unset($this->router);
    }
}