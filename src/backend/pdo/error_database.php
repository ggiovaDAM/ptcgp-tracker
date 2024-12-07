<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Database Connection Error!</title>
</head>
<body>
    <?php
        session_start();
        echo $_SESSION["DATABASE_CONNECTION_ERROR"];
        session_destroy();
    ?>
</body>
</html>