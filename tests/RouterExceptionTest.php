<?php
use Bike\Router;

class RouterExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Bike\RouterException
     * @expectedExceptionMessage Method PUT is not allowed
     * @expectedExceptionCode 2
     */
    public function testMethodNotAcceptable()
    {
        $router = new Router();

        $router->add('controller-only',
            array(
                'method' => 'GET, POST',
                'route' => '/controller'
            )
        );

        $router->match('PUT', '/news');
    }

    /**
     * @expectedException Bike\RouterException
     * @expectedExceptionMessage Method TEST is not supported
     * @expectedExceptionCode 1
     */
    public function testMethodNotSupported()
    {
        $router = new Router();

        $router->add('controller-only',
            array(
                'method' => 'GET, TEST',
                'route' => '/controller'
            )
        );

        $router->match('GET', '/news');
    }

    public function testAllMethodsSupported()
    {
        $router = new Router();

        $router->add('all-methods',
            array(
                'method' => '*',
                'route' => '/controller'
            )
        );

        $result = $router->match('PUT', '/news');

        $expected = array(
            'url' => array(
                'controller' => 'news'
            ),
            'id' => 'all-methods',
            'method' => array(
                0 => 'GET',
                1 => 'POST',
                2 => 'PUT',
                3 => 'DELETE'
            ),
            'data' => array(
                'controller' => 'news',
                'action' => 'update'
            )
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testRepeatingTokens()
    {
        $router = new Router();

        $router->addToken('r', '[a-z]+');

        $router->add('repeating-tokens',
            array(
                'method' => 'GET, POST',
                'route' => '/r/r/$subreddit/comments/#thread_id/$thread_slug/'
            )
        );

        $router->match('GET', '/r/r/php/comments/12/router/');
    }
}

?>