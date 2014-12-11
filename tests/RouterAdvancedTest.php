<?php
use Bike\Router;

class RouterAdvancedTest extends PHPUnit_Framework_TestCase
{
    public function testStatic()
    {
        $router = new Router();

        $router->add('static',
            array(
                'method' => 'GET, POST',
                'route' => '/some/static/path',
                'defaults' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'format' => 'html'
                )
            )
        );

        $result = $router->match('GET', '/some/static/path');

        $expected = array(
            'url' => array(
                'static0' => 'some',
                'static1' => 'static',
                'static2' => 'path'
            ),
            'id' => 'static',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'controller' => 'index',
                'action' => 'index',
                'format' => 'html',
                'static0' => 'some',
                'static1' => 'static',
                'static2' => 'path'
            )
        );

        $this->assertSame($expected, $result);
    }

    public function testStaticAndDynamic()
    {
        $router = new Router();

        $router->add('static-and-dynamic',
            array(
                'method' => 'GET, POST',
                'route' => '/r/$subreddit/comments/#thread_id/$thread_slug/'
            )
        );

        $result = $router->match('GET', '/r/php/comments/12/router/');

        $expected = array(
            'url' => array(
                'static0' => 'r',
                'subreddit' => 'php',
                'static1' => 'comments',
                'thread_id' => '12',
                'thread_slug' => 'router'
            ),
            'id' => 'static-and-dynamic',
            'method' => array(
                0 => 'GET',
                1 => 'POST'
            ),
            'data' => array(
                'static0' => 'r',
                'subreddit' => 'php',
                'static1' => 'comments',
                'thread_id' => '12',
                'thread_slug' => 'router',
                'action' => 'view'
            )
        );

        $this->assertSame($expected, $result);
    }
}