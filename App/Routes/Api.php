<?php
    use \MVC\Core\{Router, Database, View, Http};
    header('Content-Type: application/json');

    $router->prefix('/', function($r) {
        $r->get('/', function() {
            echo View::show('/index.php');
        });

        $r->get('/users', function() {
            echo View::show('/users.php');
        });

        $r->get('/profile', function() {
            echo View::show('/users.php');
        });

        $r->get('/settings', function() {
            echo View::show('/users.php');
        });
    });

    //$router->dump();
?>