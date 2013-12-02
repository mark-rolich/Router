<?php
/**
 * Regular expression based URL router
 *
 * Routes are defined using simple DSL,
 * class converts them to regular expressions
 * and searches for a match (on a first-match basis)
 *
 * @author Mark Rolich <mark.rolich@gmail.com>
 */
class Router
{
    /**
     * @var array - routes list
     */
    private $routes;

    /**
     * @var array - short codes for common patterns
     */
    private $shortCodes = array(
        '$' => '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*',  // php variable
        ':' => '[A-Za-z0-9]+',                              // alphanumeric
        '#' => '[0-9]+',                                    // numeric
        '*' => '(.*)',                                      // wildcard
        '~' => '[a-z]{1,5}',                                // extension
        '^' => '[A-Za-z0-9\-]+'                             // alphanumeric + hyphen
    );

    /**
     * @var array - characters to replace or escape
     */
    private $charMap = array(
        '(' => '(?:',
        ')' => ')?',
        '/' => '\/',
        '-' => '\-',
        '.' => '\.'
    );

    /**
     * @var array - last matched route
     */
    private $lastMatch = array();

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
     * Match provided url to routes list
     * return first match
     *
     * @param string $url - request url
     * @return array - matches list
     */
    public function match($url)
    {
        $result = array();

        $urlParts = parse_url($url);

        if (isset($urlParts['path'])) {
            foreach ($this->routes as $name => $route) {
                if (isset($route['regex'])) {
                    $regex = $route['regex'];
                } else {
                    $regex = $this->routeToRegex($route['route']);
                    $this->routes[$name]['regex'] = $regex;
                }

                preg_match($regex, $urlParts['path'], $matches);

                if (!empty($matches)) {
                    foreach ($matches as $k => $match) {
                        if (is_numeric($k)) {
                            unset($matches[$k]);
                        }
                    }

                    $matches = array_filter($matches);

                    $result['url'] = $matches;

                    if (isset($route['defaults'])) {
                        $matches = array_merge($route['defaults'], $matches);
                    }

                    if (isset($urlParts['query'])) {
                        parse_str($urlParts['query'], $query);
                        $matches = array_merge($query, $matches);
                    }

                    $result['id'] = $name;
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
     * Convert route to regular expression
     *
     * @param string $route - route
     * @return string - constructed regular expression
     */
    private function routeToRegex($route)
    {
        $result = '/^';

        $placeholder = null;
        $regex = null;

        $len = strlen($route);

        for ($i = 0; $i < $len; $i++) {
            switch ($route[$i]) {
                case '/':
                case '(':
                case ')':
                    if ($placeholder !== null && $regex !== null) {
                        $result .= '(?P<' . $placeholder . '>' . $regex . ')';

                        $placeholder = null;
                        $regex = null;
                    }

                    $result .= $this->charMap[$route[$i]];

                    break;
                case '-':
                    if ($placeholder !== null && $regex !== null) {
                        $result .= '(?P<' . $placeholder . '>' . $regex . ')'
                                . $this->charMap[$route[$i]];
                    } else {
                        $regex .= $route[$i];
                    }

                    break;
                case '<':
                    $regex = '';
                    $result .= '(?P<' . $placeholder . '>';
                    $placeholder = null;

                    break;
                case '>':
                    if ($regex !== null) {
                        $result .= $regex . ')';
                        $regex = null;
                    }

                    break;
                case '$':
                case ':':
                case '#':
                case '*':
                case '~':
                case '^':
                    $placeholder = '';
                    $regex = $this->shortCodes[$route[$i]];

                    break;
                default:
                    if ($placeholder !== null) {
                        $placeholder .= $route[$i];
                    } elseif ($regex !== null) {
                        $regex .= $route[$i];
                    } else {
                        if (array_key_exists($route[$i], $this->charMap)) {
                            $result .= $this->charMap[$route[$i]];
                        } else {
                            $result .= $route[$i];
                        }
                    }
            }
        }

        if ($placeholder !== null && $regex !== null) {
            $result .= '(?P<' . $placeholder . '>' . $regex . ')';
        }

        $result .= '$/D';

        return $result;
    }

    /**
     * Generate URL with specified parameters
     * based on the previously matched route
     *
     * @param array $params - parameters to join or replace to route array
     * @param array $route - previously parsed route from Router::match() method
     * @param string $type - type of URL
     * full - $params will be merged with the full route
     * url - $params will be merged with the route url array (default)
     * self - no merge will be preformed
     * @return string - constructed URL
     */
    public function url($params, $route = null, $type = 'url')
    {
        $result = '';

        if ($route === null) {
            $route = $this->lastMatch;
        }

        $data = $route['data'];
        $id = $route['id'];
        $url = $route['url'];

        $ruleRoute = $this->routes[$id]['route'];
        $pattern = '/(?:(?P<delims>[\/|\.|-]))(?:\()?[$|:|#|*|~|^](?P<placeholders>[a-z]+)/';

        preg_match_all($pattern, $ruleRoute, $matches);

        if (!empty($matches)) {
            foreach ($matches as $k => $match) {
                if (is_numeric($k)) {
                    unset($matches[$k]);
                }
            }

            switch ($type) {
                case 'url':
                    $data = array_merge($url, $params);
                    break;
                case 'full':
                    $data = array_merge($data, $params);
                    break;
                case 'self':
                    $data = $params;
                    break;
            }

            $groupData = array_intersect_key(
                array_combine($matches['placeholders'], $matches['delims']),
                $data
            );

            $data = array_intersect_key($data, $groupData);

            foreach ($groupData as $placeholder => $delim) {
                $result .= $delim . $data[$placeholder];
            }
        }

        return $result;
    }

    /**
     * Add route to routes list
     *
     * @param string $name - route name
     * @param string $route - route string
     * @param array $defaults - default options
     */
    public function add($name, $route, $defaults = array())
    {
        $this->routes[$name] = array(
            'route'     => $route,
            'defaults'  => $defaults
        );
    }

    /**
     * Add compiled regular expressions to routes and
     * output resulting routes array
     */
    public function compile()
    {
        foreach ($this->routes as $name => $route) {
            if (!isset($route['regex'])) {
                $regex = $this->routeToRegex($route['route']);
            } else {
                $regex = $route['regex'];
            }

            $this->routes[$name]['regex'] = htmlspecialchars($regex);
        }

        echo '<pre>&lt;?php return ' . stripcslashes(var_export($this->routes, true)) . '; ?&gt;</pre>';
    }
}
?>