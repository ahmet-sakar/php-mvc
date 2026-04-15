<?php
    namespace MVC\Core;

    class File {
        public static function GetFiles(string $path, $withPath = false): array {
            $path = substr($path, -1) == '/' ? substr($path, 0, -1) : $path;
            $scan = scandir($path);
            $files = [];
            foreach ($scan as $file) {
                if (!in_array(trim($file), ['.', '..']) && substr($file, 0, 1) != '_') {
                    $files[] = [
                        'name' => $file,
                        'type' => self::GetExtension($file),
                        'size' => filesize($path.'/'.$file),
                        'path' => $path.'/'.$file
                    ];
                }
            }

            return $files;
        }

        public static function GetRoutes(bool $withPath = false): array {
            return self::GetFiles(__DIR__ . '/../App/routes/', $withPath);
        }

        public static function Get(string $path): string {
            return file_get_contents($path);
        }

        public static function GetExtension($name) {
            $n = strrpos($name, '.');
            return ($n ? substr($name, $n + 1) : '');
        }

        public static function GetMime(string $ext): string {
            $types = [
                'css'   => 'text/css',
                'js'    => 'text/javascript',
                'conf'  => 'text/plain',
                'log'   => 'text/plain',
                'pl'    => 'text/plain',
                'text'  => 'text/plain',
                'txt'   => 'text/plain',
                'ttf'   => 'font/ttf',
                'otf'   => 'font/otf',
                'woff'  => 'font/woff',
                'avi'   => 'video/x-msvideo',
                'mkv'   => 'video/x-matroska',
                'm4a'   => 'video/mp4',
                'm4v'   => 'video/mp4',
                'mp4'   => 'video/mp4',
                'mp3'   => 'audio/mpeg',
                'weba'  => 'video/weba',
                'mpeg'  => 'video/mpeg',
                'webm'  => 'video/webm',
                'svg'   => 'image/svg+xml',
                'gif'   => 'image/gif',
                'webp'  => 'image/webp',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg'
            ];
        
            return $types[$ext] ?? 'Unknown Extension';
        }
    }
?>