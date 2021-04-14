<?php
    session_start();

?>

<html>

    <head>
        <title>MyHealth5 Portal</title>
        <link href = 'MyHealth.css' rel = 'stylesheet'>
    </head>
    <body>
        <div class="pageContainer" id="patientPortalContainer">
            <h2>Patient Portal</h2>
            <?php
                echo "You are logged into the patient portal as:";
                echo $_SESSION["email"];
            ?>
        </div>
    </body>
</html>