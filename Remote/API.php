<?php

require_once __DIR__ . '/OperazioniServer.php';

/**Classe API che permette l'interfaccia tra il codice PHP 
 * di recupero dati SQL con la Servlet Java */
class API {

    private OperazioniServer $op;

    public function __construct() {
        $this->op = new OperazioniServer();
    }

    /** Permette trasferimento dei dati dal database su Altervista alla Servlet Java*/
    public function carica(){
        $this->op->esegui();
    }

}

$api = new API();
$api->carica();

/* https://aquabear.altervista.org/Remote/API.php?tabella=FATTURE&offset=0&tuple=4000 */