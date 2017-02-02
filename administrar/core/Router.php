<?php

class Router {

	private static $instance;

	/**
     * Array of routes
     *
     * @var Route[] $routes
     */
    protected $routes = array();

    /**
     * Matched Route, the current found Route, if any.
     */
    protected $matchedRoute = null;

    /**
     * Set an Error Callback
     *
     * @var null $errorCallback
     */
    private $errorCallback = 'SiteController@error';

	/**
     * Router constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct() {
        self::$instance =& $this;
    }

    public static function &getInstance() {
        if (!self::$instance) {
            $router = new Router();
        } else {
            $router =& self::$instance;
        }
        return $router;
    }

    /**
     * Defines a route with or without Callback and Method.
     *
     * @param string $method
     * @param array @params
     */
    public static function __callStatic($method, $params) {
        $router = self::getInstance();
        $router->addRoute($method, $params[0], $params[1]);
    }

    /**
     * Defines callback if route is not found.
     *
     * @param string $callback
     */
    public static function error($callback) {
        $router = self::getInstance();

        $router->callback($callback);
    }

    /**
     * Router Error Callback
     *
     * @param null $callback
     * @return callback|null
     */
    public function callback($callback = null) {
        if (is_null($callback)) {
            return $this->errorCallback;
        }

        $this->errorCallback = $callback;

        return null;
    }

    /**
     * Maps a Method and URL pattern to a Callback.
     *
     * @param string $method HTTP metod(s) to match
     * @param string $route URL pattern to match
     * @param callback $callback Callback object
     */
    public function addRoute($method, $route, $callback = null) {
        $methods = array_map('strtoupper', is_array($method) ? $method : array($method));
        $pattern = ltrim($route, '/');
        $route = new Route($methods, $pattern, $callback);
        // Add the current Route instance to the known Routes list.
        array_push($this->routes, $route);
    }

    /**
     * Dispatch route
     * @return bool
     */
    public function dispatch() {

    	// Detect the current URI.
        $uri = Url::detectUri();

    	 // Not an Asset File URI? Routes the current request.
        $method = Request::getMethod();

        //print_r($this->routes);

    	foreach ($this->routes as $route) {
    		//print_r($route);echo '<br />';
    		if ($route->match($uri, $method)) {
    			// Found a valid Route; process it.
                $this->matchedRoute = $route;
                $callback = $route->callback();
                if ($callback !== null) {
                    // Invoke the Route's Callback with the associated parameters.
                    return $this->invokeObject($callback, $route->params());
                }
                return true;
    		}
    	}

    	// No valid Route found; invoke the Error Callback with the current URI as parameter.
        $params = array(
            htmlspecialchars($uri, ENT_COMPAT, 'ISO-8859-1', true)
        );

        $this->invokeObject($this->callback(), $params);

        return false;
    }

    /**
     * Invoke the callback with its associated parameters.
     *
     * @param  callable $callback
     * @param  array $params array of matched parameters
     * @return bool
     */
    protected function invokeObject($callback, $params = array()) {
        if (is_object($callback)) {
            // Call the Closure function with the given arguments.
            call_user_func_array($callback, $params);
            return true;
        }

        // Call the object Controller and its Method.
        $segments = explode('@', $callback);

        $controller = $segments[0];
        $method     = $segments[1] . 'Action';

        // The Method shouldn't start with '_'; also check if the Controller's class exists.
        if (($method[0] !== '_') && class_exists($controller)) {
            // Invoke the Controller's Method with the given arguments.
            return $this->invokeController($controller, $method, $params);
        }

        return false;
    }

    /**
     * Invoke the Controller's Method with its associated parameters.
     *
     * @param  string $className to be instantiated
     * @param  string $method method to be invoked
     * @param  array $params parameters passed to method
     * @return bool
     */
    protected function invokeController($className, $method, $params) {
        // Initialize the Controller.
        /** @var Controller $controller */
        $controller = new $className();

        // The called Method should be defined right in the called Controller.
        if (! in_array(strtolower($method), array_map('strtolower', get_class_methods($controller)))) {
            return false;
        }

        // Execute the Controller's Method with the given arguments.
        call_user_func_array(array($controller, $method), $params);

        // Controller invocation was a success; return true.
        return true;
    }

}

?>