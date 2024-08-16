<?php
// Kontroller om skjemaet ble sendt
if (isset($_POST['submit'])) {
    $target_dir = "flagg/";
    $target_file = $target_dir . basename($_FILES["flagg_bilde"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Kontroller om bildet er ekte eller ikke
    $check = getimagesize($_FILES["flagg_bilde"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "Filen er ikke et bilde.";
        $uploadOk = 0;
    }

    // Kontroller om filen allerede eksisterer
    if (file_exists($target_file)) {
        echo "Beklager, filen eksisterer allerede.";
        $uploadOk = 0;
    }

    // Kontroller filtypen
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Beklager, bare JPG, JPEG og PNG filer er tillatt.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Filen ble ikke lastet opp.";
    } else {
        // Prøv å laste opp filen
        if (move_uploaded_file($_FILES["flagg_bilde"]["tmp_name"], $target_file)) {
            // Lagre filstien i databasen sammen med landet
            $db_host = 'localhost';  // Erstatt med ditt databasevert
            $db_navn = 'racket'; // Erstatt med ditt databasenavn
            $db_bruker = 'root'; // Erstatt med ditt databasebrukernavn
            $db_passord = ''; // Erstatt med ditt databasepassord

            $db_conn = new mysqli($db_host, $db_bruker, $db_passord, $db_navn);

            if ($db_conn->connect_error) {
                die("Connection failed: " . $db_conn->connect_error);
            }

            $land_id = $_POST['land_id'];
            $flagg_bilde_path = $target_file;

            $sql = "INSERT INTO flagg (flagg_bilde_path, land_id) VALUES ('$flagg_bilde_path', $land_id)";

            if ($db_conn->query($sql) === TRUE) {
                echo "Flagget ble lastet opp og lagret i databasen.";
            } else {
                echo "Feil ved opplasting av flagget til databasen: " . $db_conn->error;
            }

            $db_conn->close();
        } else {
            echo "Det oppsto en feil under opplasting av filen.";
        }
    }
}
?>
