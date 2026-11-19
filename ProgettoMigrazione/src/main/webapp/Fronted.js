
const url = "http://localhost:8080/ProgettoMigrazione/servlet" /**da modificare */
const handshake = "JASON-HANDSHAKE"
const update = "UPDATE"

let bottone = false; /**variabile boolean che indica lo stato del bottone, il bottone si può schiacciare solo se è true */

const TOTALI_TABELLE = {
    CLIENTI: 0,
    FATTURE: 0,
    UTENZE: 0,
    LETTURE: 0
};

/**
 * jsonHandshake={
 *     tipo: "JASON-HANDSHAKE"
 *     CLIENTI-TOTALI: 0
 *     FATTURE-TOTALI: 0
 *     UTENZE-TOTALI: 0
 *     LETTURE-TOTALI: 0
 *     CLIENTI-COMPLETI: 0
 *     FATTURE-COMPLETI: 0
 *     UTENZE-COMPLETI: 0
 *     LETTURE-COMPLETI: 0   
 * }
 */



    async function startaServlet(){
        await fetch(url, "GET", update);
    }

    function esecutore(){
        let messaggio = inviaHandshake();
        handler(messaggio);

    }

    async function inviaHandshake(){
        const risposta = await fetch(url, "GET", handshake);
        const messaggio = risposta.json();
        return messaggio;
    }

    function handler(messaggio){
        if(messaggio.tipo!==handshake) {
            /**errore: il sistema non ha riconosciuto l'handshake */
        } else {
            try {
                impostaDati(messaggio);
                return true;
            } catch (error) {
                /**errore: parametri handshake errati, si prega di riprovare */
            }
        }
    }

    function impostaDati(messaggio){
        TOTALI_TABELLE.CLIENTI = messaggio.CLIENTI-TOTALI;
        TOTALI_TABELLE.FATTURE = messaggio.FATTURE-TOTALI;
        TOTALI_TABELLE.UTENZE = messaggio.UTENZE-TOTALI;
        TOTALI_TABELLE.LETTURE = messaggio.LETTURE-TOTALI;
    }

    async function RicezioneDati() {
        const connessione = new EventSource(url);  
        connessione.onmessage = centralUpdater(messaggio);
    }

    function centralUpdater(messaggio){
        let tipo = messaggio.tipo;
        if(tipo==handshake) {

        } else if(tipo==update) {

        } else {
            /**errore: scelta non valida */
        }
    }

    function decisoreStato(messaggio){
        let caso;
        let completi = messaggio.CLIENTI-COMPLETI + messaggio.FATTURE-COMPLETI + messaggio.UTENZE-COMPLETI + messaggio.LETTURE-COMPLETI;
        let totali = messaggio.CLIENTI-TOTALI + messaggio.FATTURE-TOTALI + messaggio.UTENZE-TOTALI + messaggio.LETTURE-TOTALI;
        if(completi == 0) {
            caso = "VUOTO";
        } else if(completi > 0) {
            caso = "IN_ESECUZIONE";
        } else if(completi < 0) {
            caso = "ERRORE";
            /**errore grave nel formato del handshake */
        } else if(completi == totali) {
            caso = "COMPLETATO";
        }
        return caso;
    }

    function jsonHandshakeHandler(messaggio){
        let caso = decisoreStato(messaggio);
        if(caso=="VUOTO") {
            bottone = true;
            startaServlet();
        } else if(caso=="IN_ESECUZIONE") {
            handler();
        } else if(caso == "ERRORE") {
            /**errore grave nel formato handshake */
        } else if(caso == "COMPLETATO") {
            /**migrazione completata */
        }
    }