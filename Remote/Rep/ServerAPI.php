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

    public function esegui(): void
    {
        header("Content-Type: application/json; charset=UTF-8");

        $nome = $_GET["nome"] ?? null;

        if ($nome === null) {
            http_response_code(400);
            echo json_encode([
                "errore" => "Parametro nome mancante"
            ]);
            exit;
        }

        switch ($nome) {
            case "CLIENTI":
                $risultato = $this->clienti->recupera();
                break;

            case "UTENZE":
                $risultato = $this->utenze->recupera();
                break;

            case "LETTURE":
                $risultato = $this->letture->recupera();
                break;

            case "FATTURE":
                $risultato = $this->fatture->recupera();
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