<?php

class ServerAPI
{
    private ClientiRepository $clienti;
    private UtenzeRepository $utenze;
    private LettureRepository $letture;
    private FattureRepository $fatture;

    public function __construct()
    {
        $database = new Database();

        $this->clienti = new ClientiRepository($database);
        $this->utenze = new UtenzeRepository($database);
        $this->letture = new LettureRepository($database);
        $this->fatture = new FattureRepository($database);
    }

    public function esegui(): void{
        {
        header("Content-Type: application/json; charset=UTF-8");

        $tabella = $_GET["tabella"] ?? null;
        $offset = $_GET["offset"] ?? null;
        $tuple = $_GET["tuple"] ?? null;

        if ($tabella === null) {
            http_response_code(400);
            echo json_encode([
                "errore" => "Parametro nome mancante"
            ]);
            exit;
        }

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