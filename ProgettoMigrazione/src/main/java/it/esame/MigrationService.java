package it.esame;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import okhttp3.HttpUrl;
import okhttp3.MediaType;
import okhttp3.OkHttpClient;
import okhttp3.Request;
import okhttp3.RequestBody;
import okhttp3.Response;

import java.io.IOException;
import java.io.InputStream;
import java.util.Arrays;
import java.util.Collections;
import java.util.HashMap;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Map;
import java.util.Properties;
import java.util.Set;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.TimeUnit;

public class MigrationService {

    private static final List<String> ORDINE_TABELLE = Arrays.asList("CLIENTI", "FATTURE", "UTENZE", "LETTURE");

    private static final Map<String, String> DJANGO_ENDPOINTS = new HashMap<>();
    static {
        DJANGO_ENDPOINTS.put("CLIENTI", "/clienti/");
        DJANGO_ENDPOINTS.put("FATTURE", "/fatture/");
        DJANGO_ENDPOINTS.put("UTENZE", "/utenze/");
        DJANGO_ENDPOINTS.put("LETTURE", "/letture/");
    }

    private final String phpUrl;
    private final String djangoBaseUrl;
    private final int batchSize;
    private final String djangoToken;

    private final Gson gson = new Gson();
    private final OkHttpClient client = new OkHttpClient.Builder()
            .connectTimeout(15, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .build();

    private volatile String stato = "IN_ATTESA";
    private volatile String tabellaCorrente = "";
    private volatile int tupleMigrate = 0;
    private volatile String messaggioErrore = "";
    private final Set<String> tabelleCompletate = Collections.synchronizedSet(new LinkedHashSet<>());

    private final ExecutorService executor = Executors.newSingleThreadExecutor(r -> {
        Thread t = new Thread(r);
        t.setDaemon(true);
        return t;
    });

    public MigrationService() {
        Properties props = new Properties();
        try (InputStream is = getClass().getClassLoader().getResourceAsStream("config.properties")) {
            if (is != null) {
                props.load(is);
            }
        } catch (IOException ignored) {
        }
        this.phpUrl = props.getProperty("php.url", "https://aquabear.altervista.org/Remote/API.php");
        this.djangoBaseUrl = props.getProperty("django.base.url", "http://localhost:8000/api");
        this.batchSize = Integer.parseInt(props.getProperty("batch.size", "400"));
        this.djangoToken = props.getProperty("django.token", "").trim();

        if (batchSize <= 0) {
            throw new IllegalArgumentException("Batch size non valido");
        }
    }

    public synchronized int avviaMigrazione(String rawTabella) {
        if ("IN_CORSO".equals(stato)) {
            return 409;
        }
        if (rawTabella == null || rawTabella.trim().isEmpty()) {
            this.stato = "ERRORE";
            this.messaggioErrore = "Nome tabella mancante.";
            return 400;
        }

        String tabella = rawTabella.trim().toUpperCase();
        int indice = ORDINE_TABELLE.indexOf(tabella);
        if (indice == -1) {
            this.stato = "ERRORE";
            this.messaggioErrore = "Tabella non valida: " + tabella;
            return 400;
        }

        if (indice > 0) {
            String precedente = ORDINE_TABELLE.get(indice - 1);
            if (!tabelleCompletate.contains(precedente)) {
                this.stato = "ERRORE";
                this.messaggioErrore = "Devi prima migrare la tabella " + precedente + "!";
                return 409;
            }
        }

        this.stato = "IN_CORSO";
        this.tabellaCorrente = tabella;
        this.tupleMigrate = 0;
        this.messaggioErrore = "";

        executor.submit(() -> eseguiCicloBatch(tabella));
        return 202;
    }

    private void eseguiCicloBatch(String tabella) {
        int offset = 0;
        try {
            while (true) {
                String phpJson = fetchFromPhp(tabella, offset, batchSize);
                int ricevute = countJsonObjects(phpJson);

                if (ricevute == 0) {
                    break;
                }

                boolean ok = sendToDjango(tabella, phpJson);
                if (!ok) {
                    this.stato = "ERRORE";
                    this.messaggioErrore = "Django ha rifiutato i dati di " + tabella + " all'offset " + offset;
                    return;
                }

                offset += ricevute;
                this.tupleMigrate = offset;

                if (ricevute < batchSize) {
                    break;
                }
            }

            this.tabelleCompletate.add(tabella);
            this.stato = "COMPLETATO";

        } catch (Exception e) {
            this.stato = "ERRORE";
            this.messaggioErrore = e.getMessage() != null ? e.getMessage() : "Errore sconosciuto";
        }
    }

    public String getStatoJson() {
        JsonObject obj = new JsonObject();
        obj.addProperty("stato", stato);
        obj.addProperty("tabellaCorrente", tabellaCorrente);
        obj.addProperty("tupleMigrate", tupleMigrate);
        obj.addProperty("errore", messaggioErrore);

        JsonArray arr = new JsonArray();
        synchronized (tabelleCompletate) {
            for (String t : tabelleCompletate) {
                arr.add(t);
            }
        }
        obj.add("tabelleCompletate", arr);
        return gson.toJson(obj);
    }

    private String fetchFromPhp(String tabella, int offset, int tuple) throws IOException {
        HttpUrl parsed = HttpUrl.parse(phpUrl);
        if (parsed == null) {
            throw new IOException("URL PHP non valido: " + phpUrl);
        }

        HttpUrl url = parsed.newBuilder()
                .addQueryParameter("tabella", tabella)
                .addQueryParameter("offset", String.valueOf(offset))
                .addQueryParameter("tuple", String.valueOf(tuple))
                .build();

        Request request = new Request.Builder()
                .url(url)
                .header("User-Agent", "Mozilla/5.0")
                .get()
                .build();

        try (Response response = client.newCall(request).execute()) {
            if (!response.isSuccessful() || response.body() == null) {
                throw new IOException("Altervista PHP ha risposto con errore HTTP: " + response.code());
            }
            return response.body().string();
        }
    }

    private boolean sendToDjango(String tabella, String jsonPayload) throws IOException {
        String url = djangoBaseUrl + DJANGO_ENDPOINTS.get(tabella);
        RequestBody body = RequestBody.create(jsonPayload, MediaType.get("application/json; charset=utf-8"));

        Request.Builder reqBuilder = new Request.Builder()
                .url(url)
                .post(body);

        if (!djangoToken.isEmpty()) {
            reqBuilder.header("Authorization", "Token " + djangoToken);
        }

        try (Response response = client.newCall(reqBuilder.build()).execute()) {
            return response.isSuccessful();
        }
    }

    private int countJsonObjects(String json) throws IOException {
        JsonElement element = JsonParser.parseString(json);
        if (!element.isJsonArray()) {
            throw new IOException("PHP non ha restituito una lista JSON valida.");
        }
        return element.getAsJsonArray().size();
    }
}