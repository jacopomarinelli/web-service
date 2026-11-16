package it.esame;

import jakarta.servlet.ServletContainerInitializer;
import jakarta.servlet.ServletContext;
import java.util.Set;

// registra automaticamente una Servlet all'interno del server Jakarta EE
public class JakartaInitializer implements ServletContainerInitializer {
    @Override
    public void onStartup(Set<Class<?>> c, ServletContext ctx) {
        // Registra una Servlet chiamata MigrationServlet.
        ctx.addServlet("MigrationServlet", JakartaMigrationServlet.class)
        // Quando arriva una richiesta HTTP all'indirizzo /migrate, deve eseguire quella Servlet.
           .addMapping("/migrate");
    }
}