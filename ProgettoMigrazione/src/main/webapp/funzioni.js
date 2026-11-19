// Ordine imposto dai vincoli di chiave esterna in Django e MigrationService.java
const ORDINE_ESECUZIONE = ["CLIENTI", "FATTURE", "UTENZE", "LETTURE"];

// Numero di righe di ogni tabella al momento della consegna (serve solo per la percentuale)
const TOTALI_TABELLE = {
    CLIENTI: 1300,
    FATTURE: 16246,
    UTENZE: 3883,
    LETTURE: 19504
};

let migrazioneAttiva = false;

function aggiornaBarra(tabella, percentuale, classeStato, testoExtra = "") {
    const barra = document.getElementById("bar-" + tabella);
    const label = document.getElementById("pct-" + tabella);
    if (!barra || !label) return;

    barra.style.width = percentuale + "%";
    barra.classList.remove("in-corso", "completata");
    if (classeStato) {
        barra.classList.add(classeStato);
    }
    label.textContent = testoExtra ? `${percentuale}% (${testoExtra})` : `${percentuale}%`;
}

function resetGrafica() {
    const boxErrore = document.getElementById("box-errore");
    boxErrore.hidden = true;
    boxErrore.textContent = "";
}

function mostraErrore(messaggio) {
    const boxErrore = document.getElementById("box-errore");
    boxErrore.textContent = "Errore: " + messaggio;
    boxErrore.hidden = false;
    document.getElementById("stato-generale").textContent = "Trasferimento interrotto.";
}

function attesa(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

// Blocca o sblocca il pulsante e ricorda se c'è una migrazione attiva
function impostaOccupato(occupato) {
    migrazioneAttiva = occupato;
    document.getElementById("btn-trasferisci").disabled = occupato;
}

// Unico punto in cui si parla con la servlet: trasforma i guasti in messaggi comprensibili
async function chiamaServlet(url, opzioni) {
    let risposta;
    try {
        risposta = await fetch(url, opzioni);
    } catch (e) {
        throw new Error("Servlet non raggiungibile: controlla che Tomcat sia acceso.");
    }
    let dati;
    try {
        dati = await risposta.json();
    } catch (e) {
        throw new Error(`Risposta non valida dalla servlet (HTTP ${risposta.status}).`);
    }
    return { ok: risposta.ok, status: risposta.status, dati };
}

// Controlla ogni 0,7 secondi a che punto è la tabella e aggiorna la barra
async function seguiTabella(tabella) {
    while (true) {
        await attesa(700);
        const { dati } = await chiamaServlet("migrate");

        if (dati.stato === "ERRORE") {
            throw new Error(dati.errore || `Errore durante il trasferimento di ${tabella}`);
        }
        if (dati.stato === "IN_ATTESA" || dati.tabellaCorrente !== tabella) {
            throw new Error(`La servlet non sta più lavorando su ${tabella} (forse è stata riavviata).`);
        }
        if (dati.stato === "COMPLETATO") {
            aggiornaBarra(tabella, 100, "completata");
            return;
        }
        // altrimenti è ancora IN_CORSO: aggiorno la barra
        const totale = TOTALI_TABELLE[tabella];
        if (totale > 0) {
            const pct = Math.min(99, Math.round((dati.tupleMigrate / totale) * 100));
            aggiornaBarra(tabella, pct, "in-corso");
        } else {
            aggiornaBarra(tabella, 50, "in-corso", `${dati.tupleMigrate} tuple`);
        }
    }
}

// Chiede alla servlet di far partire una tabella, poi la segue fino alla fine
async function migraSingolaTabella(tabella) {
    document.getElementById("stato-generale").textContent = `Trasferimento di ${tabella} in corso...`;
    const { ok, status, dati } = await chiamaServlet(
        "migrate?tabella=" + encodeURIComponent(tabella), { method: "POST" });
    if (!ok) {
        throw new Error(dati.errore || `Impossibile avviare ${tabella} (HTTP ${status})`);
    }
    await seguiTabella(tabella);
}

// Esegue le tabelle in ordine; se una è già in corso (pagina ricaricata) la segue prima
async function eseguiSequenza(tabelle, giaInCorso = null) {
    impostaOccupato(true);
    try {
        if (giaInCorso) {
            document.getElementById("stato-generale").textContent = `Trasferimento di ${giaInCorso} in corso...`;
            await seguiTabella(giaInCorso);
        }
        for (const tabella of tabelle) {
            await migraSingolaTabella(tabella);
        }
        document.getElementById("stato-generale").textContent = "Trasferimento completato con successo.";
    } catch (err) {
        mostraErrore(err.message);
    } finally {
        impostaOccupato(false);
    }
}

// Si attiva al clic su "Trasferisci": salta le tabelle già migrate e lancia le altre
async function avviaMigrazioneCompleta() {
    if (migrazioneAttiva) return;
    impostaOccupato(true);   // subito, così un secondo clic veloce non parte due volte
    resetGrafica();
    try {
        const { dati } = await chiamaServlet("migrate");
        const gia = dati.tabelleCompletate || [];
        const daFare = ORDINE_ESECUZIONE.filter((t) => !gia.includes(t));
        if (daFare.length === 0) {
            document.getElementById("stato-generale").textContent = "Tutte le tabelle risultano già migrate.";
            impostaOccupato(false);
            return;
        }
        await eseguiSequenza(daFare);
    } catch (err) {
        mostraErrore(err.message);
        impostaOccupato(false);
    }
}

// All'apertura della pagina: collega il pulsante e si riallinea con la servlet
window.addEventListener("DOMContentLoaded", async () => {
    document.getElementById("btn-trasferisci")
        .addEventListener("click", avviaMigrazioneCompleta);

    try {
        const { dati } = await chiamaServlet("migrate");
        const completate = dati.tabelleCompletate || [];
        completate.forEach((tab) => aggiornaBarra(tab, 100, "completata"));

        if (dati.stato === "IN_CORSO") {
            // migrazione già partita prima del ricaricamento: mi riaggancio
            const i = ORDINE_ESECUZIONE.indexOf(dati.tabellaCorrente);
            eseguiSequenza(ORDINE_ESECUZIONE.slice(i + 1), dati.tabellaCorrente);
        } else if (completate.length === ORDINE_ESECUZIONE.length) {
            document.getElementById("stato-generale").textContent = "Tutte le tabelle risultano già migrate.";
        }
    } catch (_) {
        // servlet non ancora raggiungibile: la pagina resta in attesa del primo clic
    }
});