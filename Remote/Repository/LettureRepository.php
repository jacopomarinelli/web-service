<?php

class LettureRepository {

    private PDO $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function recuperaLetture(offset, tuple): array {
        $sql = "SELECT * FROM LETTURE LIMIT offset, tuple;";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}