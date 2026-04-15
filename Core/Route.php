<?php
    namespace MVC\Core;

    class Route {
        public static array $patterns = [
            ':id[0-9]?' => '([0-9]+)',
            ':url[0-9]?' => '([a-zA-Z0-9-_]+)',
            ':username[0-9]?' => '([a-zA-Z0-9-_]+)'
        ];

        public static string $emailPattern = '/([a-zA-Z0-9._+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/';
        public static bool $hasRoute = true;
        public static array $routes = [
            'get' => [],
            'post' => []
        ];
        
        public static string $prefix = '';
        public static array $prefixes = [];
        public static string $path = '';
        public static array $paths = [];
        public static array $allowedMethods = [ 'get', 'post' ];

        /**
         * @param $path
         * @param $callback
         * @return Route
         */
        public static function get(string $path, $callback): Route {
            self::$path = $path;
            self::$routes['get'][self::$prefix . $path] = [ 'callback' => $callback ];
            return new self();
        }

        /**
         * @param string $path
         * @param $callback
         */
        public static function post(string $path, $callback): void {
            self::$routes['post'][$path] = [ 'callback' => $callback ];
        }

        public function request(callable $callback): void {
            $path = self::$prefix.self::$path;
            foreach (self::$patterns as $key => $pattern) $path = preg_replace('#' . $key . '#', $pattern, $path);
            $pattern = '#^' . $path . '$#';
            if (preg_match($pattern, self::getUrl(), $params)) self::$routes[self::getMethod()][self::$prefix.self::$path]['request'] = $callback;
        }

        public static function dispatch() {
            $url = self::getUrl();
            $method = self::getMethod();
            
            if (in_array($method, self::$allowedMethods)) {
                foreach (self::$routes[$method] as $path => $props) {
                    foreach (self::$patterns as $key => $pattern) $path = preg_replace('#' . $key . '#', $pattern, $path);
                    $pattern = '#^' . $path . '$#';
                    self::$paths[] = $path;
                    if (preg_match($pattern, $url, $params)) {
                        self::$hasRoute = false;
                        array_shift($params);
                        if (isset($props['redirect'])) Redirect::to($props['redirect'], $props['status']);
                        else {
                            $callback = $props['callback'];
                            if (is_callable($callback)) {
                                echo call_user_func_array($callback, [ $params ]); // $callback $path
                                $_path = self::$prefix.self::$path;
                                if (isset(self::$routes[$method][$_path]['request'])) echo call_user_func_array(self::$routes[$method][$_path]['request'], [ getallheaders() ]);
                            } elseif (is_string($callback)) {
                                [$controllerName, $methodName] = explode('@', $callback);
                                $controllerName = '\MVC\App\Controllers\\' . $controllerName;
                                $controller = new $controllerName();
                                echo call_user_func_array([$controller, $methodName], $params);
                            }
                        }
                    }
                }
                    
                if (self::$hasRoute) {
                    header('Content-Type: application/json');
                    die(json_encode([ 'error' => 'API Not Found' ]));
                }
            } else {
                header('Content-Type: application/json');
                die(json_encode([ 
                    'error' => 'This method not allowed',
                    'method' => strtoupper($method)
                ]));
            }
            
            /*header('Content-Type: application/json');
            die(json_encode(self::$routes));*/
        }
        
        /**
         * @param $prefix
         * @return Route
         */
        public static function prefix($prefix): Route {
            /*if (substr(self::$prefix, 0, 1) == '/') self::$prefix .= substr($prefix, 1);
            else*/ self::$prefix .= $prefix;
            self::$prefixes[] = $prefix;
            //echo substr(self::$prefix, 0, 1).'<br>';
            return new self();
        }
        
        /**
         * @param \Closure $closure
         */
        public static function group(\Closure $closure): void {
            $closure();
            //self::$prefix = '';
        }

        /**
         * @return string
         */
        public static function getMethod(): string {
            return strtolower($_SERVER['REQUEST_METHOD']);
        }

        /**
         * @return string
         */
        public static function getUrl(): string {
            return str_replace(Config::get('PATH'), null, $_SERVER['REQUEST_URI']);
        }

        public function name(string $name): void {
            self::$routes['get'][array_key_last(self::$routes['get'])]['name'] = $name;
        }

        /**
         * @param string $name
         * @param array $params
         * @return string
         */
        public static function url(string $name, array $params = []): string {
            $route = array_key_first(array_filter(self::$routes['get'], function ($route) use ($name) {
                return isset($route['name']) && $route['name'] === $name;
            }));

            return Config::get('PATH') . str_replace(array_map(fn($key) => ':' . $key, array_keys($params)), array_values($params), $route);
        }

        public function where($key, $pattern) {
            self::$patterns[':' . $key] = '(' . $pattern . ')';
        }

        public static function redirect($from, $to, $status = 301) {
            self::$routes['get'][$from] = [
                'redirect' => $to,
                'status' => $status
            ];
        }
    }
?>