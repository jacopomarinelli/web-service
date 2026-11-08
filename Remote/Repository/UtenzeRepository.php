<?php
//DIR e' una costante che restituisce il percorso assoluto, 
//unendolo alla sintassi del codice successivo rimuove problemi di percorsi relativi
require_once __DIR__ . '/../Database.php';

class UtenzeRepository {

    private PDO $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    /** Scrive la query SQL per la tabella UTENZE ricevendo il valore dell'offset e il numero di tuple richieste
     *  e ritorna il risultato */
    public function recuperaUtenze($offset, $tuple): array {
        $sql = "SELECT * FROM UTENZE LIMIT $offset, $tuple;";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}