<?php
session_start();

    require __DIR__ . "/../functions/dbConnector.php";
    require __DIR__ . "/../functions/helper.php";

    $title = "Accueil";
    $description = "La liste des films que j'ai regardé.";
    $keywords = "accueil, films, liste";

    $db = connectToDb();
    
    $request = $db->prepare("SELECT * FROM film");
    $request->execute();
    $films = $request->fetchAll();

    // Générons le jéton de sécurité (csrf_token)
    $_SESSION['csrf_token'] = bin2hex(random_bytes(10));

    // dd($films);
?>
<?php require __DIR__ . "/../partials/head.php"; ?>

    <?php require __DIR__ . "/../partials/nav.php"; ?>

    <main class="container">
        <h1 class="text-center my-3 display-5">Liste des films</h1>

        <?php if(isset($_SESSION['success']) && !empty($_SESSION['success']) ) : ?>
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif ?>

        <div class="d-flex justify-content-end align-items-center my-3">
            <a href="/create.php" class="btn btn-primary shadow">Ajouter film</a>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-5 mx-auto">
                    <?php if(isset($films) && !empty($films)) : ?>
                        <?php foreach($films as $film) : ?>
                            <div class="card p-4 mb-3 shadow">
                                <p><strong>Titre du film</strong>: <?= htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8') ?></p>
                                <p><strong>Nom du/des acteurs</strong>: <?= htmlspecialchars($film['actors'], ENT_QUOTES, 'UTF-8') ?></p>
                                <hr>
                                <div>
                                    <a title="Les détails du film: <?= htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8') ?>" class="text-dark mx-2" data-bs-toggle="modal" data-bs-target="#modal-<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>" href="#"><i class="fa-solid fa-eye"></i></a>
                                    <a title="Modifier le film: <?= htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8') ?>" class="text-secondary mx-2" href="edit.php?filmId=<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>"><i class="fas fa-edit"></i></a>
                                    <a title="Supprimer le film: <?= htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8') ?>" onclick="event.preventDefault(); return confirm('Confirmer la suppression ?') && document.querySelector('#form-delete-film-<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>').submit();" title="Supprimer" href="#" class="text-danger m-2"><i class="fa-solid fa-trash-can"></i></a>
                                    <form action="delete.php?filmId=<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>" method="post" id="form-delete-film-<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="filmId" value="<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    </form>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="modal-<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>" tabindex="-1" aria-labelledby="modal-<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="modal-title fs-5" id="modal-<?= htmlspecialchars($film['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($film['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        <p><strong>Note</strong>: <?= isset($film['review']) && $film['review'] !== "" ? htmlspecialchars($film['review'], ENT_QUOTES, 'UTF-8') : 'Non renseignée';  ?></p>
                                        <p><strong>Commentaire</strong>: <?= isset($film['comment']) && $film['comment'] !== "" ? nl2br(htmlspecialchars($film['comment'], ENT_QUOTES, 'UTF-8')) : 'Non renseigné';  ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php else :?>
                        <p>Aucun film ajouté à la liste pour l'instant</p>
                    <?php endif ?>

                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . "/../partials/footer.php"; ?>

<?php require __DIR__ . "/../partials/scripts_foot.php"; ?>