<?php

function takeRandom(string $subject): string
{
    if ($subject === '') {
        throw new InvalidArgumentException("La chaîne de caractères ne peut pas être vide.");
    }

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

    if ($pool === "") {
        return "Aucun type de caractère sélectionné.";
    }

    $password = "";

    if ($useAlphaMin) $password .= takeRandom($alphaMin);
    if ($useAlphaMaj) $password .= takeRandom($alphaMaj);
    if ($useNum)      $password .= takeRandom($numbers);
    if ($useSymbols)  $password .= takeRandom($symbols);

    while (strlen($password) < $size) {
        $password .= takeRandom($pool);
    }

    return str_shuffle($password);
}

$size        = isset($_POST['size']) ? (int)$_POST['size'] : 12;
$useAlphaMin = isset($_POST['use-alpha-min']);
$useAlphaMaj = isset($_POST['use-alpha-maj']);
$useNum      = isset($_POST['use-num']);
$useSymbols  = isset($_POST['use-symbols']);

$useAlphaMinChecked = $useAlphaMin ? "checked" : "";
$useAlphaMajChecked = $useAlphaMaj ? "checked" : "";
$useNumChecked      = $useNum ? "checked" : "";
$useSymbolsChecked  = $useSymbols ? "checked" : "";

$sizeOptions = "";
for ($i = 8; $i <= 42; $i++) {
    $selected = $i === $size ? "selected" : "";
    $sizeOptions .= "<option value=\"$i\" $selected>$i</option>";
}

$generated = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $generated = generatePassword($size, $useAlphaMin, $useAlphaMaj, $useNum, $useSymbols);
    } catch (Exception $e) {
        $generated = "Erreur : " . $e->getMessage();
    }
} else {
    $useAlphaMin = $useAlphaMaj = $useNum = $useSymbols = true;
}

$page = <<<HTML
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Générateur de mot de passe</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #451E16;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .password-box {
            background: #e0e7ff;
            padding: 20px;
            border-radius: 10px;
            font-size: 1.5em;
            text-align: center;
            margin-bottom: 30px;
            word-break: break-all;
        }
        form label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        .form-check {
            display: flex;
            align-items: center;
            margin-top: 10px;
            gap: 20px;
        }
        .form-check input[type="checkbox"] {
            margin-right: 10px;
            text-align: center;
        }
        button {
            display: block;
            margin: 30px auto 0 auto; /* Centrer le bouton */
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1em;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>
<div class="container">

    <h1>Générateur de mot de passe</h1>

    <div class="password-box">
        {$generated}
    </div>

    <form method="POST">

        <label>Taille du mot de passe :</label>
        <select name="size">
            {$sizeOptions}
        </select>

        <div class="form-check">
            <input type="checkbox" name="use-alpha-min" {$useAlphaMinChecked}>
            <label>Minuscules<br>(a-z)</label>
            <input type="checkbox" name="use-alpha-maj" {$useAlphaMajChecked}>
            <label>Majuscules<br>(A-Z)</label>
             <input type="checkbox" name="use-num" {$useNumChecked}>
            <label>Chiffres<br>(0-9)</label>
            <input type="checkbox" name="use-symbols" {$useSymbolsChecked}>
            <label>Symboles<br>(!@#$%^&*())</label>
        </div>
        <button type="submit">Générer !</button>
    </form>

</div>
</body>
</html>
HTML;

echo $page;
