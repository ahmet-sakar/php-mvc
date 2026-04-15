<?php
    namespace MVC\Core;

    class Redirect {
        /**
         * @param string $toUrl
         * @param int $status
         */
        public static function to(string $toUrl, int $status = 301) {
            header('Location:' . \MVC\Core\Config::get('PATH') . $toUrl, true, $status);
            exit;
        }
    }
?>