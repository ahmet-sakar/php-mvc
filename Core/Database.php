<?php
    namespace MVC\Core;

    class Database {
        protected \PDO $db;
        protected array $where = [];
        protected array $like = [];
        protected array $join = [];
        private string $sql = '';
        private string $table;
        private string $type = 'SELECT';
        private const PREFIX = '';

        public function __construct() {
            $conn = sprintf('mysql:host=%s;dbname=%s;charset=%s', Config::get('DB_HOST'), Config::get('DB_NAME'), Config::get('DB_CHARSET'));
            $this->db = new \PDO($conn, Config::get('DB_USER'), Config::get('DB_PASS'));
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        public function select($seperator = []) {
            $indicator = '*';
            if (!empty($seperator)) $indicator = implode(', ', $seperator);
            $this->sql = "SELECT $indicator FROM ";
            $this->type = 'SELECT';
            return $this;
        }

        public function from($table) {
            $this->sql .= $table;
            return $this;
        }

        public function insert($table) {
            $this->sql = "INSERT INTO $table";
            $this->type = 'INSERT';
            return $this;
        }

        public function into($entries, $value = '') {
            if (is_string($entries) && !empty($value)) $this->sql .= " ($entries) VALUES('$value')";
            else if (is_array($entries)) $this->sql .= '('.implode(',', array_keys($entries)).') VALUES(\''.implode('\',\'', array_values($entries)).'\')';
            return $this;
        }

        public function update($table) {
            $this->sql = "UPDATE $table SET ";
            $this->type = 'UPDATE';
            return $this;
        }

        public function set($entries, $value = '') {
            $update = [];
            if (is_string($entries) && !empty($value)) $this->sql .= "$entries = '$value'";
            else if (is_array($entries)) foreach ($entries as $key => $val) $update[] = "$key='$val'";
            $this->sql .= implode(', ', $update);
            return $this;
        }

        public function delete() {
            $this->sql = 'DELETE FROM ';
            $this->type = 'DELETE';
            return $this;
        }

        public function where($column, $value, $operation = '=') {
            $this->where[] = "$column $operation '$value'";
            return $this;
        }

        public function like($column, $value, $operator = '') {
            $this->like[] = "$column LIKE '%$value%' ".strtoupper($operator);
            return $this;
        }

        public function join($table, $column, $value, $operator = '=') {
            $this->join[] = "JOIN $table ON $column $operator $value";
            return $this;
        }

        public function escape(string $str): string {
            $words = [ '\'', '"', '\\', '--', '/*', '*/', '%', '_', 'DROP TABLE', 'TRUNCATE TABLE', 'NULL' ];
            return str_replace($words, '', $str);
        }

        public function hasRows() {
            $where = '';
            if (count($this->where) > 0) $where = ' WHERE ' . implode(' && ', $this->where);
            $query = $this->db->prepare($this->sql.$where);
            $query->execute();
            return $query->rowCount() > 0;
        }

        public function dump() {
            if (count($this->join) > 0) $this->sql .= ' ' . implode(' ', $this->join);
            if (count($this->where) > 0) $this->sql .= ' WHERE ' . implode(' && ', $this->where);
            else if (count($this->like) > 0) $this->sql .= ' WHERE ' . implode(' ', $this->like);

            $this->where = [];
            $this->like = [];
            $this->join = [];

            echo $this->sql;
        }

        public function execute() {
            if (count($this->join) > 0) $this->sql .= ' ' . implode(' ', $this->join);
            if (count($this->where) > 0) $this->sql .= ' WHERE ' . implode(' && ', $this->where);
            else if (count($this->like) > 0) $this->sql .= ' WHERE ' . implode(' ', $this->like);

            $query = $this->db->prepare(trim($this->sql));
            $this->where = [];
            $this->like = [];
            $this->join = [];

            if ($this->type == 'SELECT') {
                $query->execute();
                return $query->fetchAll(\PDO::FETCH_ASSOC);
            } else return $query->execute();
        }
    }
?>
