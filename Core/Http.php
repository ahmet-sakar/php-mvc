<?php
    namespace MVC\Core;
    class Http {
        private static string $secret_key = 'ataturk';

        public static function body(string $key = '') {
            if (!empty($_POST)) return ($key == '') ? $_POST : $_POST[$key];
	        else {
	            $json = json_decode(file_get_contents('php://input'), true);
	            return $key == '' ? $json : $json[$key];
	        }
        }

        public static function header(string $key) {
            $hdr = getallheaders();
            $key = strtolower($key);
            return isset($hdr[$key]) ? $hdr[$key] : '';
        }

        public static function headers() { return getallheaders(); }

        public static function createToken(array $payload) {
            $header = base64_encode(json_encode([ 'typ' => 'JWT', 'alg' => 'HS256' ]));
    	    $payload = base64_encode(json_encode($payload));
    	    $signature = base64_encode(hash_hmac('sha256', "$header.$payload", self::$secret_key, true));
    	    return "$header.$payload.$signature";
        }

        public static function decryptToken($token) {
            [ $header, $payload, $signature ] = explode('.', $token);
    		$std = new \stdClass();
    		$std->header = json_decode(base64_decode($header));
    		$std->payload = json_decode(base64_decode($payload));
    		$std->signature = $signature;
    		return $std;
        }

        public static function isValidToken(string $token) {
            if (!empty($token) && strstr($token, '.')) {
                $params = explode('.', $token);
                if (count($params) == 3) {
                    [ $header, $payload, $signature ] = $params;
            	    $d_header = json_decode(base64_decode($header));
            	    $d_payload = json_decode(base64_decode($payload));
            	    $e_signature = base64_encode(hash_hmac('sha256', "$header.$payload", self::$secret_key, true));
            	    return ($e_signature === $signature ? true : false);
                }
            }

            return false;
        }

        public static function UnixToDate(string $unix, string $format = 'd.m.Y H:i:s'): string {
            return date($format, $unix);
        }

        public static function code(int $code) { http_response_code($code); }
        public static function message(int $code, $status = 0) {
            self::code($code);
            echo json_encode([
                'status' => $status,
                'messsage' => self::messages($code)
            ]);
        }

        private static function messages($code) {
            switch ($code) {
                case 200: return 'OK';
                case 301: return 'Moved Permantly';
                case 400: return 'Bad Request';
                case 401: return 'Unauthorized';
                case 403: return 'Forbidden';
                case 404: return 'Not Found';
                case 500: return 'Internal Server Error';
                case 502: return 'Bad Gateway';
                case 503: return 'Service Unavailable';
                default: return 'Unknown Status';
            }
        }
    }
?>
