<?php
    date_default_timezone_set('Europe/Istanbul');
    require 'autoload.php';
    use \MVC\Core\{Router, Database, Http, Route, File, View, Config};
    // $GLOBALS['db'] = new Database();
    $router = new Router();
    require __DIR__ . '/App/Routes/Api.php';
    $router->dispatch();
?>