<?php

$joueur = "Joshua";
$ordi = 0;
$choix = "Faites un choix";
$ordi = ["pierre", "feuille", "ciseaux"];
$phpChoice = $ordi[array_rand($ordi)];


if (!isset($_GET['choix'])) {
    $phpChoice = 0;
} else {
    $tab = ["pierre", "feuille", "ciseaux"];
    $phpChoice = $tab[array_rand($tab)];
}

$choixPlayer = $_GET['choix'] ?? "Faites un choix";

if ($choixPlayer == "pierre") {
    $choix = "Choix : Pierre";
}
else if ($choixPlayer == "feuille") {
    $choix = "Choix : Feuille";
}
else if ($choixPlayer == "ciseaux") {
    $choix = "Choix : Ciseaux";
}

$result = "";
if ($choixPlayer === "pas choisi" || $phpChoice === "pas choisi") {
    $result = "Faites un choix pour commencer la partie !";
} else if ($choixPlayer === $phpChoice) {
    $result = "Egalité";
} else if (
    ($choixPlayer === 'pierre' && $phpChoice === 'ciseaux') ||
    ($choixPlayer === 'feuille' && $phpChoice === 'pierre') ||
    ($choixPlayer === 'ciseaux' && $phpChoice === 'feuille')
) {
    $result =  'GG ! Vous avez gagné !';
} else if ($choixPlayer === 'pas choisi'){
    $result = " ";
}
else if (
    ($choixPlayer === "feuille" && $phpChoice === 'ciseaux') ||
    ($choixPlayer === "pierre" && $phpChoice === 'feuille') ||
    ($choixPlayer === "ciseaux" && $phpChoice === 'pierre')
) {
    $result =  "Vous avez perdu !";
}

$html = <<<HTML
<html lang="fr">
<head>
<title>Index</title>
</head>
<body>
<h1>Jeu Pierre, Feuilles, Ciseaux</h1>
<div>
<section class='choix1'>
<p>$joueur</p>
<p>$choix</p>
</section>
<section class='choix2'>
<p>Ordi</p>
</section>
</div>
<section class='resultat'>
<p>$result</p>
</section>
<section class='boutons'>
<div></div>
<div><a href='index.php?choix=pierre'>Pierre</a></div>
<div><a href='index.php?choix=feuille'>Feuille<a/></div>
<div><a href='index.php?choix=ciseaux'>Ciseaux</a></div>
<div><a href="index.php">Reinitialiser</a></div>
</section>
</body>
</html>
HTML;

echo $html;

