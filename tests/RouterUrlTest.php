<?php
use Core\Router;

class RouterUrlTest extends PHPUnit_Framework_TestCase
{
    private $router;

    public function setUp()
    {
        $this->router = new Router();
        $this->router->addToken('page', '[0-9]+');

        $this->router->add('url',
            array(
                'method' => 'GET',
                'route' => '(/controller(/action(/page)))',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'page' => 1
                )
            )
        );
    }

    public function testSkipOnEmptyFalse()
    {
        $result = $this->router->url(array(
            'controller' => 'news',
            'page' => 2
        ), 'url');

        $expected = '/news/index/2';

        $this->assertSame($expected, $result);
    }

    public function testSkipOnEmptyTrue()
    {
        $result = $this->router->url(array(
            'controller' => 'news',
            'page' => 2
        ), 'url', true);

        $expected = '/news/2';

        $this->assertSame($expected, $result);
    }
}
?>