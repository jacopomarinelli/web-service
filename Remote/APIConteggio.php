<?php

require_once __DIR__ . '/OperazioniServer.php';

/**Classe API che permette lo scambio delle informazioni sul numero di righe con la Servlet Java */
class APIConteggio {

    private OperazioniServer $op;

    public function __construct() {
        $this->op = new OperazioniServer();
    }

    /** Permette trasferimento dei dati dal database su Altervista alla Servlet Java*/
    public function carica(){
        $this->op->contaRighe();
    }

}

$api = new APIConteggio();
$api->carica();