<?php


    /**
     * Protège le système contre les failles de type csrf.
     *
     * @param string $postCsrfToken
     * @param string $sessionCsrfToken
     * 
     * @return boolean true si le csrf_token est valide et false dans le cas contraire.
     */
    function isCsrfTokenValid(string $postCsrfToken, string $sessionCsrfToken): bool
    {
        if ( !isset($postCsrfToken) || !isset($sessionCsrfToken) ) 
        {
            unset($_SESSION['csrf_token']);
            unset($_POST['csrf_token']);
            return false;
        }

        if ( empty($postCsrfToken) || empty($sessionCsrfToken) ) 
        {
            unset($_SESSION['csrf_token']);
            unset($_POST['csrf_token']);
            return false;
        }

        if ( $postCsrfToken !== $sessionCsrfToken ) 
        {
            unset($_SESSION['csrf_token']);
            unset($_POST['csrf_token']);
            return false;
        }

        unset($_SESSION['csrf_token']);
        unset($_POST['csrf_token']);
        return true;
    }


    /**
     * Protège le système contre les robots spameurs.
     *
     * @param string $honeyPotValue
     * @return boolean retourne true si le pot de miel est léché et false dans le cas contraire.
     */
    function isHoneyPotLiked(string $honeyPotValue): bool
    {
        if ( ! isset($honeyPotValue) ) 
        {
            return true;
        }

        if ( $honeyPotValue !== "" ) 
        {
            return true;
        }

        return false;
    }