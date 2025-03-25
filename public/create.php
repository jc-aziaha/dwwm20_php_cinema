<?php
session_start();

    require __DIR__ . "/../functions/security.php";
    require __DIR__ . "/../functions/helper.php";
    require __DIR__ . "/../functions/dbConnector.php";


    // Si les données arrivent au serveur via la méthode POST
    if ( "POST" === $_SERVER['REQUEST_METHOD'] ) 
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
            return header("Location: create.php");
        }
        
        if( ! isCsrfTokenValid($_POST['csrf_token'], $_SESSION['csrf_token']) )
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: create.php");
        }


        // 2- Protéger le serveur contre les robots des spameurs
        if ( ! array_key_exists('honey_pot', $_POST) ) 
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: create.php");
        }
        
        if ( isHoneyPotLiked($_POST['honey_pot']) )
        {
            // Effectuer une redirection vers la page de laquelle proviennent les données,
            // Puis, arrêter l'exécution du script.
            return header("Location: create.php");
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
            return header("Location: create.php");
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
        $request = $db->prepare("INSERT INTO film (title, actors, review, comment, created_at, updated_at) VALUES (:title, :actors, :review, :comment, now(), now() )");

        $request->bindValue(":title", $_POST['title']);
        $request->bindValue(":actors", $_POST['actors']);
        $request->bindValue(":review", $reviewRounded);
        $request->bindValue(":comment", $_POST['comment']);

        $request->execute();
        $request->closeCursor(); // Non obligatoire.

        // 8- Générer un message flash de succès
        $_SESSION['success'] = "Le film a été ajouté avec succès.";
        
        // 9- Rediriger l'utilisateur vers la page d'accueil
        // Puis arrêter l'exécution du script.
        return header("Location: index.php");
    }

    // Générons le jéton de sécurité (csrf_token)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(10));
?>
<?php
    $title = "Nouveau film";
    $description = "Ajouter un nouveau film à la liste";
    $keywords = "nouveau, film";
?>
<?php require __DIR__ . "/../partials/head.php"; ?>

    <?php require __DIR__ . "/../partials/nav.php"; ?>

    <main class="container-fluid">
        <h1 class="text-center my-3 display-5">Nouveau film</h1>

        <!-- Formulaire d'ajout d'un nouveau film -->
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5 mx-auto bg-white p-4 rounded shadow">

                    <?php if( isset($_SESSION['formErrors']) && !empty($_SESSION['formErrors']) ) : ?>
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                <?php foreach($_SESSION['formErrors'] as $error) : ?>
                                    <li><?= $error ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['formErrors']); ?>
                    <?php endif ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="title">Titre du film <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" autofocus value="<?= isset($_SESSION['old']['title']) && $_SESSION['old']['title'] !== "" ? $_SESSION['old']['title'] : ''; unset($_SESSION['old']['title']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="actors">Nom du/des acteurs <span class="text-danger">*</span></label>
                            <input type="text" name="actors" id="actors" class="form-control" value="<?= isset($_SESSION['old']['actors']) && $_SESSION['old']['actors'] !== "" ? $_SESSION['old']['actors'] : ''; unset($_SESSION['old']['actors']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="review">Note / 5</label>
                            <input type="number" min="0" max="5" step=".1" name="review" id="review" class="form-control" value="<?= isset($_SESSION['old']['review']) && $_SESSION['old']['review'] !== "" ? $_SESSION['old']['review']: ''; unset($_SESSION['old']['review']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="comment">Laissez un commentaire</label>
                            <textarea name="comment" id="comment" class="form-control" rows="4"><?= isset($_SESSION['old']['comment']) && $_SESSION['old']['comment'] !== "" ? $_SESSION['old']['comment'] : ''; unset($_SESSION['old']['comment']); ?></textarea>
                        </div>
                        <div>
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        </div>
                        <div>
                            <input type="hidden" name="honey_pot" value="">
                        </div>
                        <div>
                            <input formnovalidate type="submit" class="btn btn-primary shadow">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . "/../partials/footer.php"; ?>

<?php require __DIR__ . "/../partials/scripts_foot.php"; ?>