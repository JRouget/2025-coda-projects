<?php

function generateSelectOptions(int $selected = 12): string
{
    $html = "";
    foreach (range(8, 42) as $value) {
        $isSelected = $value === $selected ? 'selected' : '';
        $html .= "<option value=\"{$value}\" {$isSelected}>{$value}</option>";
    }
    return $html;
}

function takeRandom(string $subject): string
{
    if ($subject === '')
        throw new InvalidArgumentException("Subject string cannot be empty.");

    $index = random_int(0, strlen($subject) - 1);
    return $subject[$index];
}

function generatePassword(
    int $size,
    bool $useAlphaMin,
    bool $useAlphaMaj,
    bool $useNum,
    bool $useSymbols
): string {

    $alphaMin = "abcdefghijklmnopqrstuvwxyz";
    $alphaMaj = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $numbers  = "0123456789";
    $symbols  = "!@#$%^&*()";

    $pool = "";
    if ($useAlphaMin) $pool .= $alphaMin;
    if ($useAlphaMaj) $pool .= $alphaMaj;
    if ($useNum)      $pool .= $numbers;
    if ($useSymbols)  $pool .= $symbols;

    if ($pool === "")
        return "Aucun type de caractère sélectionné.";

    $password = "";

    if ($useAlphaMin) $password .= takeRandom($alphaMin);
    if ($useAlphaMaj) $password .= takeRandom($alphaMaj);
    if ($useNum)      $password .= takeRandom($numbers);
    if ($useSymbols)  $password .= takeRandom($symbols);

    while (strlen($password) < $size)
        $password .= takeRandom($pool);

    return str_shuffle($password);
}

$generated   = "...";
$size        = isset($_POST['size']) ? (int) $_POST['size'] : 12;

$useAlphaMin = isset($_POST['use-alpha-min']);
$useAlphaMaj = isset($_POST['use-alpha-maj']);
$useNum      = isset($_POST['use-num']);
$useSymbols  = isset($_POST['use-symbols']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $generated = generatePassword($size, $useAlphaMin, $useAlphaMaj, $useNum, $useSymbols);
    } catch (Exception $e) {
        $generated = "Erreur : " . $e->getMessage();
    }
} else {
    $useAlphaMin = $useAlphaMaj = $useNum = $useSymbols = true;
}

$sizeSelectorOptions = generateSelectOptions($size);

$useAlphaMinChecked = $useAlphaMin ? "checked" : "";
$useAlphaMajChecked = $useAlphaMaj ? "checked" : "";
$useNumChecked      = $useNum ? "checked" : "";
$useSymbolsChecked  = $useSymbols ? "checked" : "";

$page = <<<HTML
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Générateur de mot de passe</title>
</head>
<body>
<div class="container">

    <h1 class="mt-4">Générateur de mot de passe</h1>

    <div class="row pt-4">
        <div class="col-md-12">
            <div class="alert alert-dark">
                <div class="h3">{$generated}</div>
            </div>
        </div>
    </div>

    <form method="POST">
        <h4>Paramètres</h4>

        <label class="form-label">Taille :</label>
        <select class="form-select" name="size">{$sizeSelectorOptions}</select>

        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="use-alpha-min" {$useAlphaMinChecked}>
            <label class="form-check-label">Minuscules (a-z)</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="use-alpha-maj" {$useAlphaMajChecked}>
            <label class="form-check-label">Majuscules (A-Z)</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="use-num" {$useNumChecked}>
            <label class="form-check-label">Chiffres (0-9)</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="use-symbols" {$useSymbolsChecked}>
            <label class="form-check-label">Symboles (!@#$%^&*())</label>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Générer !</button>
    </form>

</div>
</body>
</html>
HTML;

echo $page;
