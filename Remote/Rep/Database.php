<?php

class Database {

    private PDO $connection;

    public function __construct() {

        $this->connection = new PDO(
            "mysql:host=localhost;dbname=my_aquabear;charset=utf8",
            "aquabear",
            ""
        );

        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}