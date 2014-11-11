<?php
/**
 * Class Router
 */

/**
 * Regular expression based URL router
 *
 * Uses simple DSL to define routes.
 * Routes can be attached to HTTP methods.
 * The class converts them to regular expressions
 * and searches for a match (on a first-match basis).
 * Provides possibility to add user defined tokens with matching regex.
 * Can reverse generate url from a specified route name.
 *
 * @author Mark Rolich <mark.rolich@gmail.com>
 */
class Router
{
    /**
     * @var array - list of routes
     */
    public $routes;

    /**
     * @var array - last matched route
     */
    private $lastMatch = array();

    /**
     * @var array - list of supported HTTP methods
     */
    private $methods = array(
        'GET' => 'view',
        'POST' => 'create',
        'PUT' => 'update',
        'DELETE' => 'delete'
    );

    /**
     * @var array - list of built-in tokens
     */
    protected $tokens = array(
        '(' => array('T_OPTIONAL_START',    '(?:'),
        ')' => array('T_OPTIONAL_END',      ')?'),
        '.' => array('T_DOT',               '\.'),
        '<' => array('T_REGEX_DELIM',       ''),
        '>' => array('T_REGEX_DELIM',       ''),
        ':' => array('T_PATTERN',           '[A-Za-z0-9]+'),
        '#' => array('T_PATTERN',           '[0-9]+'),
        '$' => array('T_PATTERN',           '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*'),
        '~' => array('T_PATTERN',           '[a-z]{1,5}'),
        '^' => array('T_PATTERN',           '[A-Za-z0-9\-]+'),
        '/' => array('T_PATH_DELIM',        '\/'),
        '-' => array('T_HYPHEN_DELIM',      '\-')
    );

    /**
     * @var array - list of user defined tokens
     */
    protected $userTokens = array(
        'controller'    => '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*',
        'action'        => '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*'
    );

    /**
     * Constructor
     *
     * @param array $routes - predefined routes array
     */
    public function __construct($routes = array())
    {
        $this->routes = $routes;
    }

    /**
     * Parse and validate route method string against HTTP request method
     *
     * @param string $method - HTTP request method
     * @param string $methods - method(s) string
     * (* - all methods | comma separated list of methods)
     * @throws RouterException
     * @return array - list of extracted methods
     */
    private function validateMethod($method, $methods)
    {
        $routerMethods = array_keys($this->methods);

        if ($methods === '*') {
            $methods = $routerMethods;
        } else {
            $methods = array_map('trim', explode(',', $methods));
            $diff = array_diff($methods, $routerMethods);

            if (!empty($diff)) {
                throw new RouterException('Method ' . implode(',', $diff) . ' is not supported', 1);
            }
        }

        if (!in_array($method, $methods)) {
            throw new RouterException('Method ' . $method . ' is not allowed', 2);
        }

        return $methods;
    }

    /**
     * Match provided method and url to routes list
     * return first match
     *
     * @param string $method - one of the supported HTTP methods
     * (from the Router::$methods array)
     * @param string - request url
     * @return array - matched route data | empty if there is no match
     */
    public function match($method, $url)
    {
        $result = array();

        $urlParts = parse_url($url);

        if (isset($urlParts['path'])) {
            foreach ($this->routes as $name => $route) {
                if (isset($route['regex'])) {
                    $regex = $route['regex'];
                } else {
                    $regex = $this->parse($this->tokenize($route['route']));
                    $this->routes[$name]['regex'] = $regex;
                }

                preg_match($regex, $urlParts['path'], $matches);

                $matches = array_filter($matches);

                if (!empty($matches)) {
                    $methods = (isset($route['method']))
                        ? $this->validateMethod($method, $route['method'])
                        : key($this->methods);

                    foreach ($matches as $k => $match) {
                        if (is_numeric($k)) {
                            unset($matches[$k]);
                        }
                    }

                    $result['url'] = $matches;
                    $result['id'] = $name;
                    $result['method'] = $methods;

                    if (isset($route['defaults'])) {
                        $matches = array_merge($route['defaults'], $matches);
                    }

                    if (!isset($matches['action'])) {
                        $matches['action'] = $this->methods[$method];
                    }

                    if (isset($urlParts['query'])) {
                        parse_str($urlParts['query'], $query);
                        $matches = array_merge($query, $matches);
                    }

                    $result['data'] = $matches;

                    $this->lastMatch = $result;

                    break;
                }

            }
        }

        $this->lastMatch = $result;

        return $result;
    }

    /**
     * Generate URL with specified parameters based on the specified route name
     *
     * @param array $params - parameters to join or replace to route array
     * @param string $routeName - route name
     * @param bool $skipOnEmpty - whether fill empty parameter with default value (if set) or not
     * @return string - constructed URL
     */
    public function url($params, $routeName, $skipOnEmpty = false)
    {
        $result = '';
        $regexUserTokens = implode('|', array_keys($this->userTokens));
        $pattern = '/(?:(?P<delims>[\/|\.|-]))(?:\()?(?:[$|:|#|*|~|^])?(?P<placeholders>(' . $regexUserTokens . '|[a-z]+))/';

        if (isset($this->routes[$routeName])) {
            $route = $this->routes[$routeName]['route'];
            $defaults = isset($this->routes[$routeName]['defaults'])
                ? $this->routes[$routeName]['defaults']
                : array();

            preg_match_all($pattern, $route, $matches);

            if (!empty($matches)) {
                foreach ($matches as $k => $match) {
                    if (is_numeric($k)) {
                        unset($matches[$k]);
                    }
                }

                $data = array_combine($matches['placeholders'], $matches['delims']);

                foreach ($data as $placeholder => $delim) {
                    if (isset($params[$placeholder])) {
                        $result .= $delim . $params[$placeholder];
                    } elseif (isset($defaults[$placeholder]) && !$skipOnEmpty) {
                        $result .= $delim . $defaults[$placeholder];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Tokenize route expression
     *
     * @param $route - route expression
     * @return array - tokens array
     */
    private function tokenize($route)
    {
        $tokens = array();

        $chunk = '';
        $currentToken = null;
        $regex_started = false;
        $pattern = null;

        for ($i = 0; $i < strlen($route); $i++) {
            if (isset($this->tokens[$route[$i]])) {
                $tokenData = $this->tokens[$route[$i]];

                if ($chunk !== '' && !$regex_started) {
                    array_push($tokens, array(
                        $currentToken,
                        $chunk
                    ));

                    if ($pattern !== null) {
                        array_push($tokens, $pattern);
                    }

                    $chunk = '';
                    $pattern = null;
                }

                if ($tokenData[0] === 'T_REGEX_DELIM') {
                    $regex_started = !$regex_started;
                    continue;
                }

                if (!$regex_started) {
                    if ($tokenData[0] === 'T_PATTERN') {
                        $pattern = $tokenData;
                    } else {
                        array_push($tokens, $tokenData);
                    }

                    $currentToken = $tokenData[0];
                } else {
                    $chunk .= $route[$i];
                }
            } else {
                $currentToken = ($regex_started)
                    ? 'T_PATTERN'
                    : 'T_NAMED_GROUP';

                $chunk .= $route[$i];
            }
        }

        array_push($tokens, array(
            $currentToken,
            $chunk
        ));

        array_push($tokens, $pattern);

        return $tokens;
    }

    /**
     * Parse tokens to regular expression
     *
     * @param $tokens - tokens array
     * @throws RouterException
     * @return string - regular expression
     */
    private function parse($tokens)
    {
        $regex = '';

        foreach ($tokens as $i => $tokenData) {
            $chunk = $tokenData[1];

            if ($tokenData[0] === 'T_NAMED_GROUP') {

                $nextTokenData = $tokens[$i + 1];

                $chunk = '(?P<' . $chunk . '>';

                if ($nextTokenData[0] !== 'T_PATTERN') {
                    if (!isset($this->userTokens[$tokenData[1]])) {
                        throw new RouterException('Token "' . $tokenData[1] . '" is not defined', 0);
                    } else {
                        $chunk .= $this->userTokens[$tokenData[1]] . ')';
                    }
                }
            }

            if ($tokenData[0] === 'T_PATTERN') {
                $chunk .= ')';
            }

            $regex .= $chunk;
        }

        return '/' . $regex . '/';
    }

    /**
     * Add route to routes list
     *
     * @param string $name - route name
     * @param array $route - route data
     */
    public function add($name, $route)
    {
        $this->routes[$name] = $route;
    }

    /**
     * Add token to user defined tokens list
     *
     * @param string $name - token name
     * @param string $regex - token regex
     */
    public function addToken($name, $regex)
    {
        $this->userTokens[$name] = $regex;
    }

    /**
     * Add compiled regular expressions to routes and
     * output resulting routes array
     */
    public function compile($return = true)
    {
        foreach ($this->routes as $name => $route) {
            if (!isset($route['regex'])) {
                $regex = $this->parse($this->tokenize($route['route']));
            } else {
                $regex = $route['regex'];
            }

            $this->routes[$name]['regex'] = htmlspecialchars($regex);
        }

        if ($return) {
            return $this->routes;
        } else {
            echo '<pre>&lt;?php return ' . stripcslashes(var_export($this->routes, true)) . '; ?&gt;</pre>';
        }
    }
}

/**
 * Class RouterException
 */
class RouterException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
?>