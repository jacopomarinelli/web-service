package it.esame;

import jakarta.servlet.ServletContainerInitializer;
import jakarta.servlet.ServletContext;
import java.util.Set;

public class JakartaInitializer implements ServletContainerInitializer {
    @Override
    public void onStartup(Set<Class<?>> c, ServletContext ctx) {
        ctx.addServlet("MigrationServlet", JakartaMigrationServlet.class)
           .addMapping("/migrate");
    }
}