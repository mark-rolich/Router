<?php include 'documentation.php'; ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Router documentation</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
<div class="main-wrapper">
    <h1>Router</h1>

        <p>This PHP class matches url against predefined route patterns.</p>

        <p>Features</p>
        <ul>
            <li>Routes can be attached to HTTP methods, and follow simple DSL syntax.</li>
            <li>Uses short codes for the frequently used patterns</li>
            <li>Provides possibility to add user defined tokens with matching regex.</li>
            <li>Reverse generate url from a specified route name.</li>
        </ul>

        <div class="toc">
            <p><strong>Contents</strong></p>
            <ul>
                <li><a href="#short-codes">Short codes</a></li>
                <li><a href="#tokens">Predefined tokens</a></li>
                <li><a href="#methods-and-rest">HTTP methods and RESTful routing</a></li>
                <li><a href="#parameters">Named, optional and default parameters</a></li>
                <li><a href="#query-strings-and-regex-and-user-defined-tokens">Query strings, inline regular expressions and user defined tokens</a></li>
                <li><a href="#other">Other examples</a></li>
                <li><a href="#order-mass-assignment-and-compilation">Routes order, mass assignment and compilation</a></li>
                <li><a href="#url">Reverse URL generation for a given route</a></li>
            </ul>
        </div>

    <h2><a name="short-codes">Short codes</a></h2>

        <p>Router uses the following short codes for the common patterns:</p>

        <ul class="no-bullets">
            <li><strong>$</strong> - match the string according to php variable syntax ([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)</li>
            <li><strong>:</strong> - match alphanumeric string ([A-Za-z0-9]+)</li>
            <li><strong>#</strong> - match numeric string ([0-9]+)</li>
            <li><strong>~</strong> - match extension ([a-z]{1,5})</li>
            <li><strong>^</strong> - match alphanumeric with hyphen ([A-Za-z0-9\-]+)</li>
        </ul>

    <h2><a name="tokens">Predefined tokens</a></h2>
        <p>Router has the following predefined tokens:</p>

        <ul class="no-bullets">
            <li><strong>controller</strong> - match the string according to php variable syntax</li>
            <li><strong>action</strong> - match the string according to php variable syntax</li>
        </ul>

    <h2><a name="methods-and-rest">HTTP methods and RESTful routing</a></h2>
        <p>Router supports the following HTTP methods by default
            (and translates them to corresponding actions if needed):</p>

        <ul class="no-bullets">
            <li>GET - view</li>
            <li>POST - create</li>
            <li>PUT - update</li>
            <li>DELETE - delete</li>
        </ul>

        <p>Here we use predefined <strong>controller</strong> token, and indicate that route will accept only
            <strong>GET</strong> and <strong>POST</strong> requests:
        </p>

        <?php
        include 'controller_only.php';
        echo codeFile('controller_only.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

        <p>Note that action is set to 'view' by default, as there's no default value for 'action' parameter,
        so it's possible to implement <strong>REST</strong>ful routing using this approach.</p>

        <p>If no method is specified, the route will accept only GET requests by default.</p>

        <p>If route will be requested using not acceptable HTTP method, RouterException with code 2 will be thrown
            (which can be used to send appropriate HTTP header):</p>

        <?php
        include 'method_not_acceptable.php';
        echo codeFile('method_not_acceptable.php');
        ?>

        <p>If route method will contain unspecified HTTP method, RouterException with code 1 will be thrown:</p>

        <?php
        include 'method_not_supported.php';
        echo codeFile('method_not_supported.php');
        ?>

        <p>If route accepts all headers, an asterisk (<strong>*</strong>) could be set as a method:</p>

        <?php
        include 'all_methods_accepted.php';
        echo codeFile('all_methods_accepted.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

    <h2><a name="parameters">Named, optional and default parameters</a></h2>

        <p>Named placeholders should consist from uppercase and lowercase letters and underscores only,
            without spaces, hyphens etc. e.g. $my_controller, :my_action, #some_id</p>

        <?php
        include 'controller_action.php';
        echo codeFile('controller_action.php');
        ?>

        <p>Results:</p>

        <?php echo codeString($result); ?>

        <p>Optional parameters example</p>
        <?php
        include 'optional_parameters.php';
        echo codeFile('optional_parameters.php');
        ?>

        <p>Results:</p>

        <?php
        echo codeString($result1);
        echo codeString($result2);
        ?>

        <p>When using optional parameters, it makes sense to provide an array of default values:</p>

        <?php
        include 'optional_parameters_with_defaults.php';
        echo codeFile('optional_parameters_with_defaults.php');
        ?>

        <p>Results:</p>

        <?php
        echo codeString($result1);
        echo codeString($result2);
        ?>

    <h2><a name="query-strings-and-regex-and-user-defined-tokens">Query strings, inline regular expressions and user defined tokens</a></h2>

        <p>If url contains query string, it will be appended to the resulting data array
            (query string parameters will not override parameters parsed by regular expression)</p>

        <?php
        include 'query_string.php';
        echo codeFile('query_string.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

        <p>It is possible to use inline regular expressions:</p>

        <?php
        include 'in_place_regex.php';
        echo codeFile('in_place_regex.php');
        ?>

        <p>Results:</p>

        <div>No match</div>
        <?php echo codeString($result1); ?>

        <div>Has match</div>
        <?php echo codeString($result2); ?>

        <p>It is possible to add user defined tokens using Router::addToken() method for frequently used patterns:</p>
        <?php
        include 'user_defined_token.php';
        echo codeFile('user_defined_token.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

        <p>If route will use undefined token, RouterException with code 0 will be thrown:</p>

        <?php
        include 'token_undefined.php';
        echo codeFile('token_undefined.php');
        ?>

    <h2><a name="other">Other examples</a></h2>

        <?php
        include 'slug.php';
        echo codeFile('slug.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

        <?php
        include 'advanced.php';
        echo codeFile('advanced.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

    <h2><a name="order-mass-assignment-and-compilation">Routes order, mass assignment and compilation</a></h2>

        <p>As the Router works on a first-match basis, it's
            recommended to define routes in order of specificity,
            from most specific to general ones.</p>

        <?php echo codeFile('example_route_setup.php'); ?>

        <h2>Predefined routes</h2>

        <p>It's possible to have predefined routes in some php file,
            and provide them to Router as a parameter for constructor:</p>

        <?php echo codeFile('predefined_routes.php'); ?>

        <?php
        include 'predefined_routes_test.php';
        echo codeFile('predefined_routes_test.php');
        ?>

        <p>Result:</p>

        <?php
        echo codeString($result1);
        echo codeString($result2);
        echo codeString($result3);
        ?>

        <p>Using Router::compile method
        it's possible to get php code of routes array with
        compiled regular expressions, so Router will skip
        route to regex conversion thus increase its performance</p>

        <?php
        include 'predefined_routes.php';
        include 'compile.php';
        echo codeFile('compile.php');
        ?>

        <p>Result:</p>

        <?php echo codeString($result); ?>

    <h2><a name="url">Reverse URL generation for a given route</a></h2>

        <p>URLs can be generated using Router::url() method, which accepts 3 arguments:</p>

        <ul>
            <li>$params - associative array of keys and values that should be replaced</li>
            <li>$routeName - route name</li>
            <li>$skipOnEmpty - boolean indicating whether fill not provided parameters with default values (if set) or not</li>
        </ul>

        <?php
        include 'url.php';
        echo codeFile('url.php');
        ?>
</body>
</html>