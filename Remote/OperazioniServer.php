<?php

class OperazioniServer
{
    private ClientiRepository $clienti;
    private UtenzeRepository $utenze;
    private LettureRepository $letture;
    private FattureRepository $fatture;
    private Database $database;

    public function __construct()
    {
        $database = new Database();

        $this->clienti = new ClientiRepository($database);
        $this->utenze = new UtenzeRepository($database);
        $this->letture = new LettureRepository($database);
        $this->fatture = new FattureRepository($database);
    }

    /** Recupera i parametri della chiamata HTTP, 
     * inserendoli nel metodo corrispondente alla tabella richiesta */
    public function esegui(): void{
        {
        header("Content-Type: application/json; charset=UTF-8");
        
        $tabella = $_GET["tabella"] ?? null; //gestione dei valori di default
        $offset = $_GET["offset"] ?? 0; //valore default di offset = 0
        $tuple = $_GET["tuple"] ?? 400; //valore default di tuple prese = 400

        // gestione errore in caso di parametri mancanti
        if ($tabella === null) {
            http_response_code(400);
            echo json_encode([
                "errore" => "Parametro mancante"
            ]);
            exit;
        }

        //chiamata metodo legato alla tabella richiesta
        switch ($tabella) {
            case "CLIENTI":
                $risultato = $this->clienti->recuperaClienti($offset, $tuple);
                break;

            case "UTENZE":
                $risultato = $this->utenze->recuperaUtenze($offset, $tuple);
                break;

            case "LETTURE":
                $risultato = $this->letture->recuperaLetture($offset, $tuple);
                break;

            case "FATTURE":
                $risultato = $this->fatture->recuperaFatture($offset, $tuple);
                break;

            default:
                http_response_code(400);
                echo json_encode([
                    "errore" => "Nome non valido"
                ]);
                exit;
        }

        echo json_encode($risultato);
        }
    }
}