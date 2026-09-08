<?php

class UtenzeRepository {

    private PDO $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function recupera(): array {

        $sql = "SELECT * FROM UTENZE";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}