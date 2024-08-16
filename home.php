<?php
// Koble til databasen
$servername = "localhost";
$username = "root";
$password = "";
$database = "racket";

$conn = new mysqli($servername, $username, $password, $database);

// Sjekk om det er valgt et kontinent
if (isset($_GET['kontinent']) && $_GET['kontinent'] !== 'alle') {
    // Hvis et spesifikt kontinent er valgt
    $kontinent_betingelse = "AND l.kontinent = '" . $conn->real_escape_string($_GET['kontinent']) . "'";
} else {
    // Hvis "Alle land" er valgt eller ingen kontinent er valgt
    $kontinent_betingelse = "";
}

// Hent et tilfeldig bilde fra flagg-tabellen basert på valgt kontinent
$sql_bilde = "SELECT f.flagg_id, f.flagg_bilde_path, f.land_id 
              FROM flagg f
              INNER JOIN land l ON f.land_id = l.land_id
              WHERE 1 $kontinent_betingelse
              ORDER BY RAND() 
              LIMIT 1";
$result_bilde = $conn->query($sql_bilde);

if ($result_bilde->num_rows > 0) {
    $row_bilde = $result_bilde->fetch_assoc();
    $bilde_id = $row_bilde["flagg_id"];
    $bilde_path = $row_bilde["flagg_bilde_path"];
    $riktig_land_id = $row_bilde["land_id"];
} else {
    $bilde_path = "path/til/default/bilde.jpg"; // Bruk et standardbilde hvis ingen bilde ble funnet
}



// Hent tre tilfeldige land fra land-tabellen som svaralternativer basert på valgt kontinent
$sql_svar = "SELECT l.land_id, l.land_navn 
             FROM land l
             WHERE l.land_id != $riktig_land_id 
             $kontinent_betingelse
             ORDER BY RAND() 
             LIMIT 3";
$result_svar = $conn->query($sql_svar);

$svar_alternativer = array();
if ($result_svar->num_rows > 0) {
    while ($row_svar = $result_svar->fetch_assoc()) {
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <title>Tilfeldig Bilde</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Default font: Poppins */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        
        
        @media screen and (max-width: 750px) {
            /* Stiler for når skjermstørrelsen er mindre enn 500px */
            #bottom-menu {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                z-index: 999;
            }

            #bilde-container {
                width: 60%;
            }

            ul {
                padding: 0;
                list-style-type: none;
                margin: 0;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                
                width: 100%;
                z-index: 999;
            }
            }

            .svar-alternativ {
                width: calc(50% - 10px);
                margin-bottom: 10px;
            }
        

        @media screen and (min-width: 750px) {
            /* Stiler for når skjermstørrelsen er større enn 500px */
            ul {
                padding: 0;
                list-style-type: none;
                margin: 0;
                display: flex;
                justify-content: center;
            }

            .svar-alternativ {
                margin: 5px;
            }
        }

        #spill {
            text-align: center;
            max-width: 650px;
            min-width: 650px;
            padding: 20px;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #bilde-container {
            height: 400px;
            /* Sett en fast høyde for bildekonteineren */
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        #bilde {
            max-height: 100%;
            border-radius: 5px;
            max-width: 100%;
            height: auto;
            width: auto;
            /* For å sikre proporsjonal skalering av bildet */
            transition: 0.1s;
        }



        ul {
            padding: 0;
            list-style-type: none;
            margin: 0;
            /* Fjern margin for å unngå unødvendig plassering */
            height: 80px;
            /* Sett en fast høyde for svaralternativene */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .svar-alternativ {
            background-color: white;
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.1s ease-in-out;
            /* Legg til en jevn overgang */
            z-index: 0;
        }

        .svar-alternativ:hover {
            transform: scale(1.05);
            /* Forstørr på hover */
            z-index: 1;
            /* Plasser over andre elementer */
        }

        .svar-alternativ:active {
            scale: 0.9;
        }

        .correct {
            color: green;
            background-color: greenyellow;
        }

        .wrong {
            color: red;
            background-color: red;
        }

        #bottom-menu {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #ccc;
            padding: 10px;
            z-index: 2;
        }

        #kontinent {
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            color: #333;
        }

        /* Tilpassing av alternativene */
        #kontinent option {
            font-size: 16px;
            background-color: #f9f9f9;
            color: #333;
        }

    </style>
</head>

<body>
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
            <!-- Legg til en dropdown-meny for kontinenter -->
            <select id="kontinent">
                <option value="alle">Alle land</option>
                <option value="Asia">Asia</option>
                <option value="Europa">Europa</option>
                <option value="Afrika">Afrika</option>
                <option value="Nord-Amerika">Nord-Amerika</option>
                <option value="Sør-Amerika">Sør-Amerika</option>
                <option value="Oseania">Oseania</option>

                <!-- Legg til resten av kontinentene etter behov -->
            </select>

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





    <script>
        // Lagre valgt kontinent når dropdown endres
        document.getElementById('kontinent').addEventListener('change', function() {
            var valgtKontinent = this.value;
            localStorage.setItem('valgtKontinent', valgtKontinent);
        });

        // Sett standardvalg for dropdown basert på lagret verdi
        var lagretKontinent = localStorage.getItem('valgtKontinent');
        if (lagretKontinent) {
            document.getElementById('kontinent').value = lagretKontinent;
        }

        document.getElementById('kontinent').addEventListener('change', function() {
            var valgtKontinent = this.value;
            // Hvis ingen kontinent er valgt, last inn siden på nytt for å vise alle land
            if (!valgtKontinent) {
                location.reload();
                return;
            }
            // Hvis et kontinent er valgt, last inn siden på nytt og send med valgt kontinent som parameter
            location.href = '?kontinent=' + encodeURIComponent(valgtKontinent);
        });

        // Første gang siden lastes, sjekk om det allerede er en lagret score
        var score = localStorage.getItem('score') || 0;
        var liv = localStorage.getItem('liv') || 3;

        // Sjekk om det ikke er noen lagret verdi for antall gjenværende liv
        if (liv === null) {
            // Sett antall liv til 3 hvis det ikke er noen lagret verdi
            liv = 3;
            localStorage.setItem('liv', liv);
        }

        document.getElementById('score-verdi').textContent = score;
        updateLivsteller();


        var svarAlternativer = document.querySelectorAll('.svar-alternativ');
        var riktigLand = "<?php echo $riktig_land_navn; ?>";


        svarAlternativer.forEach(function(alternativ) {
            alternativ.addEventListener('click', function() {
                if (alternativ.textContent === riktigLand) {
                    alternativ.classList.add('correct');
                    score++;
                    localStorage.setItem('score', score);

                    // Last inn et nytt tilfeldig bilde når det riktige svaret er valgt
                    setTimeout(function() {
                        location.reload();
                    }, 100); // Vent 1 sekund før du laster inn et nytt bilde
                } else {
                    alternativ.classList.add('wrong');
                    // Reduser livet hvis svaret er galt
                    liv--;
                    localStorage.setItem('liv', liv);
                    updateLivsteller();
                    // Gjør alle svaralternativer ikke-klikkbare etter valg
                    alternativ.style.pointerEvents = 'none';

                    // Sjekk om spillet er over (ingen liv igjen)
                    if (liv === 0) {
                        // Gjem flagg og svaralternativer
                        document.getElementById('bilde-container').style.display = 'none';
                        document.querySelector('ul').style.display = 'none';

                        // Vis teksten "Du gikk tom for liv"
                        var gameOverText = document.createElement('p');
                        gameOverText.textContent = 'Du gikk tom for liv.';
                        document.getElementById('spill').appendChild(gameOverText);

                        // Legg til en lenke for å prøve igjen
                        var tryAgainLink = document.createElement('a');
                        tryAgainLink.href = 'home.php';
                        tryAgainLink.textContent = 'Prøv igjen';
                        document.getElementById('spill').appendChild(tryAgainLink);

                        // Tilbakestill livstelleren til 3
                        localStorage.removeItem('score');
                        localStorage.removeItem('liv');


                        tryAgainLink.onclick = function() {
                            window.location.reload();
                            return false;
                        };
                    }
                }


            });
        });
        // Funksjon for å oppdatere livstelleren
        function updateLivsteller() {
            var livElementer = document.querySelectorAll('#livsteller span');
            for (var i = 0; i < livElementer.length; i++) {
                if (i < liv) {
                    livElementer[i].style.visibility = 'visible';
                } else {
                    livElementer[i].style.visibility = 'hidden';
                }
            }
        }
        
        // Legg til en event listener for å oppdatere stiler når vindusstørrelsen endres
        window.addEventListener('resize', function() {
            updateStyles();
        });

        // Kall funksjonen en gang ved initial lasting av siden
        updateStyles();

        function updateStyles() {
            // Sjekk om vindusbredden er mindre enn 500px
            if (window.innerWidth < 750) {
                // Legg til CSS-klassen for mobilvisning
                document.body.classList.add('mobile-view');
            } else {
                // Fjern CSS-klassen for mobilvisning hvis vindusbredden er større enn 500px
                document.body.classList.remove('mobile-view');
            }
        }
    </script>
</body>

</html>