<?php
use Bike\Router;

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

        $this->router->add('static',
            array(
                'method' => 'GET, POST',
                'route' => '/some/one/static/path',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'format' => 'html'
                )
            )
        );

        $this->router->add('static-and-dynamic',
            array(
                'method' => 'GET, POST',
                'route' => '/r/$subreddit/comments/$thread_id/$thread_slug/'
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

    public function testStaticUrl()
    {
        $result = $this->router->url(array(), 'static');

        $expected = '/some/one/static/path';

        $this->assertSame($expected, $result);
    }

    public function testStaticAndDynamicUrl()
    {
        $result = $this->router->url(array(
            'subreddit' => 'javascript',
            'thread_id' => '10',
            'thread_slug' => 'router',
        ), 'static-and-dynamic');

        $expected = '/r/javascript/comments/10/router';

        $this->assertSame($expected, $result);
    }
}
?>