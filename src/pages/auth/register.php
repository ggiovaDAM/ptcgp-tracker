<?php
$result = -1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_confirm = $_POST["password-confirm"];

    require_once __DIR__ . "/../../backend/pdo/db.php";
    $db = connectToDatabase( __DIR__ . "/../../backend/pdo/server_config.xml");

    if ($password != $password_confirm) {
        $result = 1;
    } else {
        $sqlInsert = "INSERT INTO users (email, password, is_admin) VALUES (:email, :password, 0)";
        $sqlSelect = "SELECT * FROM users WHERE email = :email";
        
        try {
            $db->beginTransaction();

            $prepared = $db->prepare($sqlSelect);
            $prepared->execute([
                ":email" => $email
            ]);

            if ($prepared->rowCount() > 0) {
                $result = 2;
            } else {
                $prepared = $db->prepare($sqlInsert);
                $prepared->execute([
                    ":email" => $email,
                    ":password" => password_hash($password, PASSWORD_DEFAULT)
                ]);

                $db->commit();

                $result = 0;

                $_SESSION["LOGIN_ID"] = $db->lastInsertId();
                $_SESSION["LOGIN_EMAIL"] = $email;
    
                // todo: enviar al index
                header("Location: index.php");
                exit();
            }
        } catch (PDOException $e) {
            if ($db->inTransaction()) $db->rollBack();

            die("Error catastrófico " . $e->getMessage());
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>
    <main>
        <h1>Register</h1>
        <form id="register-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" target="_self">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Email" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" required>
            <label for="password-confirm">Confirm Password</label>
            <input type="password" name="password-confirm" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
            <?php if ($result == 1) { ?>
                <p>Passwords do not match</p>
            <?php } ?>
            <?php if ($result == 2) { ?>
                <p>User already exists</p>
            <?php } ?>
        </form>
    </main>
</body>
</html>