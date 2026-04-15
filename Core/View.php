<?php
    namespace MVC\Core;

    class View {
        public static array $fileTypes = [
			'' => 'text/html',
			'css' => 'text/css',
			'js' => 'text/javascript',
			'png' => 'image/png',
			'jpg' => 'image/jpg',
			'jpeg' => 'image/jpg'
		];

        public static function show($path): string {
            self::contentType();
			return file_get_contents(__DIR__ . "/../Public/Views$path");
        }

        public static function url(): string { return str_replace(Config::get('PATH'), '', $_SERVER['REQUEST_URI']); }
		public static function method(): string { return strtoupper($_SERVER['REQUEST_METHOD']); }
		public static function extension($url = ''): string { return preg_match('/\.([a-zA-Z0-9]+)$/', $url != '' ? $url : self::url(), $match) ? $match[1] : ''; }

        public static function contentType($url = ''): void {
			$ext = self::extension($url != '' ? $url : '');
			$type = self::$fileTypes[$ext];
			header("Content-Type: $type");
		}
    }
?>
