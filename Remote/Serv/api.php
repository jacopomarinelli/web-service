<?php

require_once "../Database.php";

require_once "../ClientiRepository.php";
require_once "../ClientiService.php";

class ClientiAPI {

    private ClientiService $service;

    public function __construct() {

        $database = new Database();

        $repository = new ClientiRepository($database);

        $this->service = new ClientiService($repository);
    }

    public function esegui(): void {

        header("Content-Type: application/json");

        $clienti = $this->service->ottieniClienti();

        echo $clienti;
    }
}