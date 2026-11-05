<?php

class FattureRepository {

    private PDO $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function recuperaFatture(offset, tuple): array {
        $sql = "SELECT * FROM FATTURE LIMIT offset, tuple;";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}