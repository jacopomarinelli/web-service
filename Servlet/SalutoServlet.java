import jakarta.servlet.*;
import jakarta.servlet.http.*;
import jakarta.servlet.annotation.*;
import java.io.IOException;

@WebServlet("/saluto") 
//@WebServlet	Indica il percorso associato alla Servlet, 
// "/saluto"	URL attraverso cui raggiungere la Servlet
public class SalutoServlet extends HttpServlet { //HttpServlet	Classe da cui eredita la Servlet, 

    @Override
    protected void doGet( //doGet()	Metodo che gestisce le richieste HTTP GET
        HttpServletRequest request, //request	Contiene le informazioni della richiesta
        HttpServletResponse response //response	Permette di costruire la risposta
    ) throws ServletException, IOException {

        response.setContentType("text/plain");

        response.getWriter().println("Ciao, mondo!"); //getWriter()	Permette di scrivere il contenuto della risposta
    }
}