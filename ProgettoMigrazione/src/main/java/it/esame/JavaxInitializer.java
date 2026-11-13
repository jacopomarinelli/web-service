package it.esame;

import javax.servlet.ServletContainerInitializer;
import javax.servlet.ServletContext;
import java.util.Set;

public class JavaxInitializer implements ServletContainerInitializer {
    @Override
    public void onStartup(Set<Class<?>> c, ServletContext ctx) {
        ctx.addServlet("MigrationServlet", JavaxMigrationServlet.class)
           .addMapping("/migrate");
    }
}