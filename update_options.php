<?php
// Koble til databasen
$servername = "localhost";
$username = "root";
$password = "";
$database = "racket";

$conn = new mysqli($servername, $username, $password, $database);

// Sjekk om valget er sendt fra klienten
if (isset($_GET['selected_option'])) {
    $selected_option = $_GET['selected_option'];

    // Forbered SQL-spørringen avhengig av valget
    if ($selected_option === 'all') {
        $sql_svar = "SELECT land_id, land_navn FROM land ORDER BY RAND() LIMIT 3";
    } else {
        // Anta at kolonnenavnene i databasen er "land_navn" og "kontinent"
        $sql_svar = "SELECT land_id, land_navn FROM land WHERE kontinent='$selected_option' ORDER BY RAND() LIMIT 3";
    }

    // Utfør SQL-spørringen
    $result_svar = $conn->query($sql_svar);

    $svar_alternativer = array();
    if ($result_svar->num_rows > 0) {
        while($row_svar = $result_svar->fetch_assoc()) {
            $svar_alternativer[] = $row_svar["land_navn"];
        }
    }

    // Returner svaralternativene som JSON
    echo json_encode($svar_alternativer);
} else {
    // Hvis ingen valg ble sendt, returner en feilmelding
    echo "Feil: Ingen valg mottatt.";
}

// Lukk tilkoblingen til databasen
$conn->close();
?>
