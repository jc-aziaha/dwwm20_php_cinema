<?php

    /**
     * Permet d'afficher le contenu d'une variable et d'arrêter l'exécution du script.
     *
     * @param mixed $data
     * @return void
     */
    function dd(mixed $data): void
    {
        var_dump($data);
        die();
    }

    /**
     * Permet d'afficher le contenu d'une variable.
     *
     * @param mixed $data
     * @return void
     */
    function dump(mixed $data): void
    {
        var_dump($data);
    }