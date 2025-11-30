<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'search.php';

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

$query = $_GET["query"] ?? "";
$queryLike = "%" . $query . "%";

// Si aucune recherche → message simple
if (trim($query) === "") {
    $resultsHTML = "<p>Aucun terme recherché.</p>";
} else {

    try {
        $artists = $db->executeQuery(<<<SQL
            SELECT id, name, cover
            FROM artist
            WHERE name LIKE ?
            SQL,
            [$queryLike]
        );
    } catch (PDOException $ex) {
        $artists = [];
    }

    $artistsHTML = "";
    foreach ($artists as $artist) {
        $id = $artist["id"];
        $name = $artist["name"];
        $cover = $artist["cover"];

        $artistsHTML .= <<<HTML
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="artist.php?id=$id" class="text-decoration-none text-white">
                <div class="card h-100 bg-dark text-white border-dark shadow">
                    <img src="$cover" class="card-img-top rounded-circle" alt="$name">
                    <div class="card-body bg-secondary-subtle text-white">
                        <h5 class="card-title">$name</h5>
                    </div>
                </div>
            </a>
        </div>
HTML;
    }

    try {
        $albums = $db->executeQuery(<<<SQL
            SELECT album.id, album.name, album.cover, album.release_date, artist.name AS artist_name
            FROM album
            JOIN artist ON album.artist_id = artist.id
            WHERE album.name LIKE ?
            SQL,
            [$queryLike]
        );
    } catch (PDOException $ex) {
        $albums = [];
    }

    $albumsHTML = "";
    foreach ($albums as $album) {
        $id = $album["id"];
        $name = $album["name"];
        $cover = $album["cover"];
        $date = substr($album["release_date"], 0, 4);
        $artistName = $album["artist_name"];

        $albumsHTML .= <<<HTML
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="album.php?id=$id" class="text-decoration-none text-white">
                <div class="card h-100 bg-dark text-white border-dark shadow">
                    <img src="$cover" class="card-img-top rounded-circle" alt="$name">
                    <div class="card-body bg-secondary-subtle text-white">
                        <h5 class="card-title">$name</h5>
                        <p class="mb-0">$artistName ($date)</p>
                    </div>
                </div>
            </a>
        </div>
HTML;
    }

    try {
        $songs = $db->executeQuery(<<<SQL
            SELECT song.id, 
                   song.name, 
                   song.duration, 
                   song.note,
                   album.cover,
                   album.name AS album_name, 
                   artist.name AS artist_name
            FROM song
            JOIN album ON song.album_id = album.id
            JOIN artist ON album.artist_id = artist.id
            WHERE song.name LIKE ?
            SQL,
            [$queryLike]
        );
    } catch (PDOException $ex) {
        $songs = [];
    }

    $songsHTML = "";
    foreach ($songs as $song) {
        $songName = $song["name"];
        $songNote = $song["note"];
        $songAlbum = $song["album_name"];
        $songArtist = $song["artist_name"];
        $albumCover = $song["cover"];

        $minutes = floor($song["duration"] / 60);
        $secondes = str_pad($song["duration"] % 60, 2, "0", STR_PAD_LEFT);

        $songsHTML .= <<<HTML
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

    $resultsHTML = <<<HTML
    <header style="display: flex; justify-content: space-between; align-items: center;">
<a href="index.php" class="link text-white"> < Retour à l'accueil</a>
<a href="artists.php" class="link text-white" text-align: end">Retour à la liste des artistes</a>
</header>
<div>
        <h2 class="mb-3">Résultats pour "$query"</h2>

        <h3 class="mt-4">Artistes</h3>
        <div class="row">
            $artistsHTML
        </div>

        <h3 class="mt-4">Albums</h3>
        <div class="row">
            $albumsHTML
        </div>

        <h3 class="mt-4">Chansons</h3>
        <div class="row">
            $songsHTML
        </div>
</div>
HTML;
}

echo (new HTMLPage(title: "Recherche"))
    ->setupBootstrap([
        "class" => "bg-dark text-white p-4",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($resultsHTML)
    ->render();