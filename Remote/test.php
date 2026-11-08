<?php
/*Pagina di test, con questa pagina si testa il codice, si verifica che tutto funzioni correttamente
seguire le istruzioni riportate qua sotto*/

/*per testare il codice:
1) apri il software xampp sul tuo dispositivo ed attiva Apache e mysql
2) verifica il numero di porta, ed inseriscilo all'interno dell'url
3) verifica il percorso ed includi la directory in cui si trova test.php
4)inserisci la richiesta finale: nome=CLIENTI
http://localhost/webService/web-service/Remote/Rep/test.php?nome=CLIENTI*/

/*http://localhost/webService/web-service/Remote/test.php?tabella=CLIENTI&offset=0&tuple=50*/
/*http://localhost/webService/web-service/Remote/test.php?tabella=CLIENTI&offset=0&tuple=1300*/

require_once "Database.php";
require_once __DIR__ . '/Repository/ClientiRepository.php';
require_once __DIR__ . '/Repository/UtenzeRepository.php';
require_once __DIR__ . '/Repository/LettureRepository.php';
require_once __DIR__ . '/Repository/FattureRepository.php';
require_once "API.php";
require_once "OperazioniServer.php";

$api = new API();

$api->carica();