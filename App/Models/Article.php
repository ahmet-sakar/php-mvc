<?php
    namespace MVC\App\Models;
    use MVC\Core\Database;

    class Article {
        public function getAll() {
            return Database::table('articles')->get();
        }

        public function find($url) {
            return Database::table('articles')->where('article_url', $url)->first();
        }
    }
?>