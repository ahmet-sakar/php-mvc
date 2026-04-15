<?php
	namespace MVC\Core;

	class Router {
		public array $patterns = [
            ':id[0-9]?' => '([0-9]+)',
            ':url[0-9]?' => '([a-zA-Z0-9-_]+)',
            ':username[0-9]?' => '([a-zA-Z0-9-_]+)',
            ':email[0-9]?' => '([a-zA-Z0-9._+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$',
			':search[0-9]?' => '([^@_\/${}\[\]]+)'
        ];

        public bool $hasRoute = true;
        public array $routes = [];
        public string $prefix = '';
        public string $path = '';
        public array $paths = [];
        public array $allowedMethods = [ 'GET', 'POST', 'PUT', 'DELETE' ];

		public function __construct(?callable $fn = null) {
			if ($fn) {
				$self = new Router();
				call_user_func_array($fn, [ $self ]);
				$this->routes = $self->routes;
			}
		}

		public function url(): string { return str_replace(Config::get('PATH'), '', $_SERVER['REQUEST_URI']); }
		public function method(): string { return strtoupper($_SERVER['REQUEST_METHOD']); }
		public function extension($url = ''): string { return preg_match('/\.([a-zA-Z0-9]+)$/', $url != '' ? $url : $this->url(), $match) ? $match[1] : ''; }

		public function types($ext = '') {
			switch ($ext) {
				case 'css': return 'text/css';
				case 'js': return 'text/javascript';
				case 'png': return 'image/png';
				case 'jpg': return 'image/jpg';
				case 'jpeg': return 'image/jpg';
				default: return 'text/html';
			}
		}

		public function contentType($url = ''): void {
			$ext = $this->extension($url != '' ? $url : '');
			$type = $this->types($ext);
			header("Content-Type: $type");
		}

		public function prefix(string $prefix, callable $fn) {
	        $r = new Router();
	        call_user_func_array($fn, [ $r ]);
	        foreach ($r->routes as $method => $routes) {
	            foreach ($routes as $path => $route) {
	                $newPath = rtrim($prefix, '/') . '/' . ltrim($path, '/');
	                $this->addRoute((substr($newPath, 0, 1) != '/' ? '/' : '').$newPath, $method, $route['callback']);
	            }
	        }
	    }

		public function get(string $path, callable $fn)    { $this->addRoute($path, 'GET', $fn);    }
		public function post(string $path, callable $fn)   { $this->addRoute($path, 'POST', $fn);   }
		public function put(string $path, callable $fn)    { $this->addRoute($path, 'PUT', $fn);    }
		public function delete(string $path, callable $fn) { $this->addRoute($path, 'DELETE', $fn); }

		private function addRoute($path, $method, $fn) {
			$this->routes[$method][$path] = [
				'method' => $method,
				'callback' => $fn
			];
		}

		public function dispatch() {
            $url = $this->url();
            $method = $this->method();
			$ext = $this->extension();

			if ($ext != '') {
				$this->contentType();
				echo file_get_contents(__DIR__ . '/../Public'.$url);
				return;
			}

            if (in_array($method, $this->allowedMethods)) {
                foreach ($this->routes[$method] as $path => $props) {
                    foreach ($this->patterns as $key => $pattern) $path = preg_replace('#' . $key . '#', $pattern, $path);
                    $pattern = '#^' . $path . '$#';
                    $this->paths[] = $path;
                    if (preg_match($pattern, $url, $params)) {
                        $this->hasRoute = false;
                        array_shift($params);
                        if (isset($props['redirect'])) Redirect::to($props['redirect'], $props['status']);
                        else {
                            $callback = $props['callback'];
                            if (is_callable($callback)) echo call_user_func_array($callback, [ $params ]);
                            else if (is_string($callback)) {
                                [$controller, $methodName] = explode('@', $callback);
                                $controller = '\MVC\App\Controllers\\' . $controller;
                                echo call_user_func_array([ new $controller(), $methodName ], $params);
                            }
                        }
                    }
                }

                if ($this->hasRoute) {
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

		public function dump() {
			echo '<pre>';
			print_r($this->routes);
			echo '</pre>';
		}
	}
?>
