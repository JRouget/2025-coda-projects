<?php

$joueur = "Joshua";
$ordi = 0;
$choix = "Faites un choix";



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
else if ($choixPlayer == "pierre") {
    $choix = "Faites un choix";
}

$result = "";
if ($choice === "pas choisi" || $phpChoice === "pas choisi") {
    $result = "Faites un choix pour commencer la partie !";
} else if ($choice === $phpChoice) {
    $result = "Egalité";
} else if (
    ($choice === 'pierre' && $phpChoice === 'ciseaux') ||
    ($choice === 'feuille' && $phpChoice === 'pierre') ||
    ($choice === 'ciseaux' && $phpChoice === 'feuille')
) {
    $result =  'GG ! Vous avez gagné !';
} else {
    $result = "Vous avez perdu !";
}

$html = <<<HTML
<html>
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
<p>Resultat</p>
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

