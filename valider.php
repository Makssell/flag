<?php
// Sjekk om skjemaet er sendt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hent dataene fra skjemaet
    $bilde_id = $_POST["bilde_id"];
    $riktig_land_navn = $_POST["riktig_land_navn"];
    $valgt_land = $_POST["valgt_land"];

    // Sjekk om det valgte svaret er riktig
    if ($valgt_land == $riktig_land_navn) {
        $melding = "Gratulerer, svaret ditt er riktig!";
    } else {
        $melding = "Dessverre, svaret ditt er feil. Det riktige svaret er $riktig_land_navn.";
    }
} else {
    // Hvis skjemaet ikke er sendt, omdiriger brukeren tilbake til startsiden
    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valider Svar</title>
</head>
<body>
    <h2>Tilbakemelding</h2>
    <p><?php echo $melding; ?></p>

    <p><a href="home.php">Tilbake til startsiden</a></p>
</body>
</html>
