<?php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'index.php';

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
    $topArtists = $db->executeQuery(<<<SQL
    SELECT 
        id,
        name,
        cover,
        monthly_listeners
    FROM artist
    ORDER BY monthly_listeners DESC
    LIMIT 5
    SQL);

} catch (PDOException $ex) {
    header("Location: erreur.php?message=" . urlencode("Erreur lors de la récupération de l'artiste."));
    exit;
}

$topArtistsHTML = "";

foreach ($topArtists as $artist) {

    $artistId = $artist['id'];
    $artistName = $artist['name'];
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

    $topArtistsHTML .= <<<HTML
    <div class="col-lg-3 col-md-6 mb-4">
        <a href="artist.php?id=$artistId" class="text-decoration-none text-white">
            <div class="card h-100 bg-dark text-white border-dark shadow">
                <img src="$artistCover" class="card-img-top rounded-circle" alt="Image 1">
                <div class="card-body bg-secondary-subtle text-white">
                    <h5 class="card-title">$artistName</h5>
                    <h5 class="card-title">$artistListeners</h5>
                </div>
            </div>
        </a>
    </div>
HTML;
}


try {
    $recentsAlbums = $db->executeQuery(<<<SQL
    SELECT 
        id,
        name,
        cover,
        release_date
    FROM album
    ORDER BY release_date DESC
    LIMIT 5
    SQL);

} catch (PDOException $ex) {
    header("Location: erreur.php?message=" . urlencode("Erreur lors de la récupération de l'artiste."));
    exit;
}

$recentsAlbumsHTML = "";

foreach ($recentsAlbums as $album) {
    $albumId = $album['id'];
    $albumName = $album['name'];
    $albumCover = $album['cover'];
    $albumReleaseDate = $album['release_date'];

    $recentsAlbumsHTML .= <<<HTML
    <div class="col-lg-3 col-md-6 mb-4">
        <a href="album.php?id=$albumId" class="text-decoration-none text-white">
            <div class="card h-100 bg-dark text-white border-dark shadow">
                <img src="$albumCover" class="card-img-top rounded-circle" alt="Image 1">
                <div class="card-body bg-secondary-subtle text-white">
                    <h5 class="card-title">$albumName</h5>
                </div>
            </div>
        </a>
    </div>
HTML;
}


try {
    $topAlbums = $db->executeQuery(<<<SQL
    SELECT 
        album.id,
        album.name,
        album.cover,
        album.release_date,
        AVG(song.note) AS average_note
    FROM album
    JOIN song ON song.album_id = album.id
    GROUP BY album.id, album.name, album.cover, album.release_date
    ORDER BY average_note DESC
    LIMIT 5
SQL);
} catch (PDOException $ex) {
    header("Location: erreur.php?message=" . urlencode("Erreur lors de la récupération de l'artiste."));
    exit;
}

$topAlbumsHTML = "";

foreach ($topAlbums as $album) {
    $albumId = $album['id'];
    $albumName = $album['name'];
    $albumCover = $album['cover'];
    $albumReleaseDate = $album['release_date'];

    $topAlbumsHTML .= <<<HTML
    <div class="col-lg-3 col-md-6 mb-4">
        <a href="album.php?id=$albumId" class="text-decoration-none text-white">
            <div class="card h-100 bg-dark text-white border-dark shadow">
                <img src="$albumCover" class="card-img-top rounded-circle" alt="Image 1">
                <div class="card-body bg-secondary-subtle text-white">
                    <h5 class="card-title">$albumName</h5>
                </div>
            </div>
        </a>
    </div>
HTML;
}


$indexAsHTML = <<<HTML
<header class="bg-dark text-white p-3 mb-4 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="m-0">Lowify</h1>

        <form action="search.php" method="GET" class="d-flex" style="max-width: 300px;">
            <input 
                type="text" 
                name="query" 
                class="form-control form-control-sm me-2" 
                placeholder="Rechercher..."
            >
            <button class="btn btn-primary btn-sm" type="submit" >OK</button>
        </form>
    </div>
</header>

<div class="container bg-dark text-white p-4">

    <h2 class="mb-4">Top 5 des artistes</h2>
    <div class="row">
        $topArtistsHTML
    </div>
    
    <h2 class="mb-4">Albums récents</h2>
    <div class="row">
        $recentsAlbumsHTML
    </div>
    
    <h2 class="mb-4">Top 5 des albums</h2>
    <div class="row">
        $topAlbumsHTML
    </div>

</div>
HTML;

echo (new HTMLPage(title: "Lowify"))
    ->setupBootstrap([
        "class" => "bg-dark text-white p-4",
        "data-bs-theme" => "dark"
    ])
    ->setupNavigationTransition()
    ->addContent($indexAsHTML)
    ->render();