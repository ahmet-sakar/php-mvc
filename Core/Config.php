<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    namespace MVC\Core;

    class Config {
        private static $cfg = [
            'PATH' => '',
            'DB' => [
                'HOST' => 'dbhost',
                'USER' => 'dbuser',
                'PASS' => 'dbpass',
                'NAME' => 'dbname',
                'CHARSET' => 'utf8mb4'
            ]
        ];
        
        public static function get(string $config) {
            if (substr($config, 0, 3) == 'DB_') return self::$cfg['DB'][substr($config, 3, strlen($config))];
            else return self::$cfg[$config];
        }

        public static function getUrl(): string {
            return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
        }
    }
?>