<?php

class Database {

    private static ?PDO $connection = null;

    // CAMBIA QUI: "locale" oppure "altervista"
    private static string $env = "locale";

    public static function getConnection(): PDO {

        if (self::$connection === null) {

            if (self::$env === "altervista") {

                self::$connection = new PDO(
                    "mysql:host=localhost;dbname=my_aquabear;charset=utf8",
                    "aquabear",
                    ""
                );

            } else { // locale (XAMPP)

                self::$connection = new PDO(
                    "mysql:host=localhost;dbname=my_aquabear;charset=utf8",
                    "root",
                    ""
                );
            }

            self::$connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        }

        return self::$connection;
    }
}