<?php
// Koble til databasen
$servername = "localhost";
$username = "root";
$password = "";
$database = "racket";

$conn = new mysqli($servername, $username, $password, $database);

// Sjekk om skjemaet er sendt og valget er gjort
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kontinent"])) {
    $valg = $_POST["kontinent"];

    // Hent et tilfeldig bilde fra flagg-tabellen basert på valget
    if ($valg == "alle") {
        $sql_bilde = "SELECT flagg_id, flagg_bilde_path, land_id FROM flagg ORDER BY RAND() LIMIT 1";
    } else {
        $sql_bilde = "SELECT f.flagg_id, f.flagg_bilde_path, f.land_id 
                      FROM flagg AS f
                      JOIN land AS l ON f.land_id = l.land_id
                      WHERE l.kontinent = '$valg'
                      ORDER BY RAND() LIMIT 1";
    }

    $result_bilde = $conn->query($sql_bilde);

    if ($result_bilde->num_rows > 0) {
        $row_bilde = $result_bilde->fetch_assoc();
        $bilde_id = $row_bilde["flagg_id"];
        $bilde_path = $row_bilde["flagg_bilde_path"];
        $riktig_land_id = $row_bilde["land_id"];

        // Fortsett med å hente svaralternativene basert på det valgte kontinentet eller "alle land"
        // Hent tre tilfeldige land fra land-tabellen som svaralternativer
        $sql_svar = "SELECT land_id, land_navn FROM land WHERE land_id != $riktig_land_id ORDER BY RAND() LIMIT 3";
        $result_svar = $conn->query($sql_svar);

        $svar_alternativer = array();
        if ($result_svar->num_rows > 0) {
            while($row_svar = $result_svar->fetch_assoc()) {
                $svar_alternativer[] = $row_svar["land_navn"];
            }
        }
        // Legg til koden for å hente og vise bildet og svaralternativene her
    } else {
        $bilde_path = "path/til/default/bilde.jpg"; // Bruk et standardbilde hvis ingen bilde ble funnet
    }

    // Hent tre tilfeldige land fra land-tabellen som svaralternativer
$sql_svar = "SELECT land_id, land_navn FROM land WHERE land_id != $riktig_land_id ORDER BY RAND() LIMIT 3";
$result_svar = $conn->query($sql_svar);

$svar_alternativer = array();
if ($result_svar->num_rows > 0) {
    while($row_svar = $result_svar->fetch_assoc()) {
        $svar_alternativer[] = $row_svar["land_navn"];
    }
}

// Legg til det riktige landet som et av svaralternativene
$riktig_land_navn = "";
$sql_riktig_land = "SELECT land_navn FROM land WHERE land_id = $riktig_land_id";
$result_riktig_land = $conn->query($sql_riktig_land);
if ($result_riktig_land->num_rows > 0) {
    $row_riktig_land = $result_riktig_land->fetch_assoc();
    $riktig_land_navn = $row_riktig_land["land_navn"];
    array_push($svar_alternativer, $riktig_land_navn);
}

// Bland svaralternativene slik at det ikke er opplagt hvilket som er det riktige
shuffle($svar_alternativer);

}

$conn->close();
?>
    <div id="spill">
        <div id="bilde-container">
            <!-- Vis bildet på siden -->
            <img id="bilde" src="<?php echo $bilde_path; ?>" alt="Tilfeldig Bilde">
        </div>
        
        <!-- Vis svaralternativer hvis det er noen -->
        <?php if (!empty($svar_alternativer)) { ?>
        <ul>
            <?php foreach ($svar_alternativer as $alternativ) { ?>
                <li class="svar-alternativ"><?php echo $alternativ; ?></li>
            <?php } ?>
        </ul>
        <?php } else { ?>
            <p>Ingen svaralternativer funnet.</p>
        <?php } ?>

        
        <div id="bottom-menu">
            <div class="menu-item">&#9664; Hele verden</div>
            
            <div class="menu-item">
                <span id="score">Score: <span id="score-verdi">0</span></span>
                <span id="livsteller"> 
                    <span id="liv1">&#10084;</span>
                    <span id="liv2">&#10084;</span>
                    <span id="liv3">&#10084;</span>
                </span>
            </div>
        </div>
    </div>