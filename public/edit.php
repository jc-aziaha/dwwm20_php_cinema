<?php
session_start();

require __DIR__ . "/../functions/security.php"; // Chargement des fonctions liée à la sécurité
require __DIR__ . "/../functions/helper.php"; // Chargement des fonctions aide
require __DIR__ . "/../functions/dbConnector.php"; // Connexion à la base de données

    // Si l'identifiant du film à modifier n'existe pas ou qu'elle n'a pas de valeur,
    if ( !isset($_GET['filmId']) || empty($_GET['filmId']) ) 
    {
        // Rediriger l'utilisateur vers la page d'accueil.
        // Puis, arrêter l'exécution du script
        // dd('testons');
        return header("Location: index.php");
    }

    
    // Dans le cas contraire,
    
    // Protéger le serveur contre les failles de type XSS.
    $filmId = (int) htmlspecialchars($_GET['filmId']);
    
    
    // Récupérer le film depuis la base de données.
    $db = connectToDb();
    
    $request = $db->prepare("SELECT * FROM film WHERE id=:id");
    $request->bindValue(":id", $filmId);
    $request->execute();

    // Si le nombre total d'enregistrement est different de 1
    if ( $request->rowCount() != 1 )
    {
        // Rediriger l'utilisateur vers la page d'accueil.
        // Puis, arrêter l'exécution du script
        return header("Location: index.php");
    }

    // Dans le cas contraire, récupérons le film à modifier
    $film = $request->fetch();
    $request->closeCursor(); // Non obligatoire.
    
    // Si les données arrivent au serveur via la méthode POST
    if ( $_SERVER['REQUEST_METHOD'] === "POST" )
    {
        
        /**
         * **************************************************
         * Traitement du formulaire
         * **************************************************
         */

        // 1- Protéger le serveur contre les failles de type csrf
        if ( ! array_key_exists('csrf_token', $_POST) ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: edit.php");
        }
        
        if( ! isCsrfTokenValid($_POST['csrf_token'], $_SESSION['csrf_token']) )
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: edit.php");
        }


        // 2- Protéger le serveur contre les robots des spameurs
        if ( ! array_key_exists('honey_pot', $_POST) ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: edit.php");
        }
        
        if ( isHoneyPotLiked($_POST['honey_pot']) )
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: edit.php");
        }

        // dd("Continuer la partie");

        // 3- Définir les contraintes de validation du formulaire
        $formErrors = [];

        if ( isset($_POST['title']) ) 
        {
            if ( trim($_POST['title']) == "" ) 
            {
                $formErrors['title'] = "Le titre du film est obligatoire.";
            }
            
            if ( mb_strlen($_POST['title']) > 255 )
            {
                $formErrors['title'] = "Le titre ne doit pas dépasser 255 caractères.";
            }
        }

        if ( isset($_POST['actors']) ) 
        {
            if ( trim($_POST['actors']) == "" ) 
            {
                $formErrors['actors'] = "Le nom du/des acteurs est obligatoire.";
            }
            
            if ( mb_strlen($_POST['actors']) > 255 )
            {
                $formErrors['actors'] = "Le nom du/des acteurs ne doit pas dépasser 255 caractères.";
            }
        }

        if ( isset($_POST['review']) ) 
        {
            // Si la note est renseignée,
            if ( trim($_POST['review']) != "" )
            {
                if ( ! is_numeric($_POST['review']) )
                {
                    $formErrors['review'] = "La note doit être nombre.";
                }
                
                if ( $_POST['review'] < '0' || $_POST['review'] > '5' )
                {
                    $formErrors['review'] = "La note doit être être comprise entre 0 et 5.";
                }
            }
        }

        if ( isset($_POST['comment']) ) 
        {
            // Si le commentaire est renseignée,
            if ( trim($_POST['comment']) != "" )
            {
                if ( mb_strlen($_POST['comment']) > 500 )
                {
                    $formErrors['comment'] = "Le commentaire ne doit pas dépasser 500 caractères.";
                }
            }
        }


        // 4- Si le formulaire est invalide
        if ( count($formErrors) > 0 ) 
        {

            // Sauvegardons les messages d'erreurs en session
            $_SESSION['formErrors'] = $formErrors;

            // Sauvegardons les anciennes données provenant du formulaire en session
            $_SESSION['old'] = $_POST;

            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        // Dans le cas contraire
        // 5- Arrondir la note à un chiffre après la virgule
        $reviewRounded = null;
        if ( isset($_POST['review']) && $_POST['review'] !== "" ) 
        {
            $reviewRounded = round($_POST['review'], 1);
        }


        // 6- Etablir une connexion avec la base de données
        $db = connectToDb();

        // 7- Effectuer la requête d'insertion du nouveau film dans la table 'film'
        $request = $db->prepare("UPDATE film SET title=:title, actors=:actors, review=:review, comment=:comment, updated_at=now() WHERE id=:id");

        $request->bindValue(":title", $_POST['title']);
        $request->bindValue(":actors", $_POST['actors']);
        $request->bindValue(":review", $reviewRounded);
        $request->bindValue(":comment", $_POST['comment']);
        $request->bindValue(":id", $film['id']);

        $request->execute();
        $request->closeCursor(); // Non obligatoire.

        // 8- Générer un message flash de succès
        $_SESSION['success'] = "Le film a été modifié avec succès.";
        
        // 9- Rediriger l'utilisateur vers la page d'accueil
        // Puis arrêter l'exécution du script.
        return header("Location: index.php");
    }

    // Générons le jéton de sécurité.
    $_SESSION['csrf_token'] = bin2hex(random_bytes(30));
?>
<?php
    // Définition du titre de cette page
    $title = "Modification de ce film"; 

    // Définition de la description de la page
    $description = "Modification des informations de ce film et mise à jour dans la base de données.";

    // Mots clés
    $keywords="Modification, Mise à jour";
?>
<?php include __DIR__ . "/../partials/head.php"; ?>

    <?php include __DIR__ . "/../partials/nav.php"; ?>

    <!-- Le contenu spécifique à la page -->
    <main class="container">
        <h1 class="text-center my-3 display-5">Modifier ce film</h1>

        <div class="container">
            <div class="row">
                <div class="col-md-9 col-lg-5 mx-auto p-4 shadow bg-white rounded">

                    <?php if(isset($_SESSION['formErrors']) && !empty($_SESSION['formErrors'])) : ?>
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                <?php foreach($_SESSION['formErrors'] as $formError) : ?>
                                    <li><?= $formError; ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['formErrors']); ?>
                    <?php endif ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="title">Le nom du film <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" value="<?= isset($_SESSION['old']['title']) ? htmlspecialchars($_SESSION['old']['title']) : htmlspecialchars($film['title']); unset($_SESSION['old']['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="actors">Le nom du/des acteurs <span class="text-danger">*</span></label>
                            <input type="text" name="actors" id="actors" class="form-control" value="<?= isset($_SESSION['old']['actors']) ? htmlspecialchars($_SESSION['old']['actors']) : htmlspecialchars($film['actors']); unset($_SESSION['old']['actors']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="review">La note / 5</label>
                            <input type="number" min="0" max="5" step=".1" name="review" id="review" class="form-control" value="<?= isset($_SESSION['old']['review']) ? htmlspecialchars($_SESSION['old']['review']) : htmlspecialchars($film['review']?? ""); unset($_SESSION['old']['review']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="comment">Laissez un commentaire</label>
                            <textarea name="comment" id="comment" class="form-control" rows="4"><?= isset($_SESSION['old']['comment']) ? htmlspecialchars($_SESSION['old']['comment']) : htmlspecialchars($film['comment'] ?? ""); unset($_SESSION['old']['comment']); ?></textarea>
                        </div>
                        <div class="mb-3 d-none">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                        </div>
                        <div class="mb-3 d-none">
                            <input type="hidden" name="honey_pot" value="">
                        </div>
                        <div>
                            <input formnovalidate type="submit" class="btn btn-primary" value="Modifier">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include __DIR__ . "/../partials/footer.php"; ?>

<?php include __DIR__ . "/../partials/scripts_foot.php"; ?>