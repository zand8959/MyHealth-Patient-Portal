<?php
    session_start();

?>

<html>

    <head>
        <title>MyHealth5 Portal</title>
        <link href = 'MyHealth.css' rel = 'stylesheet'>
    </head>
    <body>
        <div class="pageContainer" id="doctorPortalContainer">
            <h2>Doctor Portal</h2>
            <?php
                echo "You are logged into the doctor portal as:";
                echo $_SESSION["email"];
            ?>
        </div>
    </body>
</html>