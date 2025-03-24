<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?php if(isset($title) && !empty($title)) : ?>
            <title><?= $title ?> - Cinema</title>
        <?php else : ?>
            <title>Cinéma</title>
        <?php endif ?>


        <!-- SEO -->
        <?php if(isset($description) && !empty($description)) : ?>
            <meta name="description" content="<?= $description ?>">         
        <?php endif ?>

        <?php if(isset($keywords) && !empty($keywords)) : ?>
            <meta name="keywords" content="<?= $keywords ?>">         
        <?php endif ?>

        <meta name="robots" content="index, follow">
        <meta name="author" content="aicha-services">
        <meta name="publisher" content="aicha-services">

        <!-- Font awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Google font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 Stylesheet -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

        <!-- My stylesheet -->
        <link rel="stylesheet" href="/assets/styles/app.css">
    </head>
    <body class="bg-light">