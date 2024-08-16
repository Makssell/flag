<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminside - Last opp flagg</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="file"],
        select {
            width: 100%;
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <h2>Last opp et nytt flagg</h2>
    <label for="flagg_bilde">Velg flaggfil:</label>
    <input type="file" name="flagg_bilde" id="flagg_bilde" required>
    <label for="land_id">Velg land:</label>
    <select name="land_id" id="land_id" required>
        <option value="">Velg land</option>
        <?php
        // Database tilkobling
        $db_host = 'localhost';  // Erstatt med ditt databasevert
        $db_navn = 'racket'; // Erstatt med ditt databasenavn
        $db_bruker = 'root'; // Erstatt med ditt databasebrukernavn
        $db_passord = ''; // Erstatt med ditt databasepassord

        $db_conn = new mysqli($db_host, $db_bruker, $db_passord, $db_navn);

        if ($db_conn->connect_error) {
            die("Connection failed: " . $db_conn->connect_error);
        }

        // Hent alle land fra databasen
        $sql = "SELECT land_id, land_navn FROM land";
        $resultat = $db_conn->query($sql);

        if ($resultat->num_rows > 0) {
            while ($rad = $resultat->fetch_assoc()) {
                echo '<option value="' . $rad['land_id'] . '">' . $rad['land_navn'] . '</option>';
            }
        }

        // Lukk database tilkobling
        $db_conn->close();
        ?>
    </select>
    <input type="submit" value="Last opp flagg" name="submit">
</form>

</body>
</html>
