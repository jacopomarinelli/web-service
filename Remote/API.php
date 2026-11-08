<?php

require_once __DIR__ . '/OperazioniServer.php';

class API {

    private OperazioniServer $op;

    public function __construct() {
        $this->op = new OperazioniServer();
    }

    public function carica(){
        $op->esegui();
    }

}