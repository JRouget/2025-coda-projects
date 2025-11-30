<?php

require_once 'inc/page.inc.php';

$messageError = $_GET['message'] ?? "Oups ! Tu ne devrais pas être là.";

$errorHTML = <<<HTML
<header style="display: flex; justify-content: space-between; align-items: center;">
<a href="index.php" class="link text-white" < Retour à l'accueil</a>
<a href="artists.php" class="link text-white" text-align: end">Retour à la liste des artistes</a>
</header>
<div class="container text-center p-5">
    <h1 class="text-danger mb-4">Erreur</h1>
    <p class="fs-4 mb-4">$messageError</p>

    <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
</div>
HTML;

echo (new HTMLPage("Erreur"))
    ->setupBootstrap()
    ->addContent($errorHTML)
    ->render();