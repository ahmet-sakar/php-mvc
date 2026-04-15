<?php
    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    function route(string $name, array $params = []) {
        return \MVC\Core\Route::url($name, $params);
    }

    /**
     * @param string $name
     * @param array $data
     * @return string
     */
    function view(string $name, array $data = []): string {
        return \MVC\Core\View::show($name, $data);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    function url(string $name, array $params = []): string {
        return \MVC\Core\Route::url($name, $params);
    }

    /**
     * @param string $name
     * @return mixed
     */
    function model(string $name) {
        $name = '\MVC\App\Models\\' . $name;
        return new $name();
    }
?>