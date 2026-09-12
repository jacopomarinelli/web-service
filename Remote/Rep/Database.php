<?php

class Database
{
    private PDO $connection;

    public function __construct()
    {
        $servername = "localhost";
        $dbname = "nome_del_database";
        $username = "root";
        $password = "";

        $this->connection = new PDO(
            "mysql:host=" . $servername . ";dbname=" . $dbname . ";charset=utf8mb4",
            $username,
            $password
        );

        $this->connection->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}