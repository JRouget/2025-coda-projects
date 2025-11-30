<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'artist.php';

$id = $_GET['id'];

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

try {
    $allArtists = $db->executeQuery(<<<SQL
    SELECT 
        id,
        name,
        cover,
        biography,
        monthly_listeners
    FROM artist
    WHERE id = $id
    SQL);

} catch (PDOException $ex) {
    header("Location: erreur.php?message=" . urlencode("Erreur lors de la récupération de l'artiste."));
    exit;
}

if (empty($allArtists)) {
    header("Location: erreur.php?message=" . urlencode("L'artiste demandé n'existe pas."));
    exit;
}

$artist = $allArtists[0];

$artistName = $artist['name'];
$artistBio = $artist['biography'];
$artistCover = $artist['cover'];
$artistListeners = $artist['monthly_listeners'];

if ($artistListeners >= 1000000) {
    $first = floor($artistListeners / 1000000);
    $artistListeners = "$first M";
}
else if ($artistListeners >= 1000) {
    $first = floor($artistListeners / 1000);
    $artistListeners = "$first K";
}

try {
    $allSongs = $db->executeQuery(<<<SQL
        SELECT
            song.id,
            song.name,
            song.artist_id,
            song.album_id,
            song.duration,
            song.note,
            album.cover
        FROM song
        INNER JOIN album ON song.album_id = album.id
        WHERE song.artist_id = :id
        ORDER BY song.note DESC
        LIMIT 5
    SQL, ["id" => $id]);
}
catch (PDOException $ex) {
    echo "Erreur lors de la connexion à la base de données" . $ex->getMessage();
    exit;
}

$topHTML = '<div class="mt-4">';

foreach ($allSongs as $song) {

    $songName = $song['name'];
    $songDuration = $song['duration'];
    $songNote = $song['note'];
    $songCover = $song['cover'];

    $minutes = 0.0;
    $secondes = 0.0;

    if ($songDuration >= 60) {
        $minutes = floor($songDuration / 60);
        $secondes = $songDuration % 60;
    }

    $topHTML .= <<<HTML
    <div class="card bg-dark text-white border-secondary mb-3 p-2">
        <div class="d-flex align-items-center">
            <img src="$songCover" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
            
            <div>
                <h5 class="mb-1">$songName</h5>
                <p class="mb-0 text-secondary">Durée : $minutes:$secondes min</p>
                <p class="mb-0 text-secondary">Note : $songNote</p>
            </div>
        </div>
    </div>
HTML;
}

$topHTML .= '</div>';

try {
    $allAlbums = $db->executeQuery(<<<SQL
    SELECT 
        id,
        name,
        cover,
        release_date
    FROM album
    WHERE artist_id = :id
SQL, ["id" => $id]);
}
catch (PDOException $ex) {
    echo "Erreur lors de la requête : " . $ex->getMessage();
    exit;
}

$albumHTML = '<div class="row mt-4">';

foreach ($allAlbums as $album) {

    $albumId = $album['id'];
    $albumName = $album['name'];
    $albumCover = $album['cover'];
    $albumReleaseDate = $album['release_date'];

    $albumHTML .= <<<HTML
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="card h-100 bg-dark text-white border-secondary shadow-sm">
                <a href="album.php?id=$albumId" style="text-decoration: none; color: white"><img src="$albumCover" class="card-img-top" style="object-fit: cover; height: 250px;">
                <div class="card-body text-center">
                    <h5 class="card-title">$albumName</h5>
                    <p class="card-text">$albumReleaseDate</p></a>
                </div>
            </div>
        </div>
HTML;
}

$albumHTML .= '</div>';


$artistAsHTML = <<<HTML
<header style="display: flex; justify-content: space-between; align-items: center;">
<a href="index.php" class="link text-white" > < Retour à l'accueil</a>
<a href="artists.php" class="link text-white" text-align: end">Retour à la liste des artistes</a>
</header>
<div class="container bg-dark text-white p-4">

    <div class="text-center mb-4">
        <img src="$artistCover" class="rounded-circle mb-3 shadow" width="200">
        <h1 class="fw-bold">$artistName</h1>
        <p>$artistBio</p>
        <p>$artistListeners</p>
    </div>

    <h2 class="mb-3">Top 5 des chansons</h2>
    $topHTML
    
    <h2 class="mb-3 mt-4">Albums</h2>
    $albumHTML
</div>
HTML;

echo (new HTMLPage(title: "$artistName - Lowify"))
    ->setupBootstrap([
        "class" => "bg-dark text-white p-4",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($artistAsHTML)
    ->render();