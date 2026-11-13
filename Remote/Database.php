<?php

class Database {

    private static ?PDO $connection = null;

    //variabile che indica dove lavora il database, inserire "locale" oppure "altervista"
    private static string $env = "altervista";

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            //codice database su altervista
            if (self::$env === "altervista") {
                self::$connection = new PDO(
                    "mysql:host=localhost;dbname=my_aquabear;charset=utf8",
                    "aquabear",
                    ""
                );
            } else { //codice per funzionamento in locale (XAMPP)
                self::$connection = new PDO(
                    "mysql:host=localhost;dbname=my_aquabear;charset=utf8",
                    "root",
                    ""
                );
            }
            //gestione errori di connessione
            self::$connection->setAttribute(    
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            //mette nomi colonne tabelle in minuscolo
            self::$connection->setAttribute(    
                PDO::ATTR_CASE,
                PDO::CASE_LOWER
            );
        }
        return self::$connection;
    }
}