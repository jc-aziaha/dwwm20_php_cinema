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


    /**
     * Affiche l'ancienne valeur provenant du formulaire en fonction de l'input précisé.
     *
     * @param array|null $oldData
     * @param string $input
     * 
     * @return string La valeur si elle existe et un chaine de caractère vide dans le cas contraire.
     */
    function old(array|null $oldData, string $input): string
    {
        if ( isset($oldData[$input]) && !empty($oldData[$input]) ) 
        {
            unset($_SESSION['old'][$input]);
            return $oldData[$input];
        }

        return '';
    }