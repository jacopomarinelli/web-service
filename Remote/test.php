<?php

/*per testare il codice:
1) apri il software xampp sul tuo dispositivo ed attiva Apache e mysql
2) verifica il numero di porta, ed inseriscilo all'interno dell'url
3) verifica il percorso ed includi la directory in cui si trova test.php
4)inserisci la richiesta finale: nome=CLIENTI
http://localhost/webService/web-service/Remote/Rep/test.php?nome=CLIENTI*/

require_once "Database.php";
require_once "ClientiRepository.php";
require_once "UtenzeRepository.php";
require_once "LettureRepository.php";
require_once "FattureRepository.php";
require_once "ServerAPI.php";

$api = new ServerAPI();

$api->esegui();