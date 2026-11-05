<?php

class UtenzeRepository {

    private PDO $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function recuperaUtenze(offset, tuple): array {
        $sql = "SELECT * FROM UTENZE LIMIT offset, tuple;";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}