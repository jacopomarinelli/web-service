migrazioneClienti = false;
migrazioneFatture = false;
migrazioneUtenze = false;
migrazioneLetture = false;

function controlloPulsanti(){
    /*
    if (migrazione clienti non completata)
      pulsante utenza non disponibile
    if (migrazione utenze e fatture non completate)
      pulsante letture non disponibile
    */
}

function richiestaServlet(tabella){
   /*
   invio richiesta http a servlet
   */
}

function statoOperazione(flag){
   /*
   chiede stato dell'operazione
   if (operazione finita)
      flag==true
   */
}


function richiestaClienti(clienti){
   richiestaServlet(clienti);
}

function richiestaFatture(fatture){
   richiestaServlet(fatture);
}

function richiestaUtenze(utenze){
   richiestaServlet(utenze);
}

function richiestaLetture(letture){
   richiestaServlet(letture);
}


function statoClienti(migrazioneClienti){
   statoOperazione(migrazioneClienti);
}

function statoFatture(migrazioneFatture){
   statoOperazione(migrazioneFatture);
}

function statoUtenze(migrazioneUtenze){
   statoOperazione(migrazioneUtenze);
}

function statoLetture(migrazioneLetture){
   statoOperazione(migrazioneLetture);
}