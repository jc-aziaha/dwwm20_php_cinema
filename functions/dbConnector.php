<?php

    /**
     * Permet d'établir une connexion avec la base de données.
     *
     * @return PDO
     */
    function connectToDb(): PDO
    {
        try 
        {
            $dbDsn = 'mysql:dbname=dwwm20-php-cinema;host=127.0.0.1;port=3306';
            $dbUser = 'root';
            $dbPassword = '';
            
            $db = new PDO($dbDsn, $dbUser, $dbPassword);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        }
        catch (\PDOException $exception) 
        {
            die("Error connection to database: " . $exception->getMessage());
        }
    }
?>