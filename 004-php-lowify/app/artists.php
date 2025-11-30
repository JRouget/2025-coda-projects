<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'artists.php';

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    echo "Erreur lors de la connexion à la base de données : " . $ex->getMessage();
    exit;
}

$allArtists = [];

try {
    $allArtists = $db->executeQuery(<<<SQL
    SELECT 
        id,
        name,
        cover
    FROM artist
SQL);

} catch (PDOException $ex) {
    echo "Erreur lors de la requête en base de données : " . $ex->getMessage();
    exit;
}

$artistsAsHTML = "";

$iterator = 0;

foreach ($allArtists as $artist) {
    $artistName = $artist['name'];
    $artistId = $artist['id'];
    $artistCover = $artist['cover'];

    if ($iterator % 4 == 0) {
        $artistsAsHTML .= '<div class="row mb-4">';
    }

        $artistsAsHTML .= <<<HTML
            <div class="col-lg-3 col-md-6 mb-4">
                <a href="artist.php?id=$artistId" class="text-decoration-none text-white">
                    <div class="card h-100 bg-dark text-white border-dark shadow">
                        <img src="$artistCover" class="card-img-top rounded-circle" alt="Image 1">
                        <div class="card-body bg-secondary-subtle  text-white">
                            <h5 class="card-title">$artistName</h5>
                        </div>
                    </div>
                </a>
            </div>
HTML;

    if ($iterator % 4 == 3) {
        $artistsAsHTML .= '</div>';
    }

    $iterator++;
}

$html = <<<HTML
<header>
<a href="index.php" class="link text-white"> < Retour à l'accueil</a>
</header>
<div class="container bg-dark text-white p-4">

    <h1 class="mb-4">Artistes</h1>
    
    <div>
    {$artistsAsHTML}
    </div>
</div>
HTML;
echo (new HTMLPage(title: "Artistes - Lowify"))
    ->setupBootstrap([
        "class" => "bg-dark text-white p-4",
        "data-bs-theme " => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($html)
    ->render();