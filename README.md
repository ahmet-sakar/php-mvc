# Example

## Sample
```php
use \MVC\Core\{Router, View};
$router = new Router();

$router->get('/', function() {
    echo View::show('/index.php');
});

$router->dispatch();
```

## Prefix Use
```php
$router->prefix('/', function($r) {
    $r->get('/?', function() {
        echo 'Home Page';
    });
    
    $r->prefix('/foo', function($r) {
        $r->get('/?', function() {
            echo 'Foo';
        });

        $r->prefix('/bar', function($r) {
            $r->get('/?', function() {
                echo 'Foo Bar';
            });

            $r->get('/baz', function() {
                echo 'Foo Bar Baz';
            });
        });
    });
});
```

## Route Parameter Use
```php
$r->get('/users/:id', function($id) {
    echo "ID: $id";
});

$r->get('/users/:id/:id', function($id) {
    echo "ID: $id[0]-$id[1]";
});
```
