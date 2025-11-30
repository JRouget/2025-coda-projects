<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'album.php';

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

$allAlbums = [];

try {
    $allAlbums = $db->executeQuery(<<<SQL
    SELECT
        id,
        name,
        cover,
        release_date
        FROM album
        WHERE id = :id
SQL, ["id" => $id]);
} catch (PDOException $ex) {
    echo "Erreur lors de la requête en base de données : " . $ex->getMessage();
    exit;
}

$album = $allAlbums[0];

$albumName = $album['name'];
$albumCover = $album['cover'];
$albumReleaseDate = $album['release_date'];


try {
    $albumSongs = $db->executeQuery(<<<SQL
    SELECT
        song.name,
        album.cover,
        song.duration,
        song.note
        from song
        Inner join album on song.album_id = album.id
        where song.album_id = :id
SQL, ["id" => $id]);
}
catch (PDOException $ex) {
    echo "Erreur lors de la connexion à la base de données". $ex->getMessage();
    exit;
}

$songHTML = "";

foreach ($albumSongs as $songs) {

    $songName = $songs['name'];
    $songDuration = $songs['duration'];
    $songNote = $songs['note'];
    $minutes = 0.0;
    $secondes = 0.0;

    if ($songDuration >= 60) {
        $minutes = floor($songDuration / 60);
        $secondes = $songDuration % 60;
    }

$songHTML .= <<<HTML
    <div class="card bg-dark text-white border-secondary mb-3 p-2">
        <div class="d-flex align-items-center">
            <img src="$albumCover" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
            
            <div>
                <h5 class="mb-1">$songName</h5>
                <p class="mb-0 text-secondary">Durée : $minutes:$secondes min</p>
                <p class="mb-0 text-secondary">Note : $songNote</p>
            </div>
        </div>
    </div>
HTML;
}

$albumAsHTML = "";

$albumAsHTML .= <<<HTML
<header style="display: flex; justify-content: space-between; align-items: center;">
<a href="index.php" class="link text-white"> < Retour à l'accueil</a>
<a href="artists.php" class="link text-white" text-align: end">Retour à la liste des artistes</a>
</header>
<div class="container bg-dark text-white p-4">

    <div class="text-center mb-4">
        <img src="$albumCover" class="rounded-circle mb-3 shadow" width="200">
        <h1 class="fw-bold">$albumName</h1>
        <p>$albumReleaseDate</p>
    </div>
    $songHTML
</div>
HTML;

echo (new HTMLPage(title: "$albumName - Lowify"))
    ->setupBootstrap([
        "class" => "bg-dark text-white p-4",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($albumAsHTML)
    ->render();