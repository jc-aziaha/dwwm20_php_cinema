<?php
session_start();

    require __DIR__ . "/../functions/dbConnector.php";
    require __DIR__ . "/../functions/security.php";

    // Si l'identifiant du film à modifier n'existe pas ou qu'il n'a pas de valeur,
    if ( !isset($_POST['filmId']) || empty($_POST['filmId']) ) 
    {
        // Rediriger l'utilisateur vers la page d'accueil.
        // Puis, arrêter l'exécution du script
        return header("Location: index.php");
    }

    // 1- Protéger le serveur contre les failles de type csrf
    if ( ! array_key_exists('csrf_token', $_POST) ) 
    {
        // Effectuer une redirection vers la page de laquelle proviennent les données,
        // Puis, arrêter l'exécution du script.
        return header("Location: index.php");
    }
    
    if( ! isCsrfTokenValid($_POST['csrf_token'], $_SESSION['csrf_token']) )
    {
        // Effectuer une redirection vers la page de laquelle proviennent les données,
        // Puis, arrêter l'exécution du script.
        return header("Location: index.php");
    }
     
    
    // Dans le cas contraire,
    // Protéger le serveur contre les failles de type XSS.
    $filmId = (int) htmlspecialchars($_POST['filmId'], ENT_QUOTES, 'UTF-8');

    // Tenter de récupérer le film en base de données.
    $db = connectToDb();

    $searchRequest = $db->prepare("SELECT id FROM film WHERE id=:id");
    $searchRequest->bindValue(":id", $filmId);
    $searchRequest->execute();

    // Si le nombre total d'enregistrements est different de 1
    if ( $searchRequest->rowCount() != 1 ) 
    {
        // Rediriger l'utilisateur vers la page d'accueil.
        // Puis, arrêter l'exécution du script
        return header("Location: index.php");
    }

    // Dans le cas contraire,
    // Récupérons le film de la base de données
    $film = $searchRequest->fetch();
    
    // Effectuer la requête de suppression du film en base de données
    $deleteRequest = $db->prepare("DELETE FROM film WHERE id=:id");
    $deleteRequest->bindValue(":id", $film['id']);
    $deleteRequest->execute();
    $deleteRequest->closeCursor();

    // Générer le message flash de succès de l'opération
    $_SESSION['success'] = "Le film a été supprimé avec succès.";

    // 9- Rediriger l'utilisateur vers la page d'accueil
        // Puis arrêter l'exécution du script.
    return header("Location: index.php");